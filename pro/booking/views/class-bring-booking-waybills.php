<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Exit if accessed directly.
}

Bring_Booking_Waybills::setup();

/**
 * Bring_Booking_Waybills class
 */
class Bring_Booking_Waybills {

	/**
	 * Setup
	 *
	 * @return void
	 */
	public static function setup() {
		// Process waybill orders.
		add_action( 'admin_init', __CLASS__ . '::process_waybill_order' );
		add_filter( 'bulk_actions-edit-waybill', __CLASS__ . '::register_bulk_actions' );
		add_filter( 'handle_bulk_actions-edit-waybill', __CLASS__ . '::bulk_action_handler', 10, 3 );
		add_action( 'admin_notices', __CLASS__ . '::bulk_action_admin_notice' );
	}

	/**
	 * Process response
	 *
	 * @return void
	 */
	public static function process_waybill_order() {
		$consignment_numbers = filter_input( INPUT_POST, 'consignment_numbers' );

		if ( ! is_array( $consignment_numbers ) ) {
			return;
		}

		foreach ( $consignment_numbers as $customer_number => $consigments ) {

		}
	}

	/**
	 * Book mailbox consignment
	 *
	 * @param int|string $customer_number Customer number.
	 * @param array      $consignments    Consignments.
	 *
	 * @return void
	 */
	public static function book_mailbox_consignment( $customer_number, $consignments ) {
		require_once dirname( __DIR__ ) . '/classes/class-bring-mailbox-waybill-request.php';

		// Waybill booking does not have a test option.
		$request  = new Bring_Mailbox_Waybill_Request( $customer_number, array_keys( $consignments ) );
		$response = $request->post();
		$waybill  = null;

		// Save the waybill.
		if ( property_exists( $response, 'status' ) && 201 === $response->status ) {
			$data    = json_decode( $response->body, 1 );
			$waybill = new Bring_Waybill( $data );
			$waybill->save();
		}

		// Store the data for later display.
		var_dump(
			[
				'request'  => $request,
				'response' => $response,
				'waybill'  => $waybill,
			]
		);
		die;
	}

	/**
	 * Register bulk actions
	 *
	 * @param string $bulk_actions Bulk actions.
	 *
	 * @return array
	 */
	public static function register_bulk_actions( $bulk_actions ) {
		$bulk_actions['book_mailbox_consignments'] = __( 'Book consignments', 'book_mailbox_consignments' );
		return $bulk_actions;
	}

	/**
	 * Bulk action handler
	 *
	 * @param string $redirect_to Redirect to.
	 * @param string $doaction    Do action.
	 * @param array  $post_ids    Post IDs.
	 *
	 * @return string
	 */
	public static function bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'book_mailbox_consignments' !== $doaction ) {
			return $redirect_to;
		}

		foreach ( $post_ids as $post_id ) {
			echo esc_html( $post_id ) . PHP_EOL;

			$consignment_number = get_post_meta( $post_id, '_consignment_number', true );
			$customer_number    = get_post_meta( $post_id, '_customer_number', true );

			self::book_mailbox_consignment( $customer_number, $consignment_number );
		}

		die;
		$redirect_to = add_query_arg( 'waybills_booked', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	/**
	 * Bulk action admin notice
	 *
	 * @return [type] [description]
	 */
	public static function bulk_action_admin_notice() {
		$waybills_booked = filter_input( Fraktguiden_Helper::get_input_request_method(), 'waybills_booked' );

		if ( empty( $waybills_booked ) ) {
			return;
		}

		$emailed_count = intval( $waybills_booked );

		printf(
			'<div id="message" class="updated fade">' .
			esc_html(
				/* translators: %s: Number of mailbox parcels */
				_n(
					'Booked %s mailbox parcels.',
					'Booked %s mailbox parcels.',
					$emailed_count,
					'book_mailbox_consignments'
				)
			) . '</div>',
			$emailed_count // phpcs:ignore
		);
	}
}
