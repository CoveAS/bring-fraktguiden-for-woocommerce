<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Exception;
use WP_Bring_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Customer class
 */
class Bring_Booking_Customer {

	const CUSTOMERS_URL = 'https://api.bring.com/booking/api/customers.json';

	/**
	 * Get customer numbers formatted
	 *
	 * @throws Exception Exception.
	 * @return array
	 */
	public static function get_customer_numbers_formatted(): array
	{
		static $result = [];
		if (empty($result)) {
			$result = self::get_customer_numbers_from_api();
		}
		return $result;
	}

	/**
	 * @throws Exception
	 */
	private static function get_customer_numbers_from_api(): array
	{
		$args = [
			'headers' => [
				'Content-Type'       => 'application/json',
				'Accept'             => 'application/json',
				'X-MyBring-API-Uid'  => Bring_Booking::get_api_uid(),
				'X-MyBring-API-Key'  => Bring_Booking::get_api_key(),
				'X-Bring-Client-URL' => Fraktguiden_Helper::get_client_url(),
			],
		];

		$request  = new WP_Bring_Request();
		$response = $request->get( self::CUSTOMERS_URL, array(), $args );

		if ( $response->has_errors() ) {
			throw new Exception( implode("\n", $response->get_errors()) );
		}

		$result = [];
		$json   = json_decode( $response->get_body() );

		if (is_null($json)) {
			return $result;
		}

		if ($response->status_code === 500 && isset($json->message)) {
			throw new Exception(
				esc_html_e(
					'The mybring API return with an unknown error. Please contact mybring.com support for help. The error message given by the API:',
					'bring-fraktguiden-for-woocommerce'
				) . wp_kses($json->message, [])
			);
		}

		foreach ( $json->customers as $customer ) {
			$result[ $customer->customerNumber ] = '[' . $customer->countryCode . '] ' . $customer->name; // phpcs:ignore
		}

		return $result;
	}
}
