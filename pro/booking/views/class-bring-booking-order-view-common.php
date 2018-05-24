<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Common_View {

  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

  static function render_customer_selector( $name = '_bring-customer-number' ) {
    try {
      $customers = Bring_Booking_Customer::get_customer_numbers_formatted();
    }
    catch ( Exception $e ) {
      printf( '<p class="error">%s</p>', $e->getMessage() );
      return;
    }
    echo '<select name="' . $name . '" class="wc-enhanced-select" style="max-width:20em">';
    foreach ( $customers as $key => $val ) {
      echo '<option value="' . $key . '">' . $val . '</option>';
    }
    echo '</select>';
  }

  static function render_shipping_date_time( $name = '_bring-shipping-date' ) {
    $shipping_date = Bring_Booking::create_shipping_date();
    echo '<input type="text" name="' . $name . '" value="' . $shipping_date['date'] . '"  maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" style="width:12.5em">@';
    echo '<input type="text" name="' . $name . '-hour" value="' . $shipping_date['hour'] . '" maxlength="2" placeholder="' . __( 'hh', 'bring-fraktguiden' ) . '" style="width:3em;text-align:center">:';
    echo '<input type="text" name="' . $name . '-minutes" value="' . $shipping_date['minute'] . '" maxlength="2" placeholder="' . __( 'mm', 'bring-fraktguiden' ) . '" style="width:3em;text-align:center">';
  }

  static function booking_label( $plural = false ) {
    $label = sprintf( '%s', ( $plural == true ) ? __( 'Bring - Submit Consignments', 'bring-fraktguiden' ) : __( 'Submit Consignment', 'bring-fraktguiden' ) );
    return $label . ( Bring_Booking::is_test_mode() ? ' - '. __( 'Test mode', 'bring-fraktguiden' ) : '' );
  }

  /**
   * @param array $status
   * @param int $size
   * @return string
   */
  static function create_status_icon( $status, $size = 96 ) {
    return '<span class="dashicons ' . $status['icon'] . ' bring-booking-status-icon" style="font-size: ' . $size . 'px; width: ' . $size . 'px; height: ' . $size . 'px"></span>';
  }

  static function is_step2() {
    return isset( $_GET['booking_step'] ) && $_GET['booking_step'] == 2;
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   * @return string
   */
  static function get_booking_status_info( $order ) {
    $result = [
        'text' => __( 'No', 'bring-fraktguiden' ),
        'icon' => 'dashicons-minus',
    ];

    if ( self::is_step2() ) {
      $result = [
          'text' => __( 'In progress', 'bring-fraktguiden' ),
          'icon' => '',
      ];
    }

    if ( $order->is_booked() ) {
      $result = [
          'text' => __( 'Booked', 'bring-fraktguiden' ),
          'icon' => 'dashicons-yes',
      ];
    }

    if ( $order->has_booking_errors() ) {
      $result = [
          'text' => __( 'Failed', 'bring-fraktguiden' ),
          'icon' => 'dashicons-warning',
      ];
    }

    return $result;
  }

}
