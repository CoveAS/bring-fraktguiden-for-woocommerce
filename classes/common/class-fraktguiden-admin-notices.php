<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Fraktguiden_Helper
 *
 * Shared between regular and pro version
 */
class Fraktguiden_Admin_Notices {

	/**
	 * Notices
	 *
	 * @var array
	 */
	protected static $notices = [];

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		if ( defined( 'DISABLE_NAG_NOTICES' ) && DISABLE_NAG_NOTICES ) {
			return;
		}

		add_action( 'admin_notices', __CLASS__ . '::render_notices' );
		add_action( 'wp_ajax_bring_dismiss_notice', __CLASS__ . '::ajax_dismiss_notice' );
		add_action( 'wp_loaded', __CLASS__ . '::missing_shipping_method_notice_middleware' );

		// Check if PRO is available but not activated yet.
		$message = Fraktguiden_Helper::get_pro_description();
		if ( ! Fraktguiden_Helper::pro_activated( true ) ) {
			/* translators: %s: Bring Fraktguiden settings page URL */
			self::add_notice( 'pro_available', $message );
		} elseif ( ! Fraktguiden_Helper::valid_license() ) { // Check if PRO is activated but license not bought.
			self::add_notice(
				'pro_available',
				$message,
				'warning',
				fn() => $GLOBALS['current_section'] !== 'bring_fraktguiden'
			);
		}

		// Check if a default postcode of the origin where packages are sent from is set.
		if ( ! Fraktguiden_Helper::get_option( 'from_zip' ) ) {
			/* translators: %s: Bring Fraktguiden settings page URL */
			$message = __( 'Bring requires a postcode to show where packages are being sent from. Please update your postcode on the <a href="%s">settings page.</a>', 'bring-fraktguiden-for-woocommerce' );
			$message = sprintf( $message, Fraktguiden_Helper::get_settings_url() );

			self::add_notice( 'from_zip_error', $message, 'error', false );
		}

		if ( ! Fraktguiden_Helper::get_option( 'mybring_api_uid' ) || ! Fraktguiden_Helper::get_option( 'mybring_api_key' ) ) {
			self::add_missing_api_credentials_notice();
		} else {
			self::remove_missing_api_credentials_notice();
		}

		if ( ! Fraktguiden_Helper::get_option( 'mybring_customer_number' ) && Fraktguiden_Helper::booking_enabled() ) {
			self::add_missing_api_customer_number_notice();
		} else {
			self::remove_missing_api_customer_number_notice();
		}

		if ( ! Fraktguiden_Helper::get_option( 'services' ) ) {
			self::add_missing_shipping_services_notice();
		} else {
			self::remove_missing_shipping_services_notice();
		}

