<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden;

use WP_Error;

/**
 * Postcode_Validation class
 */
class Postcode_Validation {

	/**
	 * Setup.
	 */
	public static function setup() {
		add_filter( 'woocommerce_validate_postcode', __CLASS__ . '::validate_postcode', 10, 3 );
	}

	/**
	 * Get postcode information.
	 * https://developer.bring.com/api/postal-code/
	 *
	 * @param string $postcode Postcode.
	 * @param string $country  ISO2 country code.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	public static function get_postcode_information( string $postcode, string $country ) {
		$params = [
			'body' => [
				'clientUrl' => get_site_url(),
				'country'   => $country,
				'pnr'       => $postcode,
			],
		];

		return wp_remote_get( 'https://api.bring.com/shippingguide/api/postalCode.json', $params );
	}

	/**
	 * Validate postcode.
	 *
	 * @param boolean $valid    Valid postcode.
	 * @param string  $postcode Postcode.
	 * @param string  $country  ISO2 country code.
	 *
	 * @return boolean Valid postcode.
	 */
	public static function validate_postcode( $valid, $postcode, $country ): bool
	{

		$valid_countries = [ 'NO' ];

		if ( ! $valid || ! in_array( $country, $valid_countries, true ) ) {
			return $valid;
		}

		$response = self::get_postcode_information( $postcode, $country );

		if ( is_wp_error( $response ) ) {
			return preg_match( '/^\d{1,4}$/', $postcode );
		}

		if ( empty( $response['response']['code'] ) || empty( $response['body'] ) ) {
			return preg_match( '/^\d{1,4}$/', $postcode );
		}

		if ( 200 !== $response['response']['code'] ) {
			return preg_match( '/^\d{1,4}$/', $postcode );
		}

		$data = json_decode( $response['body'], true );

		return $data['valid'];
	}
}
