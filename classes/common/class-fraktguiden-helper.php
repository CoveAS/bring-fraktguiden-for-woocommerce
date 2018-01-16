<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
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

  const TEXT_DOMAIN = 'bring-fraktguiden';

  static $options;

  static function valid_license() {
    $license = fraktguiden_license::get_instance();
    return $license->valid();
  }

  /**
   * Get settings url
   * @return string URL to the settings page
   */
  static function get_settings_url() {
    $section = 'bring_fraktguiden';
    // if ( class_exists( 'WC_Shipping_Method_Bring_Pro' ) ) {
    //   $section .= '_pro';
    // }
    return admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . $section );
  }
  /**
   * Pro activated
   * @param  boolean $ignore_license (default=false) Ignore the license check if true
   * @return boolean                 True means that PRO mode is active
   */
  static function pro_activated( $ignore_license = false ) {
    $days = self::get_pro_days_remaining();
    $pro_allowed =  ( $days >= 0 ) || self::valid_license() || $ignore_license;
    if ( isset( $_POST['woocommerce_bring_fraktguiden_title'] ) ) {
      return isset( $_POST['woocommerce_bring_fraktguiden_pro_enabled'] ) && $pro_allowed;
    }
    return self::get_option( 'pro_enabled' ) == 'yes' && $pro_allowed;
  }

  static function booking_enabled() {
    if ( isset( $_POST['woocommerce_bring_fraktguiden_title'] ) ) {
      return isset( $_POST['woocommerce_bring_fraktguiden_booking_enabled'] );
    }
    return self::get_option( 'booking_enabled' ) == 'yes';
  }

  static function pro_test_mode() {
    if ( ! self::pro_activated( true) ) {
      return false;
    }
      if ( isset( $_POST['woocommerce_bring_fraktguiden_title'] ) ) {
        return isset( $_POST['woocommerce_bring_fraktguiden_test_mode'] );
      }
      return self::get_option( 'test_mode' ) == 'yes';
  }

  static function get_all_services() {
    $selected_service_name = Fraktguiden_Helper::get_option('service_name');
    $service_name = $selected_service_name ? $selected_service_name  : 'ProductName';
    $services = self::get_services_data();
    $result   = [ ];
    foreach ( $services as $key => $service ) {
      $result[$key] = $service[$service_name];
    }
    return $result;
  }

  static function get_all_selected_services() {
    $selected_service_name = Fraktguiden_Helper::get_option('service_name');
    $service_name = $selected_service_name ? $selected_service_name  : 'ProductName';

    $services = self::get_services_data();
    $selected = self::get_option( 'services' );
    $result   = [ ];
    foreach ( $services as $key => $service ) {
      if ( in_array( $key, $selected ) ) {
        $result[$key] = $service[$service_name];
      }
    }
    return $result;
  }

  static function get_service_data_for_key( $key_to_find ) {
    $result = [ ];

    $all_services = self::get_services_data();
    foreach ( $all_services as $key => $service ) {
      if ( $key == $key_to_find ) {
        $result = $service;
        break;
      }

    }
    return $result;
  }

  static function get_all_services_with_customer_types() {
    $services = self::get_services_data();
    $result   = [ ];
    foreach ( $services as $key => $service ) {
      $service['CustomerTypes'] = self::get_customer_types_for_service_id( $key );
      $result[$key]             = $service;
    }
    return $result;
  }

  static private function get_customer_types_for_service_id( $service_id ) {
    $customer_types = self::get_customer_types_data();
    $result         = [ ];
    foreach ( $customer_types as $k => $v ) {
      //$result[] = $key;
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
     * @return array
     */
  static public function get_services_data() {
    return require dirname( dirname( __DIR__ ) ) .'/config/services.php';
  }

  static function get_customer_types_data() {
    return require dirname( dirname( __DIR__ ) ) .'/config/customer-types.php';
  }

  /**
   * Gets a Woo admin setting by key
   * Returns false if key is not found.
   *
   * @todo: There must be an API in woo for this. Investigate.
   *
   * @param string $key
   * @return string|bool
   */
  static function get_option( $key, $default = false ) {
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
   * @param string $key
   * @return string|bool
   */
  static function update_option( $key, $data ) {
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
   * @param array $array
   * @param callable $callback
   * @return array
   */
  static function array_filter_key( $array, $callback ) {
    $matched_keys = array_filter( array_keys( $array ), $callback );
    return array_intersect_key( $array, array_flip( $matched_keys ) );
  }

  /**
   * Returns an array with nordic country codes
   *
   * @return array
   */
  static function get_nordic_countries() {
    global $woocommerce;
    $countries = array( 'NO', 'SE', 'DK', 'FI', 'IS' );
    return Fraktguiden_Helper::array_filter_key( $woocommerce->countries->countries, function ( $k ) use ( $countries ) {
      return in_array( $k, $countries );
    } );
  }

  static function parse_shipping_method_id( $method_id ) {
    $parts = explode( ':', $method_id );
    $service = count( $parts ) == 2 ? strtoupper( $parts[1] ) : '';
    // Identify pickup_point_id as part of the service name
    $pickup_point_id = false;
    if ( preg_match( '/^(SERVICEPAKKE)-(\d+)$/', $service, $matches ) ) {
      $service = $matches[1];
      $pickup_point_id = $matches[2];
    }
    //@todo: rename service > service_key
    return [
        'name'    => $parts[0],
        'service' => $service,
        'pickup_point_id' => $pickup_point_id,
    ];
  }

  /**
   * get_pro_days_remaining calculates how many days are remaining.
   * @return [string] [Returns amount of time since plugin was activated]
   */
  static function get_pro_days_remaining() {
    $start_date = self::get_option( 'pro_activated_on', false );
    if ( ! $start_date ) {
      $time = time();
      self::update_option( 'pro_activated_on', $time );
    } else {
      $time = intval( $start_date );
    }
    $diff = $time + 86400 * 7 - time();
    $time = floor( $diff / 86400 );
    return $time;
  }

  /**
   * get_pro_terms_link
   * @param  string $text Description
   * @return string       Link to BRING PRO page
   */
  static function get_pro_terms_link( $text = '' ) {
    if ( ! $text ) {
      $text = __( 'Click here to buy a license or learn more about Bring Fraktguiden Pro.', 'bring-fraktguiden' );
    }
    $format = '<a href="%s" target="_blank">%s</a>';
    return sprintf( $format, 'https://drivdigital.no/bring-fraktguiden-pro-woocommerce', __( $text, 'bring-fraktguiden' ) );
  }

  /**
   * [get_pro_description description]
   * @return [type] [description]
   */
  static function get_pro_description() {
    if ( self::pro_test_mode() ) {
      return __( 'Running in test-mode.', 'bring-fraktguiden' ) . ' '
        . self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden' ) );
    }
    if ( self::pro_activated( true ) ) {
      if ( self::valid_license() ) {
        return '';
      }
      $days = self::get_pro_days_remaining();

      if ( $days < 0 ) {
        return __( 'Please ensure you have a valid license to continue using PRO.', 'bring-fraktguiden' ). '<br>'
        . self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden' ) );
      }
      return sprintf( __( 'Bring Fraktguiden PRO license has not yet been activated. You have %s remaining before PRO features are disabled.', 'bring-fraktguiden' ), "$days " . _n( 'day', 'days', $days, 'bring-fraktguiden' ) ). '<br>'
      . self::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden' ) );
    }
    return sprintf(
      '<h3>%s</h3>
      <ol>
        <li>%s</li>
        <li>%s</li>
        <li>%s</li>
        <li>%s</li>
      </ol>',
      _x( 'Bring Fraktguiden Pro features', 'Title for the features section', 'bring-fraktguiden' ),
      _x( 'Free shipping limits: Set cart thresholds to enable free shipping.', 'Succinct explaination of feature', 'bring-fraktguiden' ),
      _x( 'Local pickup points: Let customers select their own pickup point based on their location.', 'Succinct explaination of feature', 'bring-fraktguiden' ),
      _x( 'MyBring Booking: Book orders directly from the order page with MyBring', 'Succinct explaination of feature', 'bring-fraktguiden' ),
      _x( 'Fixed shipping prices: Define your set price for each freight option', 'Succinct explaination of feature', 'bring-fraktguiden' )
    ) . ' ' . Fraktguiden_Helper::get_pro_terms_link();
  }

  /**
   * Get admin messages
   * @param  integer $limit
   * @param  boolean $refresh
   * @return array
   */
  static function get_admin_messages( int $limit = 0, $refresh = false ) {
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
   * @param ...$arguments Same as sprintf
   */
  static function add_admin_message( ...$arguments ) {
    static $messages;
    $message = call_user_func_array( 'sprintf', $arguments );
    $messages = self::get_admin_messages();
    if ( ! in_array( $message, $messages ) ) {
      $messages[] = $message;
    }
    self::update_option( 'admin_messages', $messages, false );
  }
}
