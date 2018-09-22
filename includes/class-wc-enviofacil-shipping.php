<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

class WC_EnvioFacil_Shipping extends WC_Shipping_Method {
	private $_origin_postcode;
	private $_show_estimated_delivery;
	private $_pac_enabled;
	private $_pac_title;
	private $_sedex_enabled;
	private $_sedex_title;
	private $_additional_time;
	public $fee;

	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );
		$this->id                 = 'enviofacil';
		$this->method_title       = __( 'Envio Fácil', WC_ENVIOFACIL_DOMAIN );
		$this->method_description = __( 'Adds shipping method PAC and SEDEX via Envio Fácil to WooCommerce.', WC_ENVIOFACIL_DOMAIN );
		$this->supports           = array( 'shipping-zones', 'instance-settings' );
		$this->title              = __( 'Envio Fácil', WC_ENVIOFACIL_DOMAIN );

		$this->init_form_fields();

		$this->_origin_postcode         = $this->get_option( 'origin_postcode' );
		$this->_show_estimated_delivery = $this->get_option( 'show_estimated_delivery' );
		$this->_pac_enabled             = $this->get_option( 'pac_enabled' );
		$this->_pac_title               = $this->get_option( 'pac_title' );
		$this->_sedex_enabled           = $this->get_option( 'sedex_enabled' );
		$this->_sedex_title             = $this->get_option( 'sedex_title' );
		$this->_additional_time         = $this->get_option( 'additional_time' );
		$this->fee                      = $this->get_option( 'fee' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * {@inheritDoc}
	 * @see WC_Settings_API::init_form_fields()
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'origin_postcode' => array(
				'title'       => __( 'Origin postcode', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'It is the postcode from the sender.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => '00000-000',
			),
			'show_estimated_delivery' => array(
				'title'       => __( 'Estimated delivery.', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'label'       => __( 'Show estimated delivery', WC_ENVIOFACIL_DOMAIN ),
				'description' => __( 'Display the estimated delivery in business day.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'pac_enabled' => array(
				'title'       => __( 'Enable PAC', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Enable PAC as a shipping method.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'pac_title' => array(
				'title'       => __( 'PAC title', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Title to be displayed on site.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'PAC',
			),
			'sedex_enabled' => array(
				'title'       => __( 'Enable SEDEX', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Enable SEDEX as a shipping method.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'sedex_title' => array(
				'title'       => __( 'Título do SEDEX', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Title to be displayed on site', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'SEDEX',
			),
			'additional_time'  => array(
				'title'       => __( 'Additional days', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'decimal',
				'description' => __( 'Additional business days to the estimated delivery.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
			),
			'fee'                => array(
				'title'       => __( 'Handling fee', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'price',
				'description' => __( 'An amount to add to the cost of the shipping method as a fee for handling it.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'placeholder' => '0.00',
				'default'     => '',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 * @see WC_Shipping_Method::calculate_shipping()
	 */
	public function calculate_shipping( $package = array() ) {
		if ( $this->_origin_postcode === '00000-000' || $this->_origin_postcode === '' ) {
			return;
		}

		$_package = new WC_EnvioFacil_Package( $package );
		$package_data = $_package->get_package_data();
		
		$ws = new WC_EnvioFacil_WebService();
		$ws->set_cep_from( $this->_origin_postcode );
		$ws->set_cep_to( $package['destination']['postcode'] );
		$ws->set_width( $package_data['width'] );
		$ws->set_height( $package_data['height'] );
		$ws->set_length( $package_data['length'] );
		$ws->set_weight( $package_data['weight'] );
		
		$enviofacil_rates = $ws->get_enviofacil_rates();

		foreach ( $enviofacil_rates as $enviofacil_rate ) {
			if ( ! $this->is_shipping_method_enabled( $enviofacil_rate->get_service_type() ) ) {
				continue;
			}

			$meta_delivery = array();
			if ( $this->_show_estimated_delivery === 'yes' ) {
				$meta_delivery = array(
					'_delivery_forecast' => intval( $enviofacil_rate->get_estimated_delivery() ) + intval( $this->_additional_time ),
				);
			}

			$cost = $enviofacil_rate->get_total_value();

			$rate = array(
				'id'       => $this->id . $enviofacil_rate->get_service_type() . $this->instance_id,
				'label'     => $this->get_service_type_site_name( $enviofacil_rate->get_service_type() ),
				'cost'      => floatval( $cost ) + floatval( $this->get_fee( $this->fee, $cost ) ),
				'meta_data' => $meta_delivery
			);
			
			$this->add_rate( $rate );
		}
	}

	/**
	 * Gets the name of the shipping method that is going to be displayed on site
	 *
	 * @param string $service_type
	 *
	 * @return string
	 */
	public function get_service_type_site_name( $service_type = null ) {
		if ( $service_type === WC_EnvioFacil_Shipping_Method::PAC ) {
			return $this->_pac_title;
		}
		else if ( $service_type === WC_EnvioFacil_Shipping_Method::SEDEX ) {
			return $this->_sedex_title;
		}
		else {
			return '';
		}
	}

	public function is_shipping_method_enabled( $service_type = null ) {
		if ( $service_type === WC_EnvioFacil_Shipping_Method::PAC && $this->_pac_enabled === 'yes' ) {
			return true;
		}

		if ( $service_type === WC_EnvioFacil_Shipping_Method::SEDEX && $this->_sedex_enabled === 'yes' ) {
			return true;
		}

		return false;
	}
}