		if ( 0 ) {
			$kco_settings = get_option( 'woocommerce_kco_settings' );
			if ( ! empty( $kco_settings ) && is_array( $kco_settings ) && $kco_settings['enabled'] === "yes" && get_option( 'woocommerce_shipping_debug_mode', 'no' ) === 'no' ) {
				self::add_klarna_debug_notice();
			} else {
				self::remove_klarna_debug_notice();
			}
		}
	}

	/**
	 * Function adds or removes notice based on shipping method availability in shipping zones
	 */
	public static function missing_shipping_method_notice_middleware() {
		if ( Fraktguiden_Helper::check_bring_fraktguiden_shipping_method() ) {
			self::remove_missing_shipping_method_notice();
		} else {
			self::add_missing_shipping_method_notice();
		}
	}

	/**
	 * Generate disabled debug message
	 */
	public static function generate_klarna_debug_notice() {
		return wp_kses_post(
			sprintf(
				__(
					<<<TEXT
					<strong>Shipping debug mode is not enabled</strong><br>
					It is recommended to enable debug mode when using Bring Fraktguiden for WooCommerce in combination with Klarna Checkout.
					Read more about why recommend this action <a href="%s" target="_blank">here</a>.<br>
					<br>
					Activate in <a href="%s">plugin settings</a>.
					TEXT,
					'bring-fraktguiden-for-woocommerce'
				),
				'https://bringfraktguiden.no/docs/debug-mode-with-payment-provider',
				admin_url() . 'admin.php?page=wc-settings&tab=shipping&section=options#woocommerce_shipping_debug_mode'
			)
		);
	}

	/**
	 * Add disabled debug message
	 */
	public static function add_klarna_debug_notice() {
		return Fraktguiden_Admin_Notices::add_notice( 'bring_fraktguiden_disabled_debug_mode', self::generate_klarna_debug_notice(), 'error', false );
	}

	/**
	 * Remove disabled debug message
	 */
	public static function remove_klarna_debug_notice() {
		return Fraktguiden_Admin_Notices::remove_notice( 'bring_fraktguiden_disabled_debug_mode' );
	}

	/**
	 * Generate missing shipping method notice
	 */
	public static function generate_missing_shipping_method_notice() {
		$messages   = [];
		$messages[] = '<span style="font-weight:bold;color:red;">' . __( 'Bring Fraktguiden shipping method is missing.', 'bring-fraktguiden-for-woocommerce' ) . '</span>';
		$messages[] = sprintf( __( 'You have to add Bring Fraktguiden as a shipping method in your <a href="%s">shipping zones</a>.', 'bring-fraktguiden-for-woocommerce' ), admin_url() . 'admin.php?page=wc-settings&tab=shipping' );

		return implode( '<br>', $messages );
	}

	/**
	 * Add missing shipping method notice
	 */
	public static function add_missing_shipping_method_notice() {
		return Fraktguiden_Admin_Notices::add_notice( 'bring_fraktguiden_missing_shipping_method', self::generate_missing_shipping_method_notice(), 'error', false );
	}

	/**
	 * Remove missing shipping method notice
	 */
	public static function remove_missing_shipping_method_notice() {
		return Fraktguiden_Admin_Notices::remove_notice( 'bring_fraktguiden_missing_shipping_method' );
	}

	/**
	 * Generate missing shipping service notice
	 */
	public static function generate_missing_shipping_services_notice() {
		$messages   = [];
		$messages[] = '<span style="font-weight:bold;color:red;">' . __( 'No shipping services enabled.', 'bring-fraktguiden-for-woocommerce' ) . '</span>';
		$messages[] = sprintf( __( 'You have to enable at least one shipping service in <a href="%s">Shipping Options</a>.', 'bring-fraktguiden-for-woocommerce' ), Fraktguiden_Helper::get_settings_url() . '#woocommerce_bring_fraktguiden_general_options_title' );

		return implode( '<br>', $messages );
	}

	/**
	 * Add missing shipping service notice
	 */
	public static function add_missing_shipping_services_notice() {
		return Fraktguiden_Admin_Notices::add_notice( 'bring_fraktguiden_missing_shipping_services', self::generate_missing_shipping_services_notice(), 'error', false );
	}

	/**
	 * Remove missing shipping service notice
	 */
	public static function remove_missing_shipping_services_notice() {
		return Fraktguiden_Admin_Notices::remove_notice( 'bring_fraktguiden_missing_shipping_services' );
	}

	/**
	 * Generate missing API credentials notice
	 */
	public static function generate_missing_api_credentials_notice() {
		$messages   = [];
		$messages[] = '<span style="font-weight:bold;color:red;">' . __( 'Bring Fraktguiden Email / API key is missing.', 'bring-fraktguiden-for-woocommerce' ) . '</span>';
		$messages[] = __( 'Bring updated their API. All users now need a Mybring account in order to calculate freight.', 'bring-fraktguiden-for-woocommerce' );
		/* translators: %s: Mybring external URL */
		$messages[] = sprintf( __( 'If you do not have a Mybring account, create your account <a href="%s" target="_blank">here</a>.', 'bring-fraktguiden-for-woocommerce' ), 'https://www.mybring.com' );
		/* translators: %s: Mybring settings tab URL */
		$messages[] = sprintf( __( 'Already have an account? Enter your Mybring details <a href="%s">here</a>.', 'bring-fraktguiden-for-woocommerce' ), Fraktguiden_Helper::get_settings_url() . '#woocommerce_bring_fraktguiden_mybring_title' );

		return implode( '<br>', $messages );
	}

	/**
	 * Add missing API credentials notice
	 */
	public static function add_missing_api_credentials_notice() {
		return self::add_notice( 'bring_api_uid_or_key_missing', self::generate_missing_api_credentials_notice(), 'error', false );
	}

	/**
	 * Remove missing API credentials notice
	 */
	public static function remove_missing_api_credentials_notice() {
		return self::remove_notice( 'bring_api_uid_or_key_missing' );
	}

	/**
	 * Generate missing API customer number notice
	 */
	public static function generate_missing_api_customer_number_notice() {
		$messages   = [];
		$messages[] = '<span style="font-weight:bold;color:red;">' . __( 'Bring Fraktguiden API Customer Number is missing.', 'bring-fraktguiden-for-woocommerce' ) . '</span>';
		$messages[] = __( 'Mybring Booking requires an API customer number.', 'bring-fraktguiden-for-woocommerce' );
		/* translators: %s: Mybring settings tab URL */
		$messages[] = sprintf( __( 'Enter your API customer number <a href="%s">here</a>.', 'bring-fraktguiden-for-woocommerce' ), Fraktguiden_Helper::get_settings_url() . '#woocommerce_bring_fraktguiden_mybring_title' );

		return implode( '<br>', $messages );
	}

	/**
	 * Add missing API customer number notice
	 */
	public static function add_missing_api_customer_number_notice() {
		return self::add_notice( 'bring_api_customer_number_missing', self::generate_missing_api_customer_number_notice(), 'error', false );
	}

	/**
	 * Remove missing API customer number notice
	 */
	public static function remove_missing_api_customer_number_notice() {
		return self::remove_notice( 'bring_api_customer_number_missing' );
	}

	/**
	 * Update notice
	 *
	 * @param string  $key         Key.
	 * @param string  $message     Message.
	 * @param string  $type        Type.
	 * @param boolean|Closure $dismissable Dismissable.
	 * @return boolean
	 */
	public static function update_notice( $key, $message, $type = 'info', $dismissable = true ) {
		if ( ! $key ) {
			return false;
		}

		if ( ! in_array( $type, [ 'info', 'warning', 'error' ], true ) ) {
			$type = 'info';
		}

		self::$notices[ $key ] = [
			'message'     => $message,
			'type'        => $type,
			'dismissable' => $dismissable,
		];

		return true;
	}

	/**
	 * Add notice
	 *
	 * @param string  $key         Key.
	 * @param string  $message     Message.
	 * @param string  $type        Type.
	 * @param boolean|Closure $dismissable Dismissable.
	 * @return boolean
	 */
	public static function add_notice( $key, $message, $type = 'info', $dismissable = true ) {
		if ( isset( self::$notices[ $key ] ) ) {
			return false;
		}

		return self::update_notice( $key, $message, $type, $dismissable );
	}

	/**
	 * Remove notice
	 *
	 * @param string $key Key.
	 *
	 * @return boolean
	 */
	public static function remove_notice( $key ) {
		if ( ! $key || ! isset( self::$notices[ $key ] ) ) {
			return false;
		}

		unset( self::$notices[ $key ] );

		return true;
	}

	/**
	 * Get disissed notices
	 *
	 * @return array
	 */
	public static function get_dismissed_notices() {
		$dismissed = Fraktguiden_Helper::get_option( 'dismissed_notices' );

		if ( ! is_array( $dismissed ) ) {
			$dismissed = [];
		}

		return $dismissed;
	}

	/**
	 * Dismiss notice
	 *
	 * @param string $key Key.
	 *
	 * @return boolean
	 */
	public static function dismiss_notice( $key ) {
		$dismissed = self::get_dismissed_notices();

		if ( ! $key ) {
			return false;
		}

		if ( in_array( $key, $dismissed ) ) {
			return true;
		}

		$dismissed[] = $key;

		Fraktguiden_Helper::update_option( 'dismissed_notices', $dismissed );

		return true;
	}

	/**
	 * Recall notice
	 *
	 * @param string $key Key.
	 *
	 * @return void
	 */
	public static function recall_notice( $key ) {
		$dismissed = self::get_dismissed_notices();

		$notice_id = array_search( $key, $dismissed );

		if ( ! $key || false === $notice_id ) {
			return;
		}

		unset( $dismissed[ $notice_id ] );

		Fraktguiden_Helper::update_option( 'dismissed_notices', $dismissed );
	}

	/**
	 * Render notices
	 *
	 * @return void
	 */
	public static function render_notices() {
		$dismissed = self::get_dismissed_notices();

		foreach ( self::$notices as $key => $notice ) {
			$dismissable = $notice['dismissable'];
			if (
				is_callable(
					$dismissable
				)
					? $dismissable()
					: $dismissable
				&& in_array( $key, $dismissed ) ) {
				continue;
			}

			$messages    = $notice['message'];
			$type        = $notice['type']; // Used in required file below.
			$dismissable = $notice['dismissable']; // Used in required file below.

			if ( is_string( $messages ) ) {
				$messages = [ $messages ];
			}

			require dirname( dirname( __DIR__ ) ) . '/includes/admin/pro-notices.php';
		}
	}

	/**
	 * Ajax dismiss notice
	 *
	 * @return void
	 */
	public static function ajax_dismiss_notice() {
		$notice_id = sanitize_key( filter_input( INPUT_POST, 'notice_id' ) );

		$data = [
			'code'    => 'success',
			'message' => $notice_id . ' was dismissed',
		];

		if ( ! self::dismiss_notice( $notice_id ) ) {
			$data = [
				'code'    => 'failure',
				'message' => $notice_id . ' was not dismissed',
			];
		}

		wp_send_json( $data );
	}
}
