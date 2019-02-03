<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WOO_EnvioFacil_UOL_WebService extends WOO_EnvioFacil_WebService {

	private $_webservice_url = 'https://pagseguro.uol.com.br/para-seu-negocio/online/envio-facil';
	private $_min_width = 11;
	private $_min_height = 2;
	private $_min_length = 16;

	private function create_params() {
		$params = [
			'cepFrom' => $this->_cep_from,
			'cepTo'   => $this->_cep_to,
			'width'   => ceil( ( $this->_width > $this->_min_width ) ? $this->_width : $this->_min_width ),
			'height'  => ceil( ( $this->_height > $this->_min_height ) ? $this->_height : $this->_min_height ),
			'length'  => ceil( ( $this->_length > $this->_min_length ) ? $this->_length : $this->_min_length ),
			'weight'  => $this->get_webservice_weigth( $this->_weight ),
			'serviceType' => ''
		];

		return $params;
	}

	protected function create_webservice_rate( $ws_raw_data ) {
		if ( ! isset( $ws_raw_data->serviceType ) ) {
			throw new InvalidArgumentException();
		}

		$service_type = '';
		if ( $ws_raw_data->serviceType == 'PAC' ) {
			$service_type = WOO_EnvioFacil_Shipping_Method::PAC;
		}
		else if ( $ws_raw_data->serviceType == 'SEDEX' ) {
			$service_type = WOO_EnvioFacil_Shipping_Method::SEDEX;
		}

		return new WOO_EnvioFacil_WebService_Rate(
			$service_type,
			$ws_raw_data->totalValue,
			$ws_raw_data->estimatedDelivery );
	}

	/**
	 * @return array|WOO_EnvioFacil_WebService_Rate[]
	 */
	public function get_webservice_rates() {
		$params = $this->create_params();

		$req = curl_init();
		curl_setopt( $req, CURLOPT_URL, $this->_webservice_url );
		curl_setopt( $req, CURLOPT_POST, true );
		curl_setopt( $req, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
		curl_setopt( $req, CURLOPT_POSTFIELDS, json_encode( $params ) );
		curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );

		$res = curl_exec( $req );
		curl_close( $req );
		$ws_raw_data_array = json_decode( $res );

		$rates = array();

		if ( ! is_array( $ws_raw_data_array ) )  {
			return $rates;
		}

		foreach ( $ws_raw_data_array as $ws_raw_data ) {
			try {
				$rate = $this->create_webservice_rate( $ws_raw_data );
				array_push( $rates, $rate );
			}
			catch(InvalidArgumentException $e) {}
		}

		return $rates;
	}
}
