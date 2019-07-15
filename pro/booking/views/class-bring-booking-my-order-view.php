<?php
/**
 * Bring Booking - My order view
 */
class Bring_Booking_My_Order_View {

	const ID          = Fraktguiden_Helper::ID;
	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Display tracking on Order/Mail etc.
	 *
	 * @param string   $content
	 * @param WC_Order $order
	 * @return string
	 */
	static function order_display_tracking_info( $content, $order ) {
		$adapter = new Bring_WC_Order_Adapter( $order );
		// The order must be booked.
		if ( ! $adapter->is_booked() ) {
			return $content;
		}
		$consignments = $adapter->get_booking_consignments();
		$tracking     = false;
		foreach ( $consignments as $consignment ) {
			$tracking_link = $consignment->get_tracking_link();
			if ( $tracking_link ) {
				$tracking = true;
			}
		}
		// There has to be tracking to continue.
		if ( ! $tracking ) {
			return $content;
		}
		$content .= '<div class="bring-order-details-booking">';
		$content .= '<strong>' . __( 'Your tracking number: ', 'bring-fraktguiden' ) . '</strong>';
		$content .= '<ul>';
		foreach ( $consignments as $consignment ) {
			$consignment_number = $consignment->get_consignment_number();
			$content           .= sprintf(
				'<li><a href="%s">%s</a></li>',
				$consignment->get_tracking_link(),
				$consignment_number
			);
		}
		$content .= '</ul>';
		$content .= '</div>';
		return $content;
	}
}
