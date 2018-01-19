<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

add_filter( 'woocommerce_order_shipping_to_display', 'Bring_Booking_My_Order_View::order_display_tracking_info', 5, 2 );

class Bring_Booking_My_Order_View {

  const ID = Fraktguiden_Helper::ID;
  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

  /**
   * Display tracking on Order/Mail etc.
   *
   * @param string $content
   * @param WC_Order $wc_order
   * @return string
   */
  static function order_display_tracking_info( $content, $wc_order ) {
    $order = new Bring_WC_Order_Adapter( $wc_order );

    if ( $order->is_booked() ) {
      $content .= '<div class="bring-order-details-booking">';
      $content .= '<strong>' . __( 'Your tracking number: ', 'bring-fraktguiden' ) . '</strong>';
      $content .= '<ul>';
      foreach ( $order->get_booking_consignments() as $consignment ) {
        $confirmation       = $consignment->confirmation;
        $consignment_number = $confirmation->consignmentNumber;
        $content .= '<li><a href="' . $confirmation->links->tracking . '">' . $consignment_number . '</a></li>';
      }
      $content .= '</ul>';
      $content .= '</div>';
    }

    return $content;
  }

}
