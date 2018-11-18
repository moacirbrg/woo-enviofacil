<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WOO_EnvioFacil_Package {
	
	private $package = array();
	
	public function __construct( $package = array() ) {
		$this->package = $package;
	}
	
	/**
	 * Calculate the weight and dimensions from the package.
	 * 
	 * @return array
	 */
	public function get_package_data() {
		$height = array();
		$length = array();
		$width  = array();
		$weight = array();
		
		foreach ( $this->package['contents'] as $item_id => $values ) {
			/** @var WOO_Product $product */
			$product = $values['data'];
			/** @var int $qty */
			$qty     = $values['quantity'];
			
			if ( $qty > 0 && $product->needs_shipping() ) {
				array_push( $height, wc_get_dimension( (float) $product->get_height() * $qty, 'cm' ) );
				array_push( $length, wc_get_dimension( (float) $product->get_length(), 'cm' ) );
				array_push( $width, wc_get_dimension( (float) $product->get_width(), 'cm' ) );
				array_push( $weight, wc_get_weight( (float) $product->get_weight() * $qty, 'kg' ) );
			}
		}
		
		return array(
			'height' => array_sum( $height ),
			'length' => max( $length ),
			'width'  => max( $width ),
			'weight' => array_sum( $weight ),
		);
	}
}