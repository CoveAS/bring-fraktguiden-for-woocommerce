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
        $consignment_number = $consignment->get_consignment_number();
        $content .= sprintf(
          '<li><a href="%s">%s</a></li>',
          $consignment->get_tracking_link(),
          $consignment_number
        );
      }
      $content .= '</ul>';
      $content .= '</div>';
    }

    return $content;
  }

}
