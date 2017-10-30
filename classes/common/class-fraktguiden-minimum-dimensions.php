<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Class Fraktguiden_Minimum_Dimensions
 */
class Fraktguiden_Minimum_Dimensions {

  /**
   * Setup
   */
  static function setup() {
    add_filter( 'bring_fraktguiden_minimum_dimensions', __CLASS__.'::minimum_dimensions' );
  }

  /**
   * Filter: Minumum Dimensions
   * @param  array $dimensions
   * @param  array $package
   * @return array
   */
  static function minimum_dimensions( $dimensions ) {
    // Check the weight
    $minimum_weight = Fraktguiden_Helper::get_option( 'minimum_weight', '0.01' ) * 1000;
    if ( $minimum_weight > $dimensions['weight_in_grams'] ) {
      $dimensions['weight_in_grams'] = $minimum_weight;
    }
    $fields = [
      'length' => 23,
      'width'  => 13,
      'height' => 1,
    ];
    foreach ( $fields as $key => $value ) {
      $minimum = Fraktguiden_Helper::get_option( 'minimum_'. $key, $value );
      if ( $minimum > $dimensions[ $key ] ) {
        $dimensions[ $key ] = $minimum;
      }
    }
    return $dimensions;
  }
}
