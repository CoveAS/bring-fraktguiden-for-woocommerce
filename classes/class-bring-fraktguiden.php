<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden\Common\Ajax;
use Bring_Fraktguiden\Common\Checkout_Modifications;

/**
 * Bring_Fraktguiden class
 */
class Bring_Fraktguiden {

	const VERSION = '1.8.7';

	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Initialize the plugin
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		spl_autoload_register( __CLASS__ . '::class_loader' );

		$plugin_path = dirname( __DIR__ );
		if ( ! class_exists( 'Packer' ) ) {
			require_once $plugin_path . '/includes/php-laff/src/Packer.php';
		}

		require_once 'class-wc-shipping-method-bring.php';
		require_once 'common/class-fraktguiden-license.php';

		require_once 'common/class-fraktguiden-admin-notices.php';
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

		self::add_settings();

		// Make sure this event hasn't been scheduled.
		if ( ! wp_next_scheduled( 'bring_fraktguiden_cron' ) ) {
			// Schedule the event.
			wp_schedule_event( time(), 'daily', 'bring_fraktguiden_cron' );
		}
		add_action( 'bring_fraktguiden_cron', __CLASS__ . '::cron_task' );

		add_action( 'woocommerce_before_checkout_form', __CLASS__ . '::checkout_message' );
		add_action( 'klarna_before_kco_checkout', __CLASS__ . '::checkout_message' );

		// Check the license when PRO version is activated.
		if ( filter_input( INPUT_POST, 'woocommerce_bring_fraktguiden_pro_enabled' ) ) {
			$license = Fraktguiden_License::get_instance();
			$license->check_license();
		}

		require_once 'common/class-postcode-validation.php';
		Bring_Fraktguiden\Postcode_Validation::setup();

		add_action( 'admin_menu', __CLASS__ . '::add_subsetting_link', 100 );

		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );

		Checkout_Modifications::setup();
		Ajax::setup();
	}

	/**
	 * Class loader
	 *
	 * @param string $class_name Path to class file.
	 */
	public static function class_loader( $class_name ) {
		if ( ! preg_match( '/^Bring_Fraktguiden(\\\.*)$/', $class_name, $matches ) ) {
			return;
		}
		$path      = substr( strtolower( $matches[1] ), 1 );
		$path      = preg_replace( '/_/', '-', $path );
		$parts     = explode( '\\', $path );
		$file_name = array_pop( $parts );
		$dir       = implode( '/', $parts );
		if ( $dir ) {
			$dir = "/$dir";
		}
		$file_name = __DIR__ . "{$dir}/class-{$file_name}.php";
		if ( file_exists( $file_name ) ) {
			require_once $file_name;
		}
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
	 * Add plugin settings
	 */
	public static function add_settings() {
		$default = Fraktguiden_Helper::get_kco_support_default();

		if ( 'yes' === Fraktguiden_Helper::get_option( 'enable_kco_support', $default ) ) {
			require_once 'common/class-fraktguiden-kco-support.php';
			Fraktguiden_KCO_Support::setup();
		}

		require_once 'debug/class-fraktguiden-product-debug.php';
		Fraktguiden_Product_Debug::setup();

		if ( 'yes' !== Fraktguiden_Helper::get_option( 'disable_stylesheet' ) ) {
			add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_styles' );
		}
	}

	/**
	 * Enqueue styles
	 */
	public static function enqueue_styles() {
		// Do not load styles on any page except cart and checkout.
		if ( ! is_cart() && ! is_checkout() ) {
			return;
		}
		$plugin_path = dirname( __DIR__ );
		wp_register_style( 'bring-fraktguiden-for-woocommerce', plugins_url( basename( $plugin_path ) . '/assets/css/bring-fraktguiden.css' ), array(), self::VERSION );
		wp_enqueue_style( 'bring-fraktguiden-for-woocommerce' );
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


	/**
	 * Admin enqueue script
	 * Add custom styling and javascript to the admin options
	 *
	 * @param string $hook Hook.
	 */
	public static function admin_enqueue_scripts( $hook ) {
		if ( 'woocommerce_page_wc-settings' !== $hook ) {
			return;
		}
		wp_enqueue_script( 'hash-tables', plugin_dir_url( __DIR__ ) . '/assets/js/jquery.hash-tabs.min.js', [ 'jquery' ], Bring_Fraktguiden::VERSION );
		wp_enqueue_script( 'bring-admin-js', plugin_dir_url( __DIR__ ) . '/assets/js/bring-fraktguiden-admin.js', [], Bring_Fraktguiden::VERSION );
		wp_enqueue_script( 'bring-settings-js', plugin_dir_url( __DIR__ ) . '/assets/js/bring-fraktguiden-settings.js', [], Bring_Fraktguiden::VERSION, true );
		wp_localize_script(
			'bring-admin-js',
			'bring_fraktguiden',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			]
		);
		wp_localize_script(
			'bring-settings-js',
			'bring_fraktguiden_settings',
			[
				'services_data'    => Fraktguiden_Helper::get_services_data(),
				'services'         => Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services' ),
				'services_enabled' => array_keys( Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services', true ) ),
				'pro_activated'    => Fraktguiden_Helper::pro_activated(),
				'i18n'             => [
					'shipping_name'               => esc_html__( 'Service name:', 'bring-fraktguiden-for-woocommerce' ),
					'fixed_price_override'        => esc_html__( 'Fixed price override:', 'bring-fraktguiden-for-woocommerce' ),
					'alternative_customer_number' => esc_html__( 'Alternative customer number:', 'bring-fraktguiden-for-woocommerce' ),
					'free_shipping_activated_at'  => esc_html__( 'Free shipping activated at:', 'bring-fraktguiden-for-woocommerce' ),
					'additional_fee'              => esc_html__( 'Additional fee:', 'bring-fraktguiden-for-woocommerce' ),
					'value_added_services'        => esc_html__( 'Value added services', 'bring-fraktguiden-for-woocommerce' ),
					'pickup_point'                => esc_html__( 'Pickup points', 'bring-fraktguiden-for-woocommerce' ),
					'error_api_uid'               => esc_html__( 'The api email should be a valid email address.', 'bring-fraktguiden-for-woocommerce' ),
					'error_customer_number'       => esc_html__( 'Customer numbers should be letters (A-Z) and underscores followed by a dash and a number.', 'bring-fraktguiden-for-woocommerce' ),
					'error_api_key'               => esc_html__( 'The api key should only contain letters (a-z), numbers and dashes.', 'bring-fraktguiden-for-woocommerce' ),
					'error_spaces'                => esc_html__( 'Spaces are not allowed in the', 'bring_fraktguiden-for-woocommerce' ),
					'api_email'                   => esc_html__( 'API email', 'bring_fraktguiden-for-woocommerce' ),
					'api_key'                     => esc_html__( 'API key', 'bring_fraktguiden-for-woocommerce' ),
					'customer_number'             => esc_html__( 'customer number', 'bring_fraktguiden-for-woocommerce' ),
				],
			]
		);
		wp_enqueue_style( 'bring-fraktguiden-styles', plugin_dir_url( __DIR__ ) . '/assets/css/bring-fraktguiden-admin.css', [], Bring_Fraktguiden::VERSION );
	}
}
