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
	 * Create the return consignments of a response.
	 *
	 * Bring answers a booking that carried a returnProduct with a second
	 * consignment number and a second label, under returnConsignmentNumber and
	 * returnLinks. See doc/return-label.md.
	 *
	 * The return is kept apart from create_from_response(), because the
	 * customer facing order view lists every consignment it returns as a
	 * tracking number. A customer tracks the parcel they wait for, not the
	 * return they may never send.
	 *
	 * @param object|array $response Response.
	 * @param int          $order_id Order ID.
	 *
	 * @return Bring_Booking_Consignment[]
	 */
	public static function create_returns_from_response( $response, $order_id ) {
		$consignments = [];

		foreach ( self::confirmations( $response ) as $confirmation ) {
			if ( empty( $confirmation['returnConsignmentNumber'] ) ) {
				continue;
			}

			$consignments[] = new Bring_Booking_Consignment(
				$order_id,
				[
					'confirmation' => [
						'consignmentNumber' => $confirmation['returnConsignmentNumber'],
						'links'             => $confirmation['returnLinks'] ?? [],
						'dateAndTimes'      => [],
						'packages'          => [],
					],
				]
			);
		}

		return $consignments;
	}

	/**
	 * The confirmation of every consignment Bring accepted.
	 *
	 * @param object|array $response Response.
	 *
	 * @return array[]
	 */
	private static function confirmations( $response ) {
		if ( ! $response ) {
			return [];
		}

		$body = is_object( $response ) ? $response->body : $response['body'];
		$body = json_decode( $body, 1 );

		$confirmations = [];

		foreach ( $body['consignments'] ?? [] as $item ) {
			if ( ! empty( $item['errors'] ) || empty( $item['confirmation'] ) ) {
				continue;
			}

			$confirmations[] = $item['confirmation'];
		}

		return $confirmations;
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
