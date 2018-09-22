<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_EnvioFacil_Rate {
    private $service_type;
    private $total_value;
    private $estimated_delivery;

    public function __construct( $obj ) {
    	if ( ! isset( $obj->serviceType ) ) {
		    throw new InvalidArgumentException();
	    }

        if ( $obj->serviceType == 'PAC' ) {
            $this->service_type = WC_EnvioFacil_Shipping_Method::PAC;
        }
        else if ( $obj->serviceType == 'SEDEX' ) {
            $this->service_type = WC_EnvioFacil_Shipping_Method::SEDEX;
        }

        $this->total_value = $obj->totalValue;
        $this->estimated_delivery = $obj->estimatedDelivery;
    }
    
    /**
     * Gets 'PAC' or 'SEDEX' to notify caller about the shipping method of this rate
     * 
     * @return string
     */
    public function get_service_type() {
    	return $this->service_type;
    }
    
    /**
     * Gets the total price of this shipping method
     * 
     * @return float
     */
    public function get_total_value() {
    	return $this->total_value;
    }
    
    /**
     * Gets estimated delivery in days
     * 
     * @return int
     */
    public function get_estimated_delivery() {
    	return $this->estimated_delivery;
    }
}