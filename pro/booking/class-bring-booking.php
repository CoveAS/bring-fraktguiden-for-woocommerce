<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

include_once 'views/class-bring-booking-my-order-view.php';

if ( is_admin() ) {
  include_once 'admin/class-bring-booking-customer.php';
  include_once 'admin/class-bring-booking-consignment-creator.php';
  include_once 'admin/class-bring-booking-request.php';
  include_once 'admin/class-bring-booking-labels.php';
  include_once 'views/class-bring-booking-order-view-common.php';
  include_once 'views/class-bring-booking-orders-view.php';
  include_once 'views/class-bring-booking-order-view.php';
}

# Register awaiting shipment status.
add_action( 'init', 'Bring_Booking::register_awaiting_shipment_order_status' );
# Add awaiting shipping to existing order statuses.
add_filter( 'wc_order_statuses', 'Bring_Booking::add_awaiting_shipment_status' );

class Bring_Booking {

  const ID = Fraktguiden_Helper::ID;
  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

  static function init() {
    if ( self::is_valid_for_use() ) {
      Bring_Booking_Orders_View::init();
      Bring_Booking_Order_View::init();
    }
  }

  /**
   * @return bool
   */
  static function is_valid_for_use() {
    $api_uid = self::get_api_uid();
    $api_key = self::get_api_key();
    return $api_uid && $api_key;
  }

