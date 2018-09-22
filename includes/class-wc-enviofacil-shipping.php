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
		$this->method_description = __( 'Métodos PAC e SEDEX do Envio Fácil.', WC_ENVIOFACIL_DOMAIN );
		$this->supports           = array( 'shipping-zones', 'instance-settings' );
		
		$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
		$this->title = isset( $this->settings['title'] )
			? $this->settings['title']
			: __( $this->method_title, WC_ENVIOFACIL_DOMAIN );

		$this->init_form_fields();

		$this->enabled                  = $this->get_option( 'enabled' );
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
			'enabled' => array( 
				'title'       => __( 'Habilitar o Envio Fácil', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'default'     => 'yes',
			),
			'origin_postcode' => array(
				'title'       => __( 'CEP do remetente', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'É o CEP de origem do pacote', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => __( '00000-000', WC_ENVIOFACIL_DOMAIN ),
			),
			'show_estimated_delivery' => array(
				'title'       => __( 'Estimativa de entrega', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'label'       => __( 'Exibir estimativa de entrega', WC_ENVIOFACIL_DOMAIN ),
				'description' => __( 'Exibe o tempo estimado de entrega em dias úteis.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'pac_enabled' => array(
				'title'       => __( 'Habilitar o PAC', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Habilitar o PAC como método de entrega.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'pac_title' => array(
				'title'       => __( 'Título do PAC', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Como o PAC será chamado no site', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => __( 'PAC', WC_ENVIOFACIL_DOMAIN ),
			),
			'sedex_enabled' => array(
				'title'       => __( 'Habilitar o SEDEX', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'checkbox',
				'description' => __( 'Habilitar o SEDEX como método de entrega.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'sedex_title' => array(
				'title'       => __( 'Título do SEDEX', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Como o SEDEX será chamado no site', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => __( 'SEDEX', WC_ENVIOFACIL_DOMAIN ),
			),
			'additional_time'  => array(
				'title'       => __( 'Dias adicionais', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'decimal',
				'description' => __( 'Dias úteis adicionados à estimativa de entrega.', WC_ENVIOFACIL_DOMAIN ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
			),
			'fee'                => array(
				'title'       => __( 'Taxa de manuseio', WC_ENVIOFACIL_DOMAIN ),
				'type'        => 'price',
				'description' => __( 'Uma quantia que você deseja adicionar ao custo de envio.', WC_ENVIOFACIL_DOMAIN ),
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
