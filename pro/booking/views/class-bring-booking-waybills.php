<?php
if ( ! defined( 'ABSPATH' ) ) {
  die; // Exit if accessed directly
}

// Create a menu item for PDF download.
add_action( 'admin_menu', 'Bring_Booking_Waybills::add_menu_page' );
// Process waybill orders
add_action( 'admin_init', 'Bring_Booking_Waybills::process_waybill_order' );

class Bring_Booking_Waybills {

  static $responses = [];

  /**
   * Add menu page
   */
  static function add_menu_page() {
    add_menu_page( 'Waybills', 'Waybills', 'manage_woocommerce', 'bring_waybills', __CLASS__.'::waybills_page', 'dashicons-chart-pie', 57 );
  }

  /**
   * Waybills page
   */
  static function waybills_page() {

    foreach ( self::$responses as $response_data ) {
      $request  = $response_data['request'];
      $response = $response_data['response'];
      $data     = json_decode( $response->body, 1 );
      require dirname( __DIR__ ) .'/templates/waybills-messages.php';
    }

    // List labels ready to order
    echo "Labels:";
    // checkbox | consignment_number | order_id | price |  date/time | download link
    $consignments = self::get_unbooked_consignments();
    require dirname( __DIR__ ) .'/templates/waybills-table-labels.php';
    // Book selected shipments | Book all shipments

    // List waybills
    echo "waybills:";
    // id | reference | date/time | download link
    self::get_waybills();
  }

  /**
   * Process response
   */
  static function process_waybill_order() {
    if ( ! isset( $_POST['consignment_numbers'] ) ) {
      return;
    }
    require_once dirname( __DIR__ ).'/classes/class-bring-mailbox-waybill-request.php';
    $consignment_numbers = $_POST['consignment_numbers'];
    foreach ( $consignment_numbers as $customer_number => $consigments ) {
      $request  = new Bring_Mailbox_Waybill_Request( $customer_number, array_keys( $consigments ) );
      $response = $request->post();
      $waybill  = null;

      // Save the waybill
      if ( 201 == $response->status ) {
        $data = json_decode( $response->body, 1 );
        $waybill = new Bring_Waybill( $data );
        $waybill->save();
      }

      // Store the data for later display
      self::$responses[] = [
        'request'  => $request,
        'response' => $response,
        'waybill'  => $waybill,
      ];
    }
  }

  /**
   * Get unbooked consignments
   * @return array
   */
  static function get_unbooked_consignments() {
    $posts = get_posts([
      'post_type'      => 'shop_order',
      'post_status'    => 'any',
      'posts_per_page' => -1,
      'fields'         => 'ids',
    ]);
    $consignments = [];
    foreach ( $posts as $post_id ) {
      $wc_order = wc_get_order( $post_id );
      $adapter = new Bring_WC_Order_Adapter( $wc_order );
      $order_consignments = $adapter->get_booking_consignments();
      foreach ( $order_consignments as $consignment ) {
        $consignments[] = $consignment;
      }
    }
    return $consignments;
  }

  static function get_waybills() {

  }
}
