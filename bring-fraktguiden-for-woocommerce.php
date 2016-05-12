<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          http://drivdigital.no
 * Description:         N/A
 * Author:              Driv Digital
 * Author URI:          http://drivdigital.no
 * License:             MIT
 *
 * Version:             ##VERSION##
 * Requires at least:   3.2.1
 * Tested up to:        4.4.1
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
      include_once 'classes/class-wc-shipping-method-bring.php';

      // Add pro features.
      if ( file_exists( dirname( __FILE__ ) . '/pro/class-fraktguiden-pro.php' ) ) {
        include_once dirname( __FILE__ ) . '/pro/class-fraktguiden-pro.php';
      }

      load_plugin_textdomain( 'bring-fraktguiden', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
      add_action( 'woocommerce_shipping_init', 'Bring_Fraktguiden::shipping_init' );
    }
  }

  /**
   * Includes the shipping method
   */
  static function shipping_init() {
    // Add the method to WooCommerce.
    add_filter( 'woocommerce_shipping_methods', 'Bring_Fraktguiden::add_bring_method' );
  }

  /**
   * Adds the Bring shipping method to WooCommerce
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
