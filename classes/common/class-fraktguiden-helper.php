<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

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
		$license = fraktguiden_license::get_instance();

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
				return isset( $_POST['woocommerce_bring_fraktguiden_pro_enabled'] ) && $pro_allowed;
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
		$selected_service_name = self::get_option( 'service_name' );
		$service_name          = $selected_service_name ? $selected_service_name : 'productName';
		$services              = self::get_services_data();
		$result                = [];

		foreach ( $services as $group => $service_group ) {
			foreach ( $service_group['services'] as $key => $service ) {
				$result[ $key ] = $service['productName'];

				if ( 'CustomName' === $service_name ) {
					if ( ! empty( $id ) ) {
						$result[ $key ] = '@TODO';
					}
				} elseif ( 'displayname' === strtolower( $service_name ) ) {
					$result[ $key ] = $service['displayName'];
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
		$selected_service_name = self::get_option( 'service_name' );
		$service_name          = $selected_service_name ? $selected_service_name : 'productName';

		$services = self::get_services_data();
		$selected = self::get_option( 'services' );
		$result   = [];

		foreach ( $services as $service_group ) {
			foreach ( $service_group['services'] as $key => $service ) {
				if ( in_array( $key, $selected ) ) {
					if ( 'CustomName' == $service_name ) {
						if ( empty( $id ) ) {
							$result[ $key ] = $service['productName'];
						} else {
							$result[ $key ] = '@TODO';
						}
					} else {
						$result[ $key ] = $service[ $service_name ];
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
	 * Get all services with customer types
	 *
	 * @return array
	 */
	public static function get_all_services_with_customer_types() {
		$services = self::get_services_data();
		$result   = [];

		foreach ( $services as $service_group ) {
			foreach ( $service_group['services'] as $key => $service ) {
				$service['CustomerTypes'] = self::get_customer_types_for_service_id( $key );
				$result[ $key ]           = $service;
			}
		}

		return $result;
	}

	/**
	 * Get all services
	 *
	 * @param int|string $service_id Service ID.
	 *
	 * @return array
	 */
	private static function get_customer_types_for_service_id( $service_id ) {
		$customer_types = self::get_customer_types_data();
		$result         = [];

		foreach ( $customer_types as $k => $v ) {
			foreach ( $v as $item ) {
				if ( $item == $service_id ) {
					$result[] = $k;
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
		return require dirname( dirname( __DIR__ ) ) . '/config/services.php';
	}

	/**
	 * Get customer types data
	 *
	 * @return array
	 */
	public static function get_customer_types_data() {
		return require dirname( dirname( __DIR__ ) ) . '/config/customer-types.php';
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
	 * @todo: There must be an API in woo for this. Investigate.
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
			self::$options = get_option( 'woocommerce_bring_fraktguiden_settings' );
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
		$time       = intval( $start_date );
		// I made a mistake in the license check here.
		// Any activation time before now (as of writing) should count as a reset for the 7 day trial.
		if ( $time < 1522249027 ) {
			$time = time();
			self::update_option( 'pro_activated_on', $time );
		}
		$time = intval( $start_date );
		$diff = $time + 86400 * 8 - time() - 10;
		$time = floor( $diff / 86400 );
		return $time;
	}

	/**
	 * get_pro_terms_link
	 *
	 * @param  string $text Description
	 * @return string       Link to BRING PRO page
	 */
	static function get_pro_terms_link( $text = '' ) {
		if ( ! $text ) {
			$text = __( 'Click here to buy a license or learn more about Bring Fraktguiden Pro.', 'bring-fraktguiden-for-woocommerce' );
		}
		$format = '<a href="%s" target="_blank">%s</a>';
		return sprintf( $format, 'https://drivdigital.no/bring-fraktguiden-pro-woocommerce', __( $text, 'bring-fraktguiden-for-woocommerce' ) );
	}

	/**
	 * [get_pro_description description]
	 *
	 * @return [type] [description]
	 */
	static function get_pro_description() {
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
				return __( 'Please ensure you have a valid license to continue using PRO.', 'bring-fraktguiden-for-woocommerce' ) . '<br>'
				. self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden-for-woocommerce' ) );
			}
			return sprintf( __( 'Bring Fraktguiden PRO license has not yet been activated. You have %s remaining before PRO features are disabled.', 'bring-fraktguiden-for-woocommerce' ), "$days " . _n( 'day', 'days', $days, 'bring-fraktguiden-for-woocommerce' ) ) . '<br>'
			. self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden-for-woocommerce' ) );
		}
		return sprintf(
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
	 * @param  integer $limit
	 * @param  boolean $refresh
	 * @return array
	 */
	static function get_admin_messages( $limit = 0, $refresh = false ) {
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
	 * @param ...$arguments Same as sprintf
	 */
	static function add_admin_message( ...$arguments ) {
		static $messages;
		$message  = call_user_func_array( 'sprintf', $arguments );
		$messages = self::get_admin_messages();
		if ( ! in_array( $message, $messages ) ) {
			$messages[] = $message;
		}
		self::update_option( 'admin_messages', $messages, false );
	}
}
