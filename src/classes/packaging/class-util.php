<?php

class Fraktguiden_Util {

  public function __construct() {
    /**/
  }

  /**
   * Return weight in grams.
   *
   * @param float $weight
   * @return float
   */
  public static function get_weight( $weight ) {
    switch ( get_option( 'woocommerce_weight_unit' ) ) {

      case 'g' :
        return $weight;

      case 'kg' :
        return $weight / 0.0010000;

      case 'lbs' :
        return $weight / 0.0022046;

      case 'oz' :
        return $weight / 0.035274;

      /* Unknown weight unit */
      default :
        return false;
    }
  }

  /**
   * Return dimension in centimeters.
   *
   * @param float $dimension
   * @return float
   */
  public static function get_dimension( $dimension ) {

    switch ( get_option( 'woocommerce_dimension_unit' ) ) {

      case 'mm' :
        $dimension = $dimension / 10.000;
        break;
      case 'in' :
        $dimension = $dimension / 0.39370;
        break;
      case 'yd' :
        $dimension = $dimension / 0.010936;
        break;
      case 'cm' :
        $dimension = $dimension;
        break;
      case 'm' :
        $dimension = $dimension / 0.010000;
        break;
      /* Unknown dimension unit */
      default :
        return false;
    }

    if ( 1 > $dimension ) {
      // Minimum 1 cm
      $dimension = 1;
    }

    return $dimension;
  }

  /**
   * Return volume in dm.
   *
   * @param $dimension
   * @return float
   */
  public static function get_volume( $dimension ) {
    switch ( get_option( 'woocommerce_dimension_unit' ) ) {

      case 'mm' :
        return $dimension / 100;

      case 'in' :
        return $dimension * 0.254;

      case 'yd' :
        return $dimension * 9.144;

      case 'cm' :
        return $dimension / 1000;

      case 'm' :
        return $dimension / 10;

      /* Unknown dimension unit */
      default :
        return false;
    }
  }
}