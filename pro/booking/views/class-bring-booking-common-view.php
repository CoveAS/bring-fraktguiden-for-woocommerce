<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Views;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Bring_Booking;
use BringFraktguidenPro\Booking\Bring_Booking_Url;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Common_View class
 */
class Bring_Booking_Common_View {

	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Booking label
	 *
	 * @param  boolean $plural Plural.
	 * @return string
	 */
	public static function booking_label( $plural = false ) {
		$label = sprintf( '%s', ( true === $plural ) ? __( 'Bring - Submit Consignments', 'bring-fraktguiden-for-woocommerce' ) : __( 'Submit Consignment', 'bring-fraktguiden-for-woocommerce' ) );
		return $label . ( Bring_Booking::is_test_mode() ? ' - ' . __( 'Test mode', 'bring-fraktguiden-for-woocommerce' ) : '' );
	}

	/**
	 * Create status icon
	 *
	 * @param array $status Status.
	 * @param int   $size   Size.
	 * @return string
	 */
	public static function create_status_icon( $status, $size = 96 ) {
		return '<span class="dashicons ' . $status['icon'] . ' bring-booking-status-icon" style="font-size: ' . $size . 'px; width: ' . $size . 'px; height: ' . $size . 'px"></span>';
	}

	/**
	 * Check if this is a second step of booking
	 *
	 * @return boolean
	 */
	public static function is_step2() {
		return 2 === (int) filter_input( INPUT_GET, 'booking_step' );
	}

	/**
	 * Get booking status info
	 */
	public static function get_booking_status_info( Bring_WC_Order_Adapter $order ): ?array
	{
		if (! $order->has_bring_shipping_methods()) {
			return null;
		}

		$result = [
			'text' => __( 'Book now', 'bring-fraktguiden-for-woocommerce' ),
			'href' => new Bring_Booking_Url( $order ),
			'action' => 'bring-book-orders',
			'ids' => [$order->order->get_id()],
			'icon' => 'dashicons-minus',
		];

		// A cancelled order ships nothing, so it offers no booking.
		if ( ! $order->is_booked() && $order->order->has_status( 'cancelled' ) ) {
			return null;
		}

		$labels_url         = Bring_Booking_Labels::create_download_url( $order->order->get_id() );
		if ( $order->is_booked() ) {
			$result = [
				'text' => __( 'Print label', 'bring-fraktguiden-for-woocommerce' ),
				'href' => $labels_url,
				'icon' => 'dashicons-yes',
			];
		}

		if ( $order->has_booking_errors() ) {
			$result = [
				'text' => __( 'Failed', 'bring-fraktguiden-for-woocommerce' ),
				'href' => '',
				'icon' => 'dashicons-warning',
			];
		}

		return $result;
	}
}
