<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WOO_EnvioFacil_Correios_WebService extends WOO_EnvioFacil_WebService {

	const PAC_SERVICE_CODE   = '04510';
	const SEDEX_SERVICE_CODE = '04014';

	private $_webservice_url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?wsdl';

	private function create_params() {
		$params = [
			'nCdEmpresa'          => '',
			'sDsSenha'            => '',
			'nCdServico'          => self::PAC_SERVICE_CODE . ',' . self::SEDEX_SERVICE_CODE,
			'sCepOrigem'          => $this->normalize_cep( $this->_cep_from ),
			'sCepDestino'         => $this->normalize_cep( $this->_cep_to ),
			'nVlPeso'             => (string) $this->get_webservice_weigth( $this->_weight ),
			'nCdFormato'          => 1, // 1 = Caixa
			'nVlComprimento'      => $this->_length,
			'nVlAltura'           => $this->_height,
			'nVlLargura'          => $this->_width,
			'nVlDiametro'         => 0,
			'sCdMaoPropria'       => 'N',
			'nVlValorDeclarado'   => 0,
			'sCdAvisoRecebimento' => 'N'
		];

		return $params;
	}

	/**
	 * Removes spaces and dashes from postalcode
	 *
	 * @param string $cep
	 *
	 * @return string
	 */
	private function normalize_cep( $cep = '' ) {
		$nospace = trim( $cep );
		$nodash  = str_replace( '-', '', $nospace );
		return $nodash;
	}

	protected function create_webservice_rate( $ws_raw_data ) {
		if ( ! isset( $ws_raw_data ) && ! isset( $ws_raw_data->Codigo ) ) {
			throw new InvalidArgumentException();
		}

		$service_type = '';
		if ( $ws_raw_data->Codigo == 4510 ) {
			$service_type = WOO_EnvioFacil_Shipping_Method::PAC;
		}
		else if ( $ws_raw_data->Codigo == 4014 ) {
			$service_type = WOO_EnvioFacil_Shipping_Method::SEDEX;
		}

		return new WOO_EnvioFacil_WebService_Rate(
			$service_type,
			floatval( $ws_raw_data->Valor ),
			intval( $ws_raw_data->PrazoEntrega ) );
	}

	/**
	 * @return array|WOO_EnvioFacil_WebService_Rate[]
	 */
	public function get_webservice_rates() {
		$rates = array();

		try {
			$client = new SoapClient( $this->_webservice_url, array(
				'cache_wsdl' => WSDL_CACHE_NONE,
				'encoding'   => 'UTF-8'
			) );

			$response = $client->__soapCall(
				'CalcPrecoPrazo',
				array( 'CalcPrecoPrazo' => $this->create_params() ),
				null,
				null
			);
		}
		catch(Exception $e) {
			return $rates;
		}

		if ( ! isset( $response->CalcPrecoPrazoResult ) &&
		     ! isset( $response->CalcPrecoPrazoResult->Servicos ) &&
		     ! is_array( $response->CalcPrecoPrazoResult->Servicos->cServico) ) {
			return $rates;
		}

		$ws_raw_data_array = $response->CalcPrecoPrazoResult->Servicos->cServico;
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