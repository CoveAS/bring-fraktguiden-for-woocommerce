<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Fraktguiden_License class
 */
class Fraktguiden_License {

	const BASE_URL = 'https://bringfraktguiden.no/license-check.php';

	/**
	 * An instance of this class
	 *
	 * @var Fraktguiden_License
	 */
	protected static $instance;

	/**
	 * Get instance
	 * Singleton helper. Get the current instance of the class
	 *
	 * @return Fraktguiden_License
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new Fraktguiden_License();
		}
		return self::$instance;
	}

	/**
	 * Curl request
	 *
	 * @param array $data GET parameters.
	 *
	 * @return boolean
	 */
	public function curl_request( $data ) {
		$query_string = http_build_query( $data );

		// Get cURL resource.
		$handle = curl_init();

		// Set some options - we are passing in a useragent too here.
		curl_setopt_array(
			$handle,
			[
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_URL            => self::BASE_URL . '?' . $query_string,
				CURLOPT_USERAGENT      => 'Bring plugin @ ' . get_site_url(),
			]
		);

		// Send the request & save response to $resp.
		$content = curl_exec( $handle );

		// Get the HTTP code.
		$code = curl_getinfo( $handle, CURLINFO_HTTP_CODE );

		// Close request to clear up some resources.
		curl_close( $handle );

		// handle error; error output.
		if ( 200 !== $code ) {
			return false;
		}

		$data = json_decode( $content, true );

		if ( empty( $data ) ) {
			return false;
		}

		return $data;
	}

	/**
	 * Valid
	 *
	 * Check if the bring license is valid or not
	 *
	 * @return boolean
	 */
	public function valid() {
		$valid = get_option( 'bring_fraktguiden_pro_valid_to' );

		if (! ctype_digit($valid)) {
			return false;
		}
		if ($valid && $valid < time() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check the license
	 *
	 * @return void
	 */
	public function check_license() {
		$url      = get_site_url();
		$url_info = wp_parse_url( $url );

		if ( ! $url_info ) {
			$this->ping();
			return;
		}
		$date_utc = new \DateTime( '-2 months', new \DateTimeZone( 'UTC' ) );
		$date_then = (int) $date_utc->format( 'Ymd' );

		$count = get_option( 'bring_fraktguiden_booking_count', [] );

		if ( ! is_array( $count ) ) {
			$count = [];
		}

		$changed = false;
		foreach ( $count as $date => $amount ) {
			if ( $date_then > $date ) {
				$changed = true;
				unset( $count[ $date ]);
			}
		}

		if ( $changed ) {
			update_option( 'bring_fraktguiden_booking_count', $count, false );
		}

		$data = $this->curl_request(
			[
				'action'        => 'check_license',
				'domain'        => $url_info['host'],
				'booking_count' => $count,
			]
		);

		if ( ! $data ) {
			return;
		}

		if ( ! isset( $data['data']['license']['valid_to'] ) ) {
			return;
		}

		$valid = (int) $data['data']['license']['valid_to'];

		if ( $valid > 0 ) {
			update_option( 'bring_fraktguiden_pro_valid_to', $valid );
		}
	}

	/**
	 * Ping the licensing server
	 *
	 * @return void
	 */
	public function ping() {
		$this->curl_request(
			[
				'action' => 'ping',
			]
		);
	}
}
