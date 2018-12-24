<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class WOO_EnvioFacil_WebService {

	protected $_cep_from = '';
	protected $_cep_to = '';
	protected $_height = 0;
	protected $_length = 0;
	protected $_width = 0;
	protected $_weight = 0;

	/**
	 * Gets the best weight according to the available weights of the web service
	 *
	 * @param int $weight Real weight of the package
	 *
	 * @return number
	 */
	protected function get_webservice_weigth( $weight = 0 ) {
		$available_weights = [
			0.3, // Max 0.3kg
			0.5, // Max 0.5kg
			1,   // Max 1kg
			1.5, // Max 1.5kg
			2,   // Max 2kg
			2.5, // Max 2.5kg
			3,   // Max 3kg
			3.5, // Max 3.5kg
			4,   // Max 4kg
			4.5, // Max 4.5kg
			5,   // Max 5kg
			6,   // Max 6kg
			7,   // Max 7kg
			8,   // Max 8kg
			9,   // Max 9kg
			10   // Max 10kg
		];

		foreach ( $available_weights as $available_weight ) {
			if ( $weight <= $available_weight ) {
				return $available_weight;
			}
		}

		return -1;
	}

	public function set_cep_from( $cep_from = '' ) {
		$this->_cep_from = $cep_from;
	}

	public function set_cep_to( $cep_to = '' ) {
		$this->_cep_to = $cep_to;
	}

	public function set_height( $height = 0 ) {
		$this->_height = $height;
	}

	public function set_length( $length = 0 ) {
		$this->_length = $length;
	}

	public function set_width( $width = 0 ) {
		$this->_width = $width;
	}

	public function set_weight( $weight = 0 ) {
		$this->_weight = $weight;
	}

	/**
	 * Create a rate object from webserive raw data
	 *
	 * @param object $ws_raw_data
	 *
	 * @return WOO_EnvioFacil_WebService_Rate
	 */
	protected abstract function create_webservice_rate( $ws_raw_data );

	/**
	 * Gets delivery rates from the web service
	 *
	 * @return WOO_EnvioFacil_WebService_Rate[]
	 */
	public abstract function get_webservice_rates();
}