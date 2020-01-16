<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Common_View class
 */
class Bring_Booking_Common_View {

	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Render customer selector
	 *
	 * @param  string                 $name    Select field name.
	 * @param  Bring_WC_Order_Adapter $adapter Order adapter.
	 * @return void
	 */
	public static function render_customer_selector( $name = '_bring-customer-number', $adapter = null ) {
		$customer_number = null;

		$shipping_items = [];

		if ( ! empty( $adapter ) ) {
			$adapter->get_fraktguiden_shipping_items();
		}

		if ( ! empty( $shipping_items ) ) {
			$shipping_item   = reset( $shipping_items );
			$method          = new WC_Shipping_Method_Bring_Pro( $shipping_item->get_instance_id() );
			$customer_number = $method->get_option( 'mybring_customer_number' );
		}

		try {
			$customers = Bring_Booking_Customer::get_customer_numbers_formatted();
		} catch ( Exception $e ) {
			printf( '<p class="error">%s</p>', esc_html( $e->getMessage() ) );
			return;
		}

		echo '<div class="bring-customer-numbers">';

		// Set default customer number as fallback in case shipping item is missing or no match with customers.
		if ( ! array_key_exists( $customer_number, $customers ) ) {
			$customer_number = Fraktguiden_Helper::get_option( 'mybring_customer_number' );
		}

		foreach ( $customers as $key => $val ) {
			$checked_attr = '';

			if ( $customer_number === $key ) {
				$checked_attr = ' checked="checked"';
			}

			echo '<label class="bring-customer-label"><input type="radio" name="' . esc_attr( $name ) . '" value="' . esc_attr( $key ) . '"' . $checked_attr . '><span class="bring-customer-name">' . esc_html( $val ) . '</span><span class="bring-customer-number">' . $key . '</span></label>'; // phpcs:ignore
		}

		echo '</div>';
	}

	/**
	 * Render shipping date time
	 *
	 * @param  string $name Input field name.
	 * @return void
	 */
	public static function render_shipping_date_time( $name = '_bring-shipping-date' ) {
		$shipping_date = Bring_Booking::create_shipping_date();
		echo '<input type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $shipping_date['date'] ) . '"  maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" style="width:12.5em">@';
		echo '<input type="text" name="' . esc_attr( $name ) . '-hour" value="' . esc_attr( $shipping_date['hour'] ) . '" maxlength="2" placeholder="' . esc_attr( __( 'hh', 'bring-fraktguiden-for-woocommerce' ) ) . '" style="width:3em;text-align:center">:';
		echo '<input type="text" name="' . esc_attr( $name ) . '-minutes" value="' . esc_attr( $shipping_date['minute'] ) . '" maxlength="2" placeholder="' . esc_attr( __( 'mm', 'bring-fraktguiden-for-woocommerce' ) ) . '" style="width:3em;text-align:center">';
	}

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
	 *
	 * @param Bring_WC_Order_Adapter $order Order.
	 * @return string
	 */
	public static function get_booking_status_info( $order ) {
		$result = [
			'text' => __( 'No', 'bring-fraktguiden-for-woocommerce' ),
			'icon' => 'dashicons-minus',
		];

		if ( self::is_step2() ) {
			$result = [
				'text' => __( 'In progress', 'bring-fraktguiden-for-woocommerce' ),
				'icon' => '',
			];
		}

		if ( $order->is_booked() ) {
			$result = [
				'text' => __( 'Booked', 'bring-fraktguiden-for-woocommerce' ),
				'icon' => 'dashicons-yes',
			];
		}

		if ( $order->has_booking_errors() ) {
			$result = [
				'text' => __( 'Failed', 'bring-fraktguiden-for-woocommerce' ),
				'icon' => 'dashicons-warning',
			];
		}

		return $result;
	}
}
