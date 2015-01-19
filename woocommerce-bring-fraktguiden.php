<?php

/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          http://drivdigital.no
 * Description:         N/A
 * Author:              Driv Digital
 * Author URI:          http://drivdigital.no
 * License:             MIT
 *
 * Version:             1.0.2
 * Requires at least:   3.2.1
 * Tested up to:        4.0.1
 *
 * Text Domain:         bring-fraktguiden
 * Domain Path:         /languages
 *
 * @package             WooCommerce
 * @category            Shipping Method
 * @author              Driv Digital
 */
class Bring_Fraktguiden {
  static function init() {
    if ( class_exists( 'WooCommerce' ) ) {
      load_plugin_textdomain( 'bring-fraktguiden', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
      add_action( 'woocommerce_shipping_init', 'Bring_Fraktguiden::shipping_init' );
    }
  }

  // Include the shipping method
  static function shipping_init() {
    include_once 'classes/class-wc-shipping-method-bring.php';
    // Add the method to WooCommerce.
    add_filter( 'woocommerce_shipping_methods', 'Bring_Fraktguiden::add_bring_method' );
  }

  /**
   * add_bring_method function.
   *
   * @package  WooCommerce/Classes/Shipping
   * @access public
   * @param array $methods
   * @return array
   */
  static function add_bring_method( $methods ) {
    $methods[] = 'WC_Shipping_Method_Bring';
    return $methods;
  }
}

add_action( 'plugins_loaded', 'Bring_Fraktguiden::init' );
