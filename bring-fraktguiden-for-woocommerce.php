<?php
/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          https://drivdigital.no/bring-fraktguiden-pro-woocommerce
 * Description:         Bring Fraktguiden for WooCommerce makes it easy for your customers to choose delivery methods from Bring.
 * Author:              Driv Digital AS
 * Author URI:          https://drivdigital.no/bring-fraktguiden-pro-woocommerce
 *
 * Version:             1.6.2
 * Requires at least:   4.9.1
 * Tested up to:        5.2.2
 *
 * WC requires at least: 3.4.0
 * WC tested up to: 3.6.5
 *
 * Text Domain:         bring-fraktguiden-for-woocommerce
 * Domain Path:         /languages
 *
 * @package             WooCommerce
 * @category            Shipping Method
 * @author              Driv Digital AS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'FRAKTGUIDEN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once 'classes/class-bring-fraktguiden.php';

add_action( 'plugins_loaded', 'Bring_Fraktguiden::init' );
register_deactivation_hook( __FILE__, 'Bring_Fraktguiden::plugin_deactivate' );
