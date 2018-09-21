<?php

class WC_EnvioFacil {

    public static function init() {
        self::wc_includes();
        self::includes();

        if ( is_admin() ) {
            self::admin_includes();
        }

        add_filter( 'woocommerce_shipping_methods', array( __CLASS__, 'include_methods' ) );
    }

    public static function wc_includes() {
        $WC_INCLUDES = dirname( WC_ENVIOFACIL_PLUGIN_FILE ) . '/../woocommerce/includes';
        include_once $WC_INCLUDES . '/abstracts/abstract-wc-shipping-method.php';
    }

    public static function includes() {
        include_once dirname( __FILE__ ) . '/enums/class-wc-enviofacil-shipping-method.php';
        include_once dirname( __FILE__ ) . '/class-wc-enviofacil-package.php';
        include_once dirname( __FILE__ ) . '/class-wc-enviofacil-rate.php';
        include_once dirname( __FILE__ ) . '/class-wc-enviofacil-webservice.php';
        include_once dirname( __FILE__ ) . '/class-wc-enviofacil-shipping.php';
    }

    public static function admin_includes() {
        
    }

    public static function include_methods( $methods ) {
        $methods['enviofacil'] = 'WC_EnvioFacil_Shipping';
        return $methods;
    }
}