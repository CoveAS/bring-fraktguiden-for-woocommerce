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
 *
 * Version:             1.3.2
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

  const VERSION = '1.3.2';

  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

  static function init() {
    if ( ! class_exists( 'WooCommerce' ) ) {
      return;
    }

    if ( ! class_exists( 'LAFFPack' ) ) {
      require_once 'includes/laff-pack.php';
    }
    require_once 'classes/class-wc-shipping-method-bring.php';
    require_once 'classes/common/class-fraktguiden-license.php';
    require_once 'classes/common/class-fraktguiden-admin-notices.php';
    require_once 'pro/class-wc-shipping-method-bring-pro.php';

    load_plugin_textdomain( 'bring-fraktguiden', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    add_action( 'woocommerce_shipping_init', 'Bring_Fraktguiden::shipping_init' );

    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Bring_Fraktguiden::plugin_action_links' );

    if ( is_admin() ) {
      require_once 'system-info-page.php';
      add_action( 'wp_ajax_bring_system_info', 'Fraktguiden_System_Info::generate' );
    }

    Fraktguiden_Minimum_Dimensions::setup();

    // Make sure this event hasn't been scheduled
    if( ! wp_next_scheduled( 'bring_fraktguiden_cron' ) ) {
      // Schedule the event
      wp_schedule_event( time(), 'daily', 'bring_fraktguiden_cron' );
    }
    add_action( 'bring_fraktguiden_cron', __CLASS__ .'::cron_task' );

    add_action( 'woocommerce_before_checkout_form', __CLASS__ .'::checkout_message' );
    add_action( 'klarna_before_kco_checkout', __CLASS__ .'::checkout_message' );

    Fraktguiden_Admin_Notices::init();
  }

  static function cron_task() {
    $license = fraktguiden_license::get_instance();
    $license->valid();
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
    if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) {
      $methods['bring_fraktguiden'] =  'WC_Shipping_Method_Bring_Pro';
    } else {
      $methods['bring_fraktguiden'] =  'WC_Shipping_Method_Bring';
    }
    return $methods;
  }

  /**
   * Show action links on the plugin screen.
   *
   * @param array $links
   * @return array
   */
  static function plugin_action_links( $links ) {
    $action_links = array(
        'settings' => '<a href="' . Fraktguiden_Helper::get_settings_url() . '" title="' . esc_attr( __( 'View Bring Fraktguiden Settings', 'bring-fraktguiden' ) ) . '">' . __( 'Settings', 'bring-fraktguiden' ) . '</a>',
    );

    return array_merge( $action_links, $links );
  }

  /**
   * Called when plugin is deactivated.
   */
  static function plugin_deactivate() {
    do_action( 'bring_fraktguiden_plugin_deactivate' );
  }

  static function checkout_message() {
    if ( ! Fraktguiden_Helper::pro_test_mode() ) {
      return;
    }
    _e( "Bring Fraktguiden PRO test-mode. Purchase a license to deactivate this message.", 'bring-fraktguiden' );

  }

}

add_action( 'plugins_loaded', 'Bring_Fraktguiden::init' );
register_deactivation_hook( __FILE__, 'Bring_Fraktguiden::plugin_deactivate' );
