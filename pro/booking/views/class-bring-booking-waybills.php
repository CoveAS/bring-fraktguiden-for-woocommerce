<?php
if ( ! defined( 'ABSPATH' ) ) {
  die; // Exit if accessed directly
}

Bring_Booking_Waybills::setup();

class Bring_Booking_Waybills {

  static function setup() {
    // Process waybill orders
    add_action( 'admin_init', __CLASS__ .'::process_waybill_order' );
    add_filter( 'bulk_actions-edit-waybill', __CLASS__ .'::register_bulk_actions' );
    add_filter( 'handle_bulk_actions-edit-waybill', __CLASS__ .'::bulk_action_handler', 10, 3 );
    add_action( 'admin_notices', __CLASS__ .'::bulk_action_admin_notice' );

  }
  /**
   * Process response
   */
  static function process_waybill_order() {
    if ( ! isset( $_POST['consignment_numbers'] ) ) {
      return;
    }
    $consignment_numbers = $_POST['consignment_numbers'];
    foreach ( $consignment_numbers as $customer_number => $consigments ) {

    }
  }

  static function book_mailbox_consignment( $customer_number, $consignments ) {
    require_once dirname( __DIR__ ).'/classes/class-bring-mailbox-waybill-request.php';
    // Waybill booking does not have a test option
    $request  = new Bring_Mailbox_Waybill_Request( $customer_number, array_keys( $consigments ) );
    $response = $request->post();
    $waybill  = null;

    // Save the waybill
    if ( property_exists( $response, 'status' ) && 201 == $response->status ) {
      $data = json_decode( $response->body, 1 );
      $waybill = new Bring_Waybill( $data );
      $waybill->save();
    }

    // Store the data for later display
    var_dump( [
      'request'  => $request,
      'response' => $response,
      'waybill'  => $waybill,
    ] );
    die;
  }


  static function register_bulk_actions($bulk_actions) {
    $bulk_actions['book_mailbox_consignments'] = __( 'Book consignments', 'book_mailbox_consignments');
    return $bulk_actions;
  }


  static function bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'book_mailbox_consignments' ) {
      return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
      echo "$post_id\n";

      $consignment_number = get_post_meta( $post_id, '_consignment_number', true );
      $customer_number = get_post_meta( $post_id, '_customer_number', true );

      self::book_mailbox_consignment( $customer_number, $consignment_number );
    }
    die;
    $redirect_to = add_query_arg( 'waybills_booked', count( $post_ids ), $redirect_to );
    return $redirect_to;
  }

  static function bulk_action_admin_notice() {
    if ( ! empty( $_REQUEST['waybills_booked'] ) ) {
      $emailed_count = intval( $_REQUEST['waybills_booked'] );
      printf( '<div id="message" class="updated fade">' .
        _n( 'Booked %s mailbox parcels.',
          'Booked %s mailbox parcels.',
          $emailed_count,
          'book_mailbox_consignments'
        ) . '</div>', $emailed_count );
    }
  }
}
