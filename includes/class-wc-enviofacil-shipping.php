<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

class WC_EnvioFacil_Shipping extends WC_Shipping_Method {
	
	public function __construct( $instance_id = 0 ) {
		$this->instance_id        = absint( $instance_id );
		$this->id                 = 'enviofacil';
		$this->method_title       = __( 'Envio Fácil', WC_ENVIOFACIL_DOMAIN );
		$this->method_description = __( 'Métodos PAC e SEDEX do Envio Fácil.', WC_ENVIOFACIL_DOMAIN );
		$this->supports           = array( 'shipping-zones', 'instance-settings' );
		
		$this->init();
		
		$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
		$this->title = isset( $this->settings['title'] )
			? $this->settings['title']
			: __( $this->method_title, WC_ENVIOFACIL_DOMAIN );
	}

	public function init() {
		$this->init_form_fields();
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$this->form_fields = array( 
			'enabled' => array( 
				'title'       => __( 'Habilitado', WC_ENVIOFACIL_DOMAIN ), 
				'type'        => 'checkbox', 
				'description' => __( 'Habilitar esta entrega.', WC_ENVIOFACIL_DOMAIN ), 
				'default'     => 'yes' ), 
			'title'   => array( 
				'title'       => __( 'Título', WC_ENVIOFACIL_DOMAIN ), 
				'type'        => 'text', 
				'description' => __( 'Título para mostrar no site', WC_ENVIOFACIL_DOMAIN ), 
				'default'     => __( $this->method_title, WC_ENVIOFACIL_DOMAIN ) ) );
	}

	public function calculate_shipping( $package = array() ) {
		$_package = new WC_EnvioFacil_Package( $package );
		$package_data = $_package->get_package_data();
		
		$ws = new WC_EnvioFacil_WebService();
		$ws->set_cep_from( '91910-400' );
		$ws->set_cep_to( $package['destination']['postcode'] );
		$ws->set_width( $package_data['width'] );
		$ws->set_height( $package_data['height'] );
		$ws->set_length( $package_data['length'] );
		$ws->set_weight( $package_data['weight'] );
		
		$enviofacil_rates = $ws->get_enviofacil_rates();
		
		foreach ( $enviofacil_rates as $enviofacil_rate ) {
			$rate = array(
				'id'       => $this->id . $enviofacil_rate->get_service_type() . $this->instance_id,
				'label'    => $enviofacil_rate->get_service_type(),
				'cost'     => $enviofacil_rate->get_total_value(),
				'calc_tax' => 'per_item' );
			
			$this->add_rate( $rate );
		}
	}
}
