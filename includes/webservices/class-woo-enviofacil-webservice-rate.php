<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WOO_EnvioFacil_WebService_Rate {

	private $service_type;
	private $total_value;
	private $estimated_delivery;

	/**
	 * WOO_EnvioFacil_WebService_Rate constructor.
	 *
	 * @param string $service_type
	 * @param float $total_value
	 * @param int $estimated_delivery
	 */
	public function __construct( $service_type = '', $total_value = 0.00, $estimated_delivery = 0 ) {
		$this->set_service_type( $service_type );
		$this->set_total_value( $total_value );
		$this->set_estimated_delivery( $estimated_delivery );
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
	 * @param string $service_type
	 */
	private function set_service_type( $service_type = '' ) {
		$this->service_type = $service_type;
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
	 * @param float $total_value
	 */
	private function set_total_value( $total_value = 0.00 ) {
		$this->total_value = $total_value;
	}

	/**
	 * Gets estimated delivery in days
	 *
	 * @return int
	 */
	public function get_estimated_delivery() {
		return $this->estimated_delivery;
	}

	/**
	 * @param int $estimated_delivery
	 */
	private function set_estimated_delivery( $estimated_delivery = 0 ) {
		$this->estimated_delivery = $estimated_delivery;
	}
}