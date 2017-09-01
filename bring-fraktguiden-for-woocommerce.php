<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

define( 'FRAKTGUIDEN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          http://drivdigital.no
 * Description:         N/A
 * Author:              Driv Digital
 * Author URI:          http://drivdigital.no
 * License:             MIT
 *
 * Version:             2.2.2
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

      include_once 'includes/laff-pack.php';
      include_once 'classes/class-wc-shipping-method-bring.php';
      include_once dirname( __FILE__ ) . '/pro/class-wc-shipping-method-bring-pro.php';

      load_plugin_textdomain( 'bring-fraktguiden', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

      add_action( 'woocommerce_shipping_init', 'Bring_Fraktguiden::shipping_init' );

      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Bring_Fraktguiden::plugin_action_links' );

      if ( is_admin() ) {
        include_once 'system-info-page.php';
        add_action( 'wp_ajax_bring_system_info', 'Fraktguiden_System_Info::generate' );
      }
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
    $methods['bring_fraktguiden'] = Fraktguiden_Helper::pro_activated() ? 'WC_Shipping_Method_Bring_Pro' : 'WC_Shipping_Method_Bring';
    return $methods;
  }

  /**
   * Show action links on the plugin screen.
   *
   * @param array $links
   * @return array
   */
  static function plugin_action_links( $links ) {
    $section = 'wc_shipping_method_bring';
    if ( class_exists( 'WC_Shipping_Method_Bring_Pro' ) ) {
      $section .= '_pro';
    }
    $action_links = array(
        'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . $section ) . '" title="' . esc_attr( __( 'View Bring Fraktguiden Settings', 'bring-fraktguiden' ) ) . '">' . __( 'Settings', 'bring-fraktguiden' ) . '</a>',
    );

    return array_merge( $action_links, $links );
  }

  /**
   * Called when plugin is deactivated.
   */
  static function plugin_deactivate() {
    do_action( 'bring_fraktguiden_plugin_deactivate' );
  }

}

add_action( 'plugins_loaded', 'Bring_Fraktguiden::init' );
register_deactivation_hook( __FILE__, 'Bring_Fraktguiden::plugin_deactivate' );
