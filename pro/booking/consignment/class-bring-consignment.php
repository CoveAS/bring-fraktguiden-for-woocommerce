<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Consignment;

use BringFraktguidenPro\Booking\Bring_Booking_File;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Consignment class
 */
abstract class Bring_Consignment {

	/**
	 * Create from response
	 *
	 * @param object $response Response.
	 * @param int    $order_id Order ID.
	 *
	 * @return array
	 */
	public static function create_from_response( $response, $order_id ) {
		if ( ! $response ) {
			return [];
		}

		$body         = is_object( $response ) ? $response->body : $response['body'];
		$body         = json_decode( $body, 1 );
		$consignments = [];

		if ( isset( $body['consignments'] ) ) {
			// Build the booking consignments.
			foreach ( $body['consignments'] as $item ) {
				// Check for errors.
				if ( ! empty( $item['errors'] ) ) {
					// Return empty if any errors are found.
					return [];
				}

				$consignments[] = new Bring_Booking_Consignment( $order_id, $item );
			}
		}
		return $consignments;
	}

	/**
	 * Get label file
	 *
	 * @throws Exception
	 */
	public function get_label_file(): Bring_Booking_File
	{
		return new Bring_Booking_File( 'label', $this->get_consignment_number(), $this->get_label_url(), $this->order_id );
	}

	/**
	 * Download label
	 *
	 * @return void
	 */
	public function download_label() {
		$url = $this->get_label_url();
	}
}
