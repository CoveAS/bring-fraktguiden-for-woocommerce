<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

include_once 'views/class-bring-booking-my-order-view.php';

if ( is_admin() ) {
  include_once 'admin/class-bring-booking-customer.php';
  include_once 'admin/class-bring-consignment.php';
  include_once 'admin/class-bring-booking-consignment.php';
  include_once 'admin/class-bring-mailbox-consignment.php';
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
    $adapter = new Bring_WC_Order_Adapter( $wc_order );

    // One booking request per. order shipping item.
    foreach ( $adapter->get_fraktguiden_shipping_items() as $shipping_item ) {

      $service_id      = Fraktguiden_Helper::parse_shipping_method_id( $shipping_item['method_id'] )['service'];
      // service_id    = POST_I_POSTKASSE_SPORBAR
      // shipping_item = WC_Order_Item_Shipping
      // adapter       = Bring_WC_Order_Adapter

      // Select the correct consignment type
      if ( preg_match( '/^PAKKE_I_POSTKASSEN/', strtoupper( $service_id ) ) ) {
        $consignment = new Bring_Mailbox_Consignment( $shipping_item );
      } else {
        $consignment = new Bring_Booking_Consignment( $shipping_item );
      }

      $consignment->fill( [
        'shipping_date_time' => self::get_shipping_date_time(),
        'customer_number'    => isset( $_REQUEST['_bring-customer-number'] ) ? $_REQUEST['_bring-customer-number'] : '',
      ] );

      $original_order_status = $wc_order->get_status();

      // Set order status to awaiting shipping.
      $wc_order->update_status( 'wc-bring-shipment' );

      // Set data
      $request = new WP_Bring_Request();
      $request_data = [
        'headers' => [
            'Content-Type'       => 'application/json',
            'Accept'             => 'application/json',
            'X-MyBring-API-Uid'  => self::get_api_uid(),
            'X-MyBring-API-Key'  => self::get_api_key(),
            'X-Bring-Client-URL' => self::get_client_url(),
          ],
          'body' => json_encode( $consignment->create_data() )
      ];

      // Send the booking.
      $response = $request->post( $consignment->get_endpoint_url(), array(), $request_data );
      if( ! in_array( $response->get_status_code(),  [200, 201, 202, 203, 204] ) ) {
        //@TODO: Error message
        // wp_send_json( json_decode('['.$response->get_status_code().','.$request_data['body'].','.$response->get_body().']',1) );die;
      }
      // $response = $booking_request->send();

      // Save the response json to the order.
      $adapter->update_booking_response( $response );

      // Download labels pdf
      if ( ! $adapter->has_booking_errors() ) {
        Bring_Booking_Labels::download_to_local( $adapter );
      }

      // Create new status and order note.
      if ( ! $adapter->has_booking_errors() ) {
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
