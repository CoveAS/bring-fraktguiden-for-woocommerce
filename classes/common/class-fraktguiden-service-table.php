<?php

class Fraktguiden_Service_Table {

  protected $shipping_method;

  function __construct( $shipping_method ) {
    $this->shipping_method = $shipping_method;
  }

  /**
   * Validate the service table field
   * @param  string $key
   * @param  mixed $value
   * @return array
   */
  public function validate_services_table_field( $key, $value = null ) {
    if ( isset( $value ) ) {
      return $value;
    }
    $sanitized_services = [];
    $field_key = $this->shipping_method->get_field_key( 'services' );
    if ( ! isset( $_POST[ $field_key ] ) ) {
      return $sanitized_services;
    }
    foreach ( $_POST[ $field_key ] as $service ) {
      if ( preg_match( '/^[A-Za-z_\-]+$/', $service ) ) {
        $sanitized_services[] = $service;
      }
    }
    return $sanitized_services;
  }

  /**
   * Process services field
   * @param  string|null $instance_key
   */
  public function process_services_field( $instance_key ) {

    $field_key = $this->shipping_method->get_field_key( 'services' );
    // @TODO: Use instance key to have per-zone settings

    // Process services table
    $services_custom_prices_field = $field_key . '_custom_prices';
    $custom_prices                = [ ];
    if ( isset( $_POST[$field_key] ) ) {
      $checked_services = $_POST[$field_key];
      foreach ( $checked_services as $key => $service ) {

        if ( isset( $_POST[$services_custom_prices_field][$service] ) ) {
          $custom_prices[$service] = $_POST[$services_custom_prices_field][$service];
        }
      }
    }

    update_option( $services_custom_prices_field, $custom_prices );

    // Process services table
    $services  = Fraktguiden_Helper::get_services_data();
    $field_key = $field_key;
    $vars      = [
        'custom_prices',
        'free_shipping_checks',
        'free_shipping_thresholds',
    ];
    $options = [];
    // Only process options for enabled services
    foreach ( $services as $group => $service_group ) {
      foreach ( $service_group['services'] as $key => $service ) {
        foreach ( $vars as $var ) {
          $data_key = "{$field_key}_{$var}";
          if ( ! isset( $options[$data_key] ) ) {
            $options[$data_key] = [];
          }
          if ( isset( $_POST[$data_key][$key] ) ) {
            $options[$data_key][$key] = $_POST[$data_key][$key];
          }
        }
      }
    }

    foreach ($options as $data_key => $value) {
      update_option( $data_key, $value );
    }
  }

  /**
   * Generate services field
   * @return string html
   */
  public function generate_services_table_html() {
    $field_key                = $this->shipping_method->get_field_key( 'services' );
    $services                 = Fraktguiden_Helper::get_services_data();
    $service_options = [
      'field_key'                => $field_key,
      'selected'                 => $this->shipping_method->services,
      'custom_prices'            => get_option( $field_key . '_custom_prices' ),
      'free_shipping_checks'     => get_option( $field_key . '_free_shipping_checks' ),
      'free_shipping_thresholds' => get_option( $field_key . '_free_shipping_thresholds' ),
    ];

    ob_start();
    require dirname( dirname( __DIR__ ) ) . '/templates/service-field.php';
    return ob_get_clean();
  }

}