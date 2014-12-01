<?php
/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          http://drivdigital.no
 * Description:         N/A
 * Author:              Driv Digital
 * Author URI:          http://drivdigital.no
 * License:             MIT
 *
 * Version:             1.0.0
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

function init_bring_fraktguiden() {
  if ( class_exists( 'WooCommerce' ) ) {
    load_plugin_textdomain( 'bring-fraktguiden', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    add_action( 'woocommerce_shipping_init', function () {
      include_once 'class-bring-fraktguiden.php';
    } );
  }
}

add_action( 'plugins_loaded', 'init_bring_fraktguiden' );
