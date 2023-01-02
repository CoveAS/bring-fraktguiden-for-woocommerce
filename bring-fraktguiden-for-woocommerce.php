<?php
/**
 * Plugin Name:         Bring Fraktguiden for WooCommerce
 * Plugin URI:          https://bringfraktguiden.no/
 * Description:         Bring Fraktguiden for WooCommerce makes it easy for your customers to choose delivery methods from Bring.
 * Author:              Cove AS
 * Author URI:          https://cove.no/
 *
 * Version:             1.9.3
 * Requires at least:   5.6.0
 * Tested up to:        6.0.1
 *
 * WC requires at least: 4.8.0
 * WC tested up to: 6.7.0
 *
 * Text Domain:         bring-fraktguiden-for-woocommerce
 * Domain Path:         /languages
 *
 * @package             WooCommerce
 * @category            Shipping Method
 * @author              Bring Fraktguiden for WooCommerce
 */

use Bring_Fraktguiden\ClassLoader;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_License;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once 'classes/ClassLoader.php';
require_once 'classes/class-bring-fraktguiden.php';

spl_autoload_register( ClassLoader::class . '::load');

if (
	isset($_GET['license-please'])
	&& $_GET['license-please'] === 'bring-fraktguiden'
) {
	try {
		Fraktguiden_License::get_instance()->check_license();
		$valid_to = get_option('bring_fraktguiden_pro_valid_to');
		wp_send_json(['valid_to' => $valid_to]);
	} catch (Exception $e) {
		// Do nothing
		wp_send_json(['error'=> $e->getMessage()], 500);
	}
	die;
}

add_action( 'plugins_loaded', [Bring_Fraktguiden::class, 'init'] );
register_deactivation_hook( __FILE__, [Bring_Fraktguiden::class, 'plugin_deactivate'] );
