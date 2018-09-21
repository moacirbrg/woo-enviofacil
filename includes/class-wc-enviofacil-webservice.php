<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_EnvioFacil_WebService {
	private $_webservice_url = 'https://pagseguro.uol.com.br/para-seu-negocio/online/envio-facil';
	private $_cep_from = '';
	private $_cep_to = '';
	private $_height = 0;
	private $_length = 0;
	private $_width = 0;
	private $_weight = 0;

	public function __construct() { }

	private function get_webservice_weigth( $weight = 0 ) {
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
				return $weight;
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

	private function create_params() {
		$params = [
			'cepFrom' => $this->_cep_from,
			'cepTo'   => $this->_cep_to,
			'width'   => $this->_width,
			'height'  => $this->_height,
			'length'  => $this->_length,
			'weight'  => $this->get_webservice_weigth( $this->_weight ),
			'serviceType' => ''
		];

		return $params;
	}

	/**
	 * Get delivery rates from EnvioFácil WebService
	 * @return WC_EnvioFacil_Rate[]
	 */
	public function get_enviofacil_rates() {
		$params = $this->create_params();

		$req = curl_init();
		curl_setopt( $req, CURLOPT_URL, $this->_webservice_url );
		curl_setopt( $req, CURLOPT_POST, true );
		curl_setopt( $req, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
		curl_setopt( $req, CURLOPT_POSTFIELDS, json_encode( $params ) );
		curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
		
		$res = curl_exec( $req );
		curl_close( $req );
		$data_array = json_decode( $res );

		$rates = array();
		
		foreach ( $data_array as $data_obj ) {
			$rate = new WC_EnvioFacil_Rate( $data_obj );
			array_push( $rates, $rate );
		}

		return $rates;
	}
}