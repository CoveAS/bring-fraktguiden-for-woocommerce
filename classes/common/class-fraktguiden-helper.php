<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Common;

use WC_Shipping_Zones;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Fraktguiden_Helper
 *
 * Shared between regular and pro version
 */
class Fraktguiden_Helper {

	// Be careful changing the ID!
	// Used for Shipping method ID's etc. for existing orders.
	const ID = 'bring_fraktguiden';

	const TEXT_DOMAIN = 'bring-fraktguiden-for-woocommerce';

	/**
	 * Options
	 *
	 * @var array
	 */
	public static $options;

	/**
	 * Check if the license is valid
	 *
	 * @return boolean
	 */
	public static function valid_license() {
		require_once 'class-fraktguiden-license.php';
		$license = Fraktguiden_License::get_instance();

		return $license->valid();
	}

	/**
	 * Get KCO Support default
	 *
	 * @return string Returns 'yes' or 'no'.
	 */
	public static function get_kco_support_default() {
		$is_old = defined( 'KCO_WC_VERSION' ) && 0 > version_compare( KCO_WC_VERSION, '1.8.0' );

		return $is_old ? 'yes' : 'no';
	}

	/**
	 * Get settings url
	 *
	 * @return string URL to the settings page.
	 */
	public static function get_settings_url() {
		$section = 'bring_fraktguiden';

		return admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . $section );
	}

	/**
	 * Pro activated
	 *
	 * @param boolean $ignore_license Ignore the license check if true (default=false).
	 *
	 * @return boolean True means that PRO mode is active.
	 */
	public static function pro_activated( $ignore_license = false ) {
		$pro_allowed = true;

		if ( ! $ignore_license ) {
			$days        = self::get_pro_days_remaining();
			$pro_allowed = ( $days >= 0 ) || self::valid_license() || $ignore_license;

			if ( isset( $_POST['woocommerce_bring_fraktguiden_title'] ) ) {
				return isset( $_POST['woocommerce_bring_fraktguiden_enabled'] ) && $pro_allowed;
			}
		}

		return self::get_option( 'pro_enabled' ) === 'yes' && $pro_allowed;
	}

	/**
	 * Check if booking is enabled
	 *
	 * @return boolean
	 */
	public static function booking_enabled() {
		if ( isset( $_POST['woocommerce_bring_fraktguiden_title'] ) ) {
			return isset( $_POST['woocommerce_bring_fraktguiden_booking_enabled'] );
		}

		return self::get_option( 'booking_enabled' ) === 'yes';
	}

	/**
	 * Check if the plugin works in PRO test mode
	 *
	 * @return boolean
	 */
	public static function pro_test_mode() {
		if ( ! self::pro_activated( true ) ) {
			return false;
		}

		if ( isset( $_POST['woocommerce_bring_fraktguiden_title'] ) ) {
			return isset( $_POST['woocommerce_bring_fraktguiden_test_mode'] );
		}

		return self::get_option( 'test_mode' ) === 'yes';
	}

	/**
	 * Get all services
	 *
	 * @param boolean $id ID.
	 *
	 * @return array
	 */
	public static function get_all_services( $id = false ) {
		$services              = self::get_services_data();
		$result                = [];
		foreach ( $services as $group => $service_group ) {
			foreach ( $service_group['services'] as $key => $service ) {
				$result[ $key ] = $service['productName'];
				if ( ! empty( $id ) ) {
					$result[ $key ] = '@TODO';
				}
			}
		}

		return $result;
	}

	/**
	 * Get all selected services
	 *
	 * @param boolean $id ID.
	 *
	 * @return array
	 */
	public static function get_all_selected_services( $id = false ) {
		$services = self::get_services_data();
		$selected = self::get_option( 'services' );
		$result   = [];
		foreach ( $services as $service_group ) {
			foreach ( $service_group['services'] as $key => $service ) {
				if ( in_array( $key, $selected ) ) {
					if ( ! empty( $id ) ) {
						$result[ $key ] = '@TODO';
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Get all services
	 *
	 * @param int|string $key_to_find Key to find.
	 *
	 * @return array
	 */
	public static function get_service_data_for_key( $key_to_find ) {
		$key_to_find = strtoupper( $key_to_find );
		$result      = [];

		$all_services = self::get_services_data();

		foreach ( $all_services as $service_group ) {
			foreach ( $service_group['services'] as $key => $service ) {
				if ( $key == $key_to_find ) {
					$result = $service;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Available Fraktguiden services.
	 * Information is copied from the service's XML API
	 *
	 * @return array
	 */
	public static function get_services_data() {
		static $services_data;
		static $customer_number;
		if (! isset($services_data)) {
			$services_data = require dirname( dirname( __DIR__ ) ) . '/config/services.php';
		}
		if (! isset($customer_number)) {
			$customer_number = self::get_option( 'mybring_customer_number' );
		}
		if ( ! preg_match( '/^\d+$/', trim( $customer_number ) ) ) {
			$warning = sprintf(
				__( 'You\'re using an outdated customer number, %s - The latest services from Bring require you to update your customer number.', 'bring-fraktguiden-for-woocommerce' ),
				$customer_number
			);
			foreach ( $services_data['common']['services'] as &$service_data ) {
				if ( empty( $service_data['warning'] ) ) {
					$service_data['warning'] = $warning;
				} else {
					$service_data['warning'] = $warning . '<br>' . $service_data['warning'];
				}
			}
			unset( $service_data );
		}
		foreach ( $services_data['homedelivery']['services'] as &$service_data ) {
			$service_data['home_delivery'] = true;
		}
		return apply_filters(
			'bring_fraktguiden_services_data',
			$services_data
		);
	}

	/**
	 * Value added services
	 * https://developer.bring.com/api/services/
	 * https://developer.bring.com/api/services/revisedservice/
	 *
	 * @return array
	 */
	public static function get_vas_data() {
		return require dirname( dirname( __DIR__ ) ) . '/config/value-added-services.php';
	}

	/**
	 * Get phone i18n
	 *
	 * @return array
	 */
	public static function get_phone_i18n() {
		return require dirname( dirname( __DIR__ ) ) . '/config/phone-i18n.php';
	}

	/**
	 * Phone i18n
	 *
	 * @param string $phone_number Phone number.
	 * @param string $country      Country.
	 *
	 * @return string
	 */
	public static function phone_i18n( $phone_number, $country ) {
		static $map;

		// Check for existing + in the beginning of the phone number.
		$phone_number = trim( $phone_number );

		if ( preg_match( '/^\+/', $phone_number ) ) {
			return $phone_number;
		}

		if ( ! $map ) {
			$map = self::get_phone_i18n();
		}

		// The customer country is not found.
		if ( ! isset( $map[ $country ] ) ) {
			return $phone_number;
		}

		// Return the i18n-ed phone number.
		return '+' . $map[ $country ] . ' ' . $phone_number;
	}

	/**
	 * Gets a Woo admin setting by key
	 * Returns false if key is not found.
	 *
	 * @param string  $key     Key.
	 * @param boolean $default Default.
	 *
	 * @return string|bool
	 */
	public static function get_option( $key, $default = false ) {
		if ( empty( self::$options ) ) {
			self::$options = get_option( 'woocommerce_bring_fraktguiden_settings' );
		}

		if ( empty( self::$options ) ) {
			return $default;
		}

		if ( ! isset( self::$options[ $key ] ) ) {
			return $default;
		}

		if ( 'services' === $key ) {
			return array_map( 'strtoupper', self::$options[ $key ] );
		}

		return self::$options[ $key ];
	}

	/**
	 * Updates a Woo admin setting by key
	 *
	 * @param string $key Key.
	 * @param mixed  $data Data.
	 *
	 * @return void
	 */
	public static function update_option( $key, $data ) {
		if ( empty( self::$options ) ) {
			self::$options = get_option( 'woocommerce_bring_fraktguiden_settings', [] );
		}

		self::$options[ $key ] = $data;
		update_option( 'woocommerce_bring_fraktguiden_settings', self::$options, true );
	}

	/**
	 * Returns an array based on the filter in the callback function.
	 * Same as PHP's array_filter but uses the key instead of value.
	 *
	 * @param array    $array    Array.
	 * @param callable $callback Callback.
	 *
	 * @return array
	 */
	public static function array_filter_key( $array, $callback ) {
		$matched_keys = array_filter( array_keys( $array ), $callback );

		return array_intersect_key( $array, array_flip( $matched_keys ) );
	}

	/**
	 * Returns an array with nordic country codes
	 *
	 * @return array
	 */
	public static function get_nordic_countries() {
		global $woocommerce;

		$countries = array( 'NO', 'SE', 'DK', 'FI', 'IS' );

		return self::array_filter_key(
			$woocommerce->countries->countries,
			function ( $k ) use ( $countries ) {
				return in_array( $k, $countries, true );
			}
		);
	}

	/**
	 * Calculate how many days are remaining.
	 *
	 * @return string Returns amount of time since plugin was activated.
	 */
	public static function get_pro_days_remaining() {
		$start_date = self::get_option( 'pro_activated_on', false );
		$time = intval( $start_date );
		$diff = $time + 86400 * 8 - time() - 10;
		$time = floor( $diff / 86400 );

		return $time;
	}

	/**
	 * Get PRO terms link
	 *
	 * @param  string $text Description.
	 *
	 * @return string Link to BRING PRO page.
	 */
	public static function get_pro_terms_link( $text = '' ) {
		if ( ! $text ) {
			$text = __( 'Click here to buy a license or learn more about Bring Fraktguiden Pro.', 'bring-fraktguiden-for-woocommerce' );
		}

		return sprintf( '<a href="%s" target="_blank">%s</a>', 'https://bringfraktguiden.no/', esc_html( $text ) );
	}

	/**
	 * Get PRO description
	 *
	 * @return string
	 */
	public static function get_pro_description() {
		if ( self::pro_test_mode() ) {
			return __( 'Running in test-mode.', 'bring-fraktguiden-for-woocommerce' ) . ' '
			. self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden-for-woocommerce' ) );
		}

		if ( self::pro_activated( true ) ) {
			if ( self::valid_license() ) {
				return '';
			}

			$days = self::get_pro_days_remaining();

			if ( $days < 0 ) {
				$message = __( 'Bring Fraktguiden PRO features have been deactivated.', 'bring-fraktguiden-for-woocommerce' );
				return $message . ' ' .__( 'Please ensure you have a valid license to continue using PRO.', 'bring-fraktguiden-for-woocommerce' ) . '<br>'
				. self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden-for-woocommerce' ) );
			}
			if ( $days > 8 ) {
				return sprintf( __( "☠️ Ahoy, matey! we understand that there ye don't want to, or fer other reasons be unable pay fer our plugin. Please kindly consider gettin' a license at a later time if ye enjoy usin' it.", 'bring-fraktguiden-for-woocommerce' ), "$days " . _n( 'day', 'days', $days, 'bring-fraktguiden-for-woocommerce' ) ) . '<br>'
					. self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden-for-woocommerce' ) );
			}

			/* translators: %s: Number of days */
			return sprintf( __( 'Bring Fraktguiden PRO license has not yet been activated. You have %s remaining before PRO features are disabled.', 'bring-fraktguiden-for-woocommerce' ), "$days " . _n( 'day', 'days', $days, 'bring-fraktguiden-for-woocommerce' ) ) . '<br>'
			. self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden-for-woocommerce' ) );
		}

		$message = __( 'Bring Fraktguiden PRO is now available, <a href="%s">Click here to upgrade to PRO.</a>', 'bring-fraktguiden-for-woocommerce' );
		$message = sprintf( $message, Fraktguiden_Helper::get_settings_url() );

		return $message . sprintf(
			'<ol>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ol>',
			_x( 'Free shipping limits: Set cart thresholds to enable free shipping.', 'Succinct explaination of feature', 'bring-fraktguiden-for-woocommerce' ),
			_x( 'Local pickup points: Let customers select their own pickup point based on their location.', 'Succinct explaination of feature', 'bring-fraktguiden-for-woocommerce' ),
			_x( 'Mybring Booking: Book orders directly from the order page with Mybring', 'Succinct explaination of feature', 'bring-fraktguiden-for-woocommerce' ),
			_x( 'Fixed shipping prices: Define your set price for each freight option', 'Succinct explaination of feature', 'bring-fraktguiden-for-woocommerce' )
		) . ' ' . self::get_pro_terms_link();
	}

	/**
	 * Get admin messages
	 *
	 * @param integer $limit   Limit.
	 * @param boolean $refresh Refresh.
	 *
	 * @return array
	 */
	public static function get_admin_messages( $limit = 0, $refresh = false ) {
		static $messages = [];

		if ( empty( $messages ) || $refresh ) {
			$messages = self::get_option( 'admin_messages' );
		}

		if ( ! is_array( $messages ) ) {
			$messages = [];
		}

		if ( $limit > 0 ) {
			return array_splice( $messages, 0, $limit );
		}

		return $messages;
	}

	/**
	 * Add admin messages
	 *
	 * @param mixed ...$arguments Same as sprintf.
	 */
	public static function add_admin_message( ...$arguments ) {
		static $messages;

		$message  = call_user_func_array( 'sprintf', $arguments );
		$messages = self::get_admin_messages();

		if ( ! in_array( $message, $messages, true ) ) {
			$messages[] = $message;
		}

		self::update_option( 'admin_messages', $messages, false );
	}

	/**
	 * Get input request method
	 *
	 * @return int
	 */
	public static function get_input_request_method() {
		$request_method = 'GET';

		if ( ! empty( $_SERVER['REQUEST_METHOD'] ) ) {
			$request_method = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) );
		}

		return constant( 'INPUT_' . $request_method );
	}

	/**
	 * Get client URL
	 *
	 * @todo: create setting.
	 *
	 * @return bool|string
	 */
	public static function get_client_url() {
		$client_url = filter_input( INPUT_SERVER, 'HTTP_HOST' );

		if ( ! empty( $client_url ) ) {
			return $client_url;
		}

		// Fallback for not supported INPUT_SERVER when using FASTCGI.
		$home_url = wp_parse_url( get_home_url() );

		return $home_url['host'];
	}

	/**
	 * Get pretty-printed shipping methods
	 */
	public static function get_shipping_methods(): array
	{
		if ( ! class_exists( WC_Shipping_Zones::class ) ) {
			return [];
		}

		$shipping_methods = array_map(
			fn ($zoneArray) => $zoneArray['shipping_methods'],
			WC_Shipping_Zones::get_zones()
		);
		$shipping_methods[] = WC_Shipping_Zones::get_zone(0)->get_shipping_methods();

		if ( ! is_array( $shipping_methods ) ) {
			return [];
		}

		$flatten = array_merge( ...$shipping_methods );

		$normalized_shipping_methods = array();

		foreach ( $flatten as $key => $class ) {
			$normalized_shipping_methods[ $class->id ] = $class->method_title;
		}

		return $normalized_shipping_methods;
	}

	/**
	 * Check if Bring Fraktguiden shipping method is active
	 *
	 * @return bool
	 */
	public static function check_bring_fraktguiden_shipping_method() {
		if ( array_key_exists( self::ID, self::get_shipping_methods() ) ) {
			return true;
		} else {
			return false;
		}
	}

}
