<?php

class Fraktguiden_Service {
  public $key;
  public $id;
  public $service_data;

  public $custom_price_id;
  public $custom_price;
  public $free_shipping_id;
  public $free_shipping;
  public $free_shipping_threshold_id;
  public $free_shipping_threshold;

  function __construct( $key, $service_data, $service_options ) {

    $this->key          = $key;
    $this->id           = $service_options['field_key'] . '_' . $key;
    $this->service_data = $service_data;
    $selected           = $service_options['selected'];
    $this->enabled      = ! empty( $selected ) ? in_array( $key, $selected ) : false;

    // Custom prices
    $this->custom_price_id = "{$service_options['field_key']}_custom_prices[$key]";
    $this->custom_price    = esc_html( @$service_options['custom_prices'][$key] );

    // Free shipping
    $this->free_shipping_id = "{$service_options['field_key']}_free_shipping_checks[$key]";
    $this->free_shipping    = esc_html( @$service_options['free_shipping_checks'][$key] );

    // Shipping threshold
    $this->free_shipping_threshold_id = "{$service_options['field_key']}_free_shipping_thresholds[$key]";
    $this->free_shipping_threshold    = esc_html( @$service_options['free_shipping_thresholds'][$key] );

  }
}