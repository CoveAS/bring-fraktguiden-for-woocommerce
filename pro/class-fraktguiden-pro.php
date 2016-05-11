<?php

class Bring_Fraktguiden_Pro {

  static function init() {
    // Add option settings.
    add_filter( 'bring_fraktguiden_admin_form_fields', 'Bring_Fraktguiden_Pro::add_admin_options', 1, 1 );

    // Register and enqueue the pickup point javascript.
    if ( self::get_woo_setting( 'pickup_point' ) == 'yes' ) {
      wp_register_script( 'bring-fraktguiden-checkout', plugins_url( 'js/bring-fraktguiden-pickuppoint.js', __FILE__ ), array( 'jquery' ), '##VERSION##', true );
      add_action( 'wp_enqueue_scripts', 'Bring_Fraktguiden_Pro::enqueue_pickup_point_script' );
    }
  }

  /**
   * Adds form options fields to the admin page.
   *
   * @param array $fields
   * @return array
   */
  static function add_admin_options( $fields ) {
    //@todo: translate
    $fields['pickup_point'] = array(
        'title'       => 'Pickup Point',
        'label'       => 'Activate pickup point',
        'type'        => 'checkbox',
        'description' => 'TODO: Description',
        'default'     => 'no'
    );
    return $fields;
  }

  /**
   * Enqueue pickup point javascript to the checkout page.
   */
  static function enqueue_pickup_point_script() {
    if ( is_checkout() ) {
      wp_enqueue_script( 'bring-fraktguiden-checkout' );
    }
  }

  /**
   * Gets a Woo admin setting by key
   * Returns false if key is not found.
   *
   * @param string $key
   * @return string|bool
   *
   * @todo: There must be an API in woo for this. Investigate.
   */
  static function get_woo_setting( $key ) {
    $options = get_option( 'woocommerce_' . WC_Shipping_Method_Bring::ID . '_settings' );
    return array_key_exists( $key, $options ) ? $options[$key] : false;
  }

}

Bring_Fraktguiden_Pro::init();


