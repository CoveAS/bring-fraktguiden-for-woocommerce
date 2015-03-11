<?php

class Fraktguiden_Simple_Packaging implements iPackaging {

  public function __construct() {

    include_once( __DIR__ . '/../vendor/php-laff/laff-pack.php' );

    $this->packer             = new LAFFPack();
    $this->containers_to_ship = array();
    $this->popped_boxes       = array();
  }

  /**
   * @param $product_boxes Array
   * product boxes dimensions. Each 'box' contains an array of { length, width, height, weight }
   */
  public function pack( $product_boxes ) {}

  public function create_weight_dimensions_param( $standard_params ) {
    return $standard_params;
  }

}
