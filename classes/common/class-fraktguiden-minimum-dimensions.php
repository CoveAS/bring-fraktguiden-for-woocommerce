<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use BringFraktguiden\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Fraktguiden_Minimum_Dimensions class
 */
class Fraktguiden_Minimum_Dimensions {

	/**
	 * Setup
	 */
	public static function setup() {
		add_filter( 'bring_fraktguiden_minimum_dimensions', __CLASS__ . '::minimum_dimensions' );
	}

	/**
	 * Filter: Minumum Dimensions
	 *
	 * @param array $dimensions Dimensions.
	 *
	 * @return array
	 */
	public static function minimum_dimensions( $dimensions ) {
		// Check the weight.
		$minimum_weight = Settings::instance()->minimum_weight->value * 1000;

		if ( $minimum_weight > $dimensions['weight_in_grams'] ) {
			$dimensions['weight_in_grams'] = $minimum_weight;
		}

		foreach ( [ 'length', 'width', 'height' ] as $key ) {
			$minimum = Settings::instance()->{'minimum_' . $key}->value;

			if ( $minimum > $dimensions[ $key ] ) {
				$dimensions[ $key ] = $minimum;
			}
		}

		return $dimensions;
	}
}
