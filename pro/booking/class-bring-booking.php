<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// Frontend views
include_once 'views/class-bring-booking-my-order-view.php';


// Consignment
include_once 'classes/consignment/class-bring-consignment.php';
include_once 'classes/consignment/class-bring-mailbox-consignment.php';
include_once 'classes/consignment/class-bring-booking-consignment.php';

// Consignment request
include_once 'classes/consignment-request/class-bring-consignment-request.php';
include_once 'classes/consignment-request/class-bring-booking-consignment-request.php';
include_once 'classes/consignment-request/class-bring-mailbox-consignment-request.php';

// Classes
include_once 'classes/class-bring-booking-file.php';
include_once 'classes/class-bring-booking-customer.php';
include_once 'classes/class-bring-booking-request.php';


if ( is_admin() ) {
  // Views
  include_once 'views/class-bring-booking-labels.php';
  include_once 'views/class-bring-booking-waybills.php';
  include_once 'views/class-bring-booking-order-view-common.php';
  include_once 'views/class-bring-booking-orders-view.php';
  include_once 'views/class-bring-booking-order-view.php';
  include_once 'views/class-bring-waybill-view.php';
  Bring_Waybill_View::setup();
}

if ( Fraktguiden_Helper::booking_enabled() && Fraktguiden_Helper::pro_activated() ) {
  include_once 'classes/class-post-type-mailbox-waybill.php';
  include_once 'classes/class-post-type-mailbox-label.php';
  include_once 'classes/class-generate-mailbox-labels.php';
  Post_Type_Mailbox_Waybill::setup();
  Post_Type_Mailbox_Label::setup();
  Generate_Mailbox_Labels::setup();
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
    $adapter = new Bring_WC_Order_Adapter( $wc_order );

    // One booking request per. order shipping item.
    foreach ( $adapter->get_fraktguiden_shipping_items() as $shipping_item ) {

      // service_id    = POST_I_POSTKASSE_SPORBAR
      // shipping_item = WC_Order_Item_Shipping
      // adapter       = Bring_WC_Order_Adapter

      // Create the consignment
      $consignment_request = Bring_Consignment_Request::create( $shipping_item );
      $consignment_request->fill( [
        'shipping_date_time' => self::get_shipping_date_time(),
        'customer_number'    => isset( $_REQUEST['_bring-customer-number'] ) ? $_REQUEST['_bring-customer-number'] : '',
      ] );
      $original_order_status = $wc_order->get_status();
      // Set order status to awaiting shipping.
      $wc_order->update_status( 'wc-bring-shipment' );

      // Send the booking.
      $response = $consignment_request->post();

      if( ! in_array( $response->get_status_code(),  [200, 201, 202, 203, 204] ) ) {
        //@TODO: Error message
        // wp_send_json( json_decode('['.$response->get_status_code().','.$request_data['body'].','.$response->get_body().']',1) );die;
      }

      // Save the response json to the order.
      $adapter->update_booking_response( $response );
      // Download labels pdf
      if ( $adapter->has_booking_errors() ) {
        // If there are errors, set the status back to the original status.
        $status      = $original_order_status;
        $status_note = __( "Booking errors. See the Bring Booking box for details." . "\n", 'bring-fraktguiden' );
        $wc_order->update_status( $status, $status_note );
        continue;
      }
      // Download the labels
      $consigments = Bring_Consignment::create_from_response( $response, $wc_order->get_id() );
      foreach ( $consigments as $consignment ) {
        $consignment->download_label();
      }
      // Create new status and order note.
      $status = Fraktguiden_Helper::get_option( 'auto_set_status_after_booking_success' );
      if ( $status == 'none' ) {
        // Set status back to the previous status
        $status = $original_order_status;
      }
      $status_note = __( "Booked with Bring" . "\n", 'bring-fraktguiden' );
      // Update status.
      $wc_order->update_status( $status, $status_note );
    }
  }

  static function create_shipping_date() {
    return array(
        'date'   => date_i18n( 'Y-m-d' ),
        'hour'   => date_i18n( 'H', strtotime( '+1 hour', current_time( 'timestamp' ) ) ),
        'minute' => date_i18n( 'i' ),
    );
  }

  static function get_shipping_date_time() {
    // Get the shipping date
    if ( isset( $_REQUEST['_bring-shipping-date'] ) && isset( $_REQUEST['_bring-shipping-date-hour'] ) && isset( $_REQUEST['_bring-shipping-date-minutes'] ) ) {
      return $_REQUEST['_bring-shipping-date'] . 'T' . $_REQUEST['_bring-shipping-date-hour'] . ':' . $_REQUEST['_bring-shipping-date-minutes'] . ':00';
    }
    $shipping_date      = self::create_shipping_date();
    return $shipping_date['date'] . "T" . $shipping_date['hour'] . ":" . $shipping_date['minute'] . ":00";
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
}
