<?php

class Fraktguiden_Multi_Packaging implements iPackaging {

  public function __construct() {

    include_once( __DIR__ . '/../vendor/php-laff/laff-pack.php' );

    $this->packer             = new LAFFPack();
    $this->containers_to_ship = array();
    $this->popped_boxes       = array();
  }

  /**
   * @param $product_boxes Array product boxes dimensions. Each 'box' contains an array of { length, width, height, weight }
   */
  public function pack( $product_boxes ) {

    // Calculate total weight of boxes.
    $total_weight = 0;
    foreach ( $product_boxes as $box ) {
      $total_weight += $box['weight'];
    }

    // Pack the boxes in a container.
    $this->packer->pack( $product_boxes );
    $container_size = $this->packer->get_container_dimensions();
    // Get the sizes in cm.
    $container = array(
        'weight_in_grams' => $this->get_weight( $total_weight ),
        'length'          => $this->get_dimension( $container_size['length'] ),
        'width'           => $this->get_dimension( $container_size['width'] ),
        'height'          => $this->get_dimension( $container_size['height'] ),
    );

    // Check if the container exceeds max values.
    if ( $this->exceeds_max_values( $container ) ) {
      // Move one item to the popped cache and run again.
      $this->popped_boxes[] = array_pop( $product_boxes );
      $this->pack( $product_boxes );
    } else {
      // The container is valid, save it to the cache.
      $this->containers_to_ship[] = $container;

      // Check the remaining boxes.
      if ( count( $this->popped_boxes ) > 0 ) {
        $popped = $this->popped_boxes;
        unset( $this->popped_boxes );
        $this->popped_boxes = array();
        $this->pack( $popped );
      }
    }
  }

  public function create_weight_dimensions_param( $standard_params ) {
    return $standard_params;
  }

  /**
   * Checks if the given package size qualifies for package splitting.
   *
   * @param $container_size
   * @return bool
   */
  private function exceeds_max_values( $container_size ) {

    $weight = $container_size['weight_in_grams'];

    // Create L x W x H array by removing the weight element.
    $dimensions = $container_size;
    unset( $dimensions['weight_in_grams'] );
    // Reverse sort the dimensions/L x W x H array.
    arsort( $dimensions );
    // The longest side should now be on the first element.
    $longest_side = current( $dimensions );
    // Store the other sides.
    $side2 = next( $dimensions );
    $side3 = next( $dimensions );

    // Add the longest side and add the other sides multiplied by 2.
    $longest_plus_circumference = $longest_side + ( $side2 * 2 ) + ( $side3 * 2 );

    if ( $weight > 35000 ) {
      return true;
    }

    if ( $longest_side > 240 ) {
      return true;
    }

    if ( $longest_plus_circumference > 360 ) {
      return true;
    }

    return false;
  }


}
