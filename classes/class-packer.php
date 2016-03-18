<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Class Fraktguiden_Packaging
 *
 * Packs products into 'containers'
 */
class Fraktguiden_Packer {

  private $containers_to_ship;
  private $popped_boxes_cache;

  public function __construct() {

    include_once( __DIR__ . '/../vendor/php-laff/laff-pack.php' );

    $this->laff_pack   = new LAFFPack();
    $this->dim_unit    = get_option( 'woocommerce_dimension_unit' );
    $this->weight_unit = get_option( 'woocommerce_weight_unit' );

    $this->containers_to_ship = array();
    $this->popped_boxes_cache = array();
  }

  /**
   * Pack product box(es) into container/s
   * @recursive
   *
   * @param array $product_boxes Array product boxes dimensions. Each 'box' contains an array of { length, width, height, weight }
   * @param boolean $multi_pack
   */
  public function pack( $product_boxes, $multi_pack = false ) {

    // Calculate total weight of boxes.
    $total_weight = 0;
    foreach ( $product_boxes as $box ) {
      $total_weight += $box['weight'];
    }

    // Pack the boxes in a container.
    $this->laff_pack->pack( $product_boxes );
    $container_size = $this->laff_pack->get_container_dimensions();

    // Get the sizes in cm.
    $container = array(
        'weight_in_grams' => $this->get_weight( $total_weight ),
        'length'          => $this->get_dimension( $container_size['length'] ),
        'width'           => $this->get_dimension( $container_size['width'] ),
        'height'          => $this->get_dimension( $container_size['height'] ),
    );

    if ( $multi_pack ) {
      // Check if the container exceeds max values.
      // Note: This only works for SERVICEPAKKE.
      if ( $this->exceeds_max_package_values( $container ) ) {
        // Move one item to the popped cache and run again.
        $this->popped_boxes_cache[] = array_pop( $product_boxes );
        $this->pack( $product_boxes, true );
      }
      else {
        // The container size is within max values, save it to the cache.
        $this->containers_to_ship[] = $container;
        // Check the remaining boxes.
        if ( count( $this->popped_boxes_cache ) > 0 ) {
          $popped = $this->popped_boxes_cache;
          unset( $this->popped_boxes_cache );
          $this->popped_boxes_cache = array();
          $this->pack( $popped, true );
        }
      }
    }
    else {
      $this->containers_to_ship[] = $container;
    }
  }

  /**
   * Creates an array of dimension/s and weight/s for each container.
   *
   * @return array
   */
  public function create_coli_params() {
    $params = array();
    for ( $i = 0; $i < count( $this->containers_to_ship ); $i++ ) {
      $params['length' . $i]        = $this->containers_to_ship[$i]['length'];
      $params['width' . $i]         = $this->containers_to_ship[$i]['width'];
      $params['height' . $i]        = $this->containers_to_ship[$i]['height'];
      $params['weightInGrams' . $i] = $this->containers_to_ship[$i]['weight_in_grams'];
    }
    return $params;
  }

  public function validate( $product_boxes ) {
    foreach ( $product_boxes as $box ) {
      if ( $this->get_weight( $box['weight'] ) > 35000 ) {
        return false;
      }
    }
    return true;
  }

  /**
   * Checks if the given package size qualifies for package splitting.
   *
   * @param array $container_size Array with container width/height/length/weight.
   * @return bool
   */
  public function exceeds_max_package_values( $container_size ) {

    $weight = $container_size['weight_in_grams'];
    if ( $weight > 35000 ) {
      return true;
    }

    // Create L x W x H array by removing the weight elements.
    $dimensions = $container_size;
    unset( $dimensions['weight_in_grams'] );
    unset( $dimensions['weight'] );

    // Reverse sort the dimensions/L x W x H array.
    arsort( $dimensions );
    // The longest side should now be on the first element.
    $longest_side = current( $dimensions );
    if ( $longest_side > 240 ) {
      return true;
    }

    // Store the other sides.
    $side2 = next( $dimensions );
    $side3 = next( $dimensions );

    // Add the longest side and add the other sides multiplied by 2.
    $longest_plus_circumference = $longest_side + ( $side2 * 2 ) + ( $side3 * 2 );

    if ( $longest_plus_circumference > 360 ) {
      return true;
    }

    return false;
  }

  /**
   * Return weight in grams.
   *
   * @param float $weight
   * @return float
   */
  public function get_weight( $weight ) {
    switch ( $this->weight_unit ) {

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
  public function get_dimension( $dimension ) {

    switch ( $this->dim_unit ) {

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
  public function get_volume( $dimension ) {
    switch ( $this->dim_unit ) {

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