  /**
   * Register awaiting shipment order status.
   */
  static function register_awaiting_shipment_order_status() {
    // Be careful changing the post status name.
    // If orders has this status they will not be available in admin.
    register_post_status( 'wc-bring-shipment', array(
        'label'                     => __( 'Awaiting Shipment', 'bring-fraktguiden' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( __( 'Awaiting Shipment', 'bring-fraktguiden' ) . ' <span class="count">(%s)</span>', __( 'Awaiting Shipment', 'bring-fraktguiden' ) . ' <span class="count">(%s)</span>' )
    ) );
  }

  /**
   * Add awaiting shipment to order statuses.
   *
   * @param array $order_statuses
   * @return array
   */
  static function add_awaiting_shipment_status( $order_statuses ) {
    $new_order_statuses = array();
    // Add the order status after processing
    foreach ( $order_statuses as $key => $status ) {
      $new_order_statuses[$key] = $status;
      if ( 'wc-processing' === $key ) {
        $new_order_statuses['wc-bring-shipment'] = __( 'Awaiting Shipment', 'bring-fraktguiden' );
      }
    }
    return $new_order_statuses;
  }

  /**
   * @param WC_Order $wc_order
   */
  static function send_booking( $wc_order ) {
    $order = new Bring_WC_Order_Adapter( $wc_order );

    $order_id        = $wc_order->get_id();
    $test_mode       = self::is_test_mode();
    $api_key         = self::get_api_key();
    $api_uid         = self::get_api_uid();
    $client_url      = self::get_client_url();
    $customer_number = isset( $_REQUEST['_bring-customer-number'] ) ? $_REQUEST['_bring-customer-number'] : '';

    if ( isset( $_REQUEST['_bring-shipping-date'] ) && isset( $_REQUEST['_bring-shipping-date-hour'] ) && isset( $_REQUEST['_bring-shipping-date-minutes'] ) ) {
      $shipping_date_time = $_REQUEST['_bring-shipping-date'] . 'T' . $_REQUEST['_bring-shipping-date-hour'] . ':' . $_REQUEST['_bring-shipping-date-minutes'] . ':00';
    }
    else {
      $shipping_date      = self::create_shipping_date();
      $shipping_date_time = $shipping_date['date'] . "T" . $shipping_date['hour'] . ":" . $shipping_date['minute'] . ":00";
    }

    $additional_info = '';
    if ( isset( $_REQUEST['_bring_additional_info'] ) ) {
      $additional_info = filter_var( $_REQUEST['_bring_additional_info'], FILTER_SANITIZE_STRING );
    }

    $sender_address    = self::get_sender_address( $wc_order, $additional_info );

    $recipient_address = $order->get_recipient_address_formatted();

    // One booking request per. order shipping item.
    foreach ( $order->get_fraktguiden_shipping_items() as $item_id => $shipping_method ) {

      $service_id   = Fraktguiden_Helper::parse_shipping_method_id( $shipping_method['method_id'] )['service'];
      $packages     = $order->get_packages_formatted( $item_id );
      $pickup_point = $order->get_pickup_point_for_shipping_item_formatted( $item_id );

      $booking_request = new Bring_Booking_Request( new WP_Bring_Request() );
      $booking_request
          ->set_test_mode( $test_mode )
          ->set_content_type( 'application/json' )
          ->set_accept( 'application/json' )
          ->set_api_key( $api_key )
          ->set_api_uid( $api_uid )
          ->set_client_url( $client_url );

      $consignment = new Bring_Booking_Consignment_Creator();
      $consignment
          ->set_purchase_order( $order_id )
          ->set_shipping_date_time( $shipping_date_time )
          ->set_sender_address( $sender_address )
          ->set_recipient_address( $recipient_address )
          ->set_product_id( $service_id )
          ->set_customer_number( $customer_number )
          ->set_packages( $packages );

      if ( ! empty( $pickup_point ) ) {
        $consignment->set_pickup_point( $pickup_point );
      }

      if ( Fraktguiden_Helper::get_option( 'evarsling' ) == 'yes' ) {
        $product_services = [
            'recipientNotification' => [
                'email'  => $recipient_address['contact']['email'],
                'mobile' => $recipient_address['contact']['phoneNumber'],
            ]
        ];
        $consignment->set_product_services( $product_services );
      }

      $booking_request->add_consignment_data( $consignment->create_data() );

      // Start sending the booking
      if ( $booking_request->is_valid() ) {

        $original_order_status = $wc_order->get_status();

        // Set order status to awaiting shipping.
        $wc_order->update_status( 'wc-bring-shipment' );

        // Send the booking.
        $response = $booking_request->send();

        // Save the response json to the order.
        $order->update_booking_response( $response );

        // Download labels pdf
        if ( ! $order->has_booking_errors() ) {
          Bring_Booking_Labels::download_to_local( $order );
        }

        // Create new status and order note.
        if ( ! $order->has_booking_errors() ) {
          // Check if the plugin has been configured to set a specific order status after success.
          $status = Fraktguiden_Helper::get_option( 'auto_set_status_after_booking_success' );
          if ( $status == 'none' ) {
            // Set status back to the previous status
            $status = $original_order_status;
          }
          $status_note = __( "Booked with Bring" . "\n", 'bring-fraktguiden' );
        }
        else {
          // If there are errors, set the status back to the original status.
          $status      = $original_order_status;
          $status_note = __( "Booking errors. See the Bring Booking box for details." . "\n", 'bring-fraktguiden' );
        }

        // Update status.
        $wc_order->update_status( $status, $status_note );
      }
      else {
        // @todo: Not valid. show message?
      }

    }
  }

  static function create_shipping_date() {
    return array(
        'date'   => date_i18n( 'Y-m-d' ),
        'hour'   => date_i18n( 'H', strtotime( '+1 hour', current_time( 'timestamp' ) ) ),
        'minute' => date_i18n( 'i' ),
    );
  }

  /**
   * Bulk booking requests.
   *
   * @param array $post_ids Array of WC_Order ID's
   */
  static function bulk_send_booking( $post_ids ) {
    foreach ( $post_ids as $post_id ) {
      $order = new Bring_WC_Order_Adapter( new WC_Order( $post_id ) );
      if ( ! $order->has_booking_consignments() ) {
        self::send_booking( $order->order );
      }
    }
  }

  /**
   * @return bool
   */
  static function is_test_mode() {
    return Fraktguiden_Helper::get_option( 'booking_test_mode_enabled' ) == 'yes';
  }

  /**
   * @return bool|string
   */
  static function get_api_uid() {
    return Fraktguiden_Helper::get_option( 'mybring_api_uid' );
  }

  /**
   * @return bool|string
   */
  static function get_api_key() {
    return Fraktguiden_Helper::get_option( 'mybring_api_key' );
  }

  /**
   * @todo: create setting.
   * @return bool|string
   */
  static function get_client_url() {
    return $_SERVER['HTTP_HOST'];
  }

  /**
   * Return the sender's address formatted for Bring consignment
   *
   * @param WC_Order $wc_order
   * @param string $additional_info
   * @return array
   */
  static function get_sender_address( $wc_order, $additional_info = '' ) {
    $form_fields = [
        'booking_address_store_name',
        'booking_address_street1',
        'booking_address_street2',
        'booking_address_postcode',
        'booking_address_city',
        'booking_address_country',
        'booking_address_contact_person',
        'booking_address_phone',
        'booking_address_email',
        'booking_address_reference',
    ];


    // Load sender address data from options.
    $result = [];
    foreach ( $form_fields as $field ) {
      $result[$field] = Fraktguiden_Helper::get_option( $field );
    }

    return [
        "name"                  => $result['booking_address_store_name'],
        "addressLine"           => $result['booking_address_street1'],
        "addressLine2"          => $result['booking_address_street2'],
        "postalCode"            => $result['booking_address_postcode'],
        "city"                  => $result['booking_address_city'],
        "countryCode"           => $result['booking_address_country'],
        "reference"             => self::parse_sender_address_reference( $result['booking_address_reference'], $wc_order ),
        "additionalAddressInfo" => $additional_info,
        "contact"               => [
            "name"        => $result['booking_address_contact_person'],
            "email"       => $result['booking_address_email'],
            "phoneNumber" => $result['booking_address_phone'],
        ]
    ];
  }

  /**
   * Parses the sender address reference value.
   * Supports simple template macros.
   *
   * Eg. "Order: {order_id}" will be replace {order_id} with the order's ID
   *
   * Available macros:
   *
   *   {order_id}
   *
   * @param string $reference
   * @param WC_Order $wc_order
   * @return mixed
   */
  static function parse_sender_address_reference( $reference, $wc_order ) {
    $replacements = array(
        '{order_id}' => $wc_order->get_id(),
    );
    $result = $reference;
    foreach ( $replacements as $replacement => $value ) {
      $result = preg_replace( "/" . preg_quote( $replacement ) . "/", $value, $result );
    }
    return $result;
  }

}
