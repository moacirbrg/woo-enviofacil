<?php

class WOO_EnvioFacil {

    public static function init() {
        self::wc_includes();
        self::includes();

        add_filter( 'woocommerce_shipping_methods', array( __CLASS__, 'include_methods' ) );
    }

    public static function wc_includes() {
        $WOO_INCLUDES = dirname( WOO_ENVIOFACIL_PLUGIN_FILE ) . '/../woocommerce/includes';
        include_once $WOO_INCLUDES . '/abstracts/abstract-wc-shipping-method.php';
    }

    public static function includes() {
        include_once dirname( __FILE__ ) . '/enums/class-woo-enviofacil-shipping-method.php';
        include_once dirname( __FILE__ ) . '/class-woo-enviofacil-package.php';
        include_once dirname( __FILE__ ) . '/webservices/class-woo-enviofacil-webservice-rate.php';
        include_once dirname( __FILE__ ) . '/webservices/class-woo-enviofacil-webservice.php';
        include_once dirname( __FILE__ ) . '/webservices/class-woo-enviofacil-uol-webservice.php';
        include_once dirname( __FILE__ ) . '/webservices/class-woo-enviofacil-correios-webservice.php';
        include_once dirname( __FILE__ ) . '/class-woo-enviofacil-shipping.php';
    }

    public static function include_methods( $methods ) {
        $methods['enviofacil'] = 'WOO_EnvioFacil_Shipping';
        return $methods;
    }
}