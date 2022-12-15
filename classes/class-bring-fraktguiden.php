<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden\ClassLoader;
use Bring_Fraktguiden\Common\Ajax;
use Bring_Fraktguiden\Common\Checkout_Modifications;
use Bring_Fraktguiden\Common\Fraktguiden_Admin_Notices;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_License;
use Bring_Fraktguiden\Debug\Fraktguiden_Product_Debug;
use Bring_Fraktguiden\ResourceManagement\Styles;
use BringFraktguidenPro\BringFraktguidenPro;

/**
 * Bring_Fraktguiden class
 */
class Bring_Fraktguiden {

	const VERSION = '1.9.3';

	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Initialize the plugin
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		require_once __DIR__ . '/ClassLoader.php';
		spl_autoload_register( ClassLoader::class . '::load');
		BringFraktguidenPro::setup();

		$plugin_path = dirname( __DIR__ );
		if ( ! class_exists( 'Packer' ) ) {
			require_once $plugin_path . '/includes/php-laff/src/Packer.php';
		}

		require_once 'class-wc-shipping-method-bring.php';

		Fraktguiden_Admin_Notices::init();

		require_once $plugin_path . '/pro/class-wc-shipping-method-bring-pro.php';

		// Enable enhanced descriptions if the option is ticked.
		if ( 'yes' === Fraktguiden_Helper::get_option( 'display_desc' ) ) {
			require_once $plugin_path . '/pro/pickuppoint/class-fraktguiden-pick-up-point-enhancement.php';
			Fraktguiden_Pick_Up_Point_Enhancement::setup();
		}
		if ( 'yes' === Fraktguiden_Helper::get_option( 'display_eta' ) ) {
			add_action( 'woocommerce_after_shipping_rate', 'Bring_Fraktguiden\Common\Rate_Eta::add_estimated_delivery_date', 10, 2 );
		}

		load_plugin_textdomain( 'bring-fraktguiden-for-woocommerce', false, basename( $plugin_path ) . '/languages/' );

		add_action( 'woocommerce_shipping_init', 'Bring_Fraktguiden::shipping_init' );

		add_filter( 'plugin_action_links_' . basename( $plugin_path ) . '/bring-fraktguiden-for-woocommerce.php', __CLASS__ . '::plugin_action_links' );

		if ( is_admin() ) {
			add_action( 'wp_ajax_bring_system_info', [ __CLASS__, 'get_system_info_page' ] );
		}

		Fraktguiden_Minimum_Dimensions::setup();

		Fraktguiden_Product_Debug::setup();
		if ( 'yes' !== Fraktguiden_Helper::get_option( 'disable_stylesheet' ) ) {
			Styles::setup();
		}

		// Make sure this event hasn't been scheduled.
		if ( ! wp_next_scheduled( 'bring_fraktguiden_cron' ) ) {
			// Schedule the event.
			wp_schedule_event( time(), 'daily', 'bring_fraktguiden_cron' );
		}
		add_action( 'bring_fraktguiden_cron', __CLASS__ . '::cron_task' );

		add_action( 'woocommerce_before_checkout_form', __CLASS__ . '::checkout_message' );
		add_action( 'klarna_before_kco_checkout', __CLASS__ . '::checkout_message' );

		// Check the license when PRO version is activated.
		if ( filter_input( INPUT_POST, 'woocommerce_BringFraktguidenPro_enabled' ) ) {
			$license = Fraktguiden_License::get_instance();
			$license->check_license();
		}

		require_once 'common/class-postcode-validation.php';
		Bring_Fraktguiden\Postcode_Validation::setup();

		add_action( 'admin_menu', __CLASS__ . '::add_subsetting_link', 100 );


		Checkout_Modifications::setup();
		Ajax::setup();
	}

	public static function add_subsetting_link() {
		global $submenu;
		if ( ! isset( $submenu['woocommerce'] ) ) {
			return;
		}

		add_submenu_page(
			'woocommerce',
			__( 'Bring settings', 'bring-fraktguiden-for-woocommerce' ),
			__( 'Bring settings', 'bring-fraktguiden-for-woocommerce' ),
			'manage_woocommerce',
			'admin.php?page=wc-settings&tab=shipping&section=bring_fraktguiden'
		);

		return $submenu;
	}

	/**
	 * Get system info page
	 */
	public static function get_system_info_page() {
		require_once dirname( __DIR__ ) . '/system-info-page.php';

		return Fraktguiden_System_Info::generate();
	}


	/**
	 * Set up a cron task for license check
	 */
	public static function cron_task() {
		$license = Fraktguiden_License::get_instance();
		$license->check_license();
	}

	/**
	 * Include Bring shipping method
	 */
	public static function shipping_init() {
		// Add the method to WooCommerce.
		add_filter( 'woocommerce_shipping_methods', 'Bring_Fraktguiden::add_bring_method' );
	}

	/**
	 * Add Bring shipping method to WooCommerce
	 *
	 * @param array $methods A list of shipping methods.
	 *
	 * @return array
	 * @package  WooCommerce/Classes/Shipping
	 * @access public
	 */
	public static function add_bring_method( $methods ) {
		$methods['bring_fraktguiden'] = 'WC_Shipping_Method_Bring_Pro';

		return $methods;
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param array $links The action links displayed for each plugin in the Plugins list table.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {

		$action_links = array(
			'settings' => '<a href="' . Fraktguiden_Helper::get_settings_url() . '" title="' . esc_attr( __( 'View Bring Fraktguiden Settings', 'bring-fraktguiden-for-woocommerce' ) ) . '">' . __( 'Settings', 'bring-fraktguiden-for-woocommerce' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Add action to call when the plugin is deactivated
	 */
	public static function plugin_deactivate() {
		do_action( 'bring_fraktguiden_plugin_deactivate' );
	}

	/**
	 * Display a notification that the PRO version of the plugin runs in a test mode
	 */
	public static function checkout_message() {
		if ( ! Fraktguiden_Helper::pro_test_mode() ) {
			return;
		}

		esc_html_e( 'Bring Fraktguiden PRO is in test-mode. Deactivate the test-mode to remove this message.', 'bring-fraktguiden-for-woocommerce' );
	}


}
