<?php
/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          https://bringfraktguiden.no/
 * Description:         Bring Fraktguiden for WooCommerce makes it easy for your customers to choose delivery methods from Bring.
 * Author:              Bring Fraktguiden
 * Author URI:          https://bringfraktguiden.no/
 *
 * Version:             1.7.1
 * Requires at least:   4.9.1
 * Tested up to:        5.2.2
 *
 * WC requires at least: 3.4.0
 * WC tested up to: 3.7.0
 *
 * Text Domain:         bring-fraktguiden-for-woocommerce
 * Domain Path:         /languages
 *
 * @package             WooCommerce
 * @category            Shipping Method
 * @author              Bring Fraktguiden for WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'classes/class-bring-fraktguiden.php';
require_once 'classes/common/class-fraktguiden-helper.php';

add_action( 'plugins_loaded', 'Bring_Fraktguiden::init' );
register_deactivation_hook( __FILE__, 'Bring_Fraktguiden::plugin_deactivate' );
