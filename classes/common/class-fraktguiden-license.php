<?php

class fraktguiden_license {

	const BASE_URL = 'https://bring.driv.digital/';

	protected static $instance;

	/**
	 * Get instance
	 * Singleton helper. Get the current instance of the class
	 * @return fraktguiden_license
	 */
	static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new fraktguiden_license();
		}
		return self::$instance;
	}

	/**
	 * Curl request
	 * @param  array $data GET parameters
	 * @return boolean
	 */
	public function curl_request( $data ) {
		$query_string = http_build_query( $data );
		// Get cURL resource
		$ch = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array( $ch, [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_URL            => self::BASE_URL .'?'. $query_string,
			CURLOPT_USERAGENT      => 'Bring plugin @ '. get_site_url()
		] );
		// Send the request & save response to $resp
		$content = curl_exec( $ch );
		// Get the HTTP code
		$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		// Close request to clear up some resources
		curl_close( $ch );
		// handle error; error output
		if ( $code !== 200 ) {
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
	 * @return boolean
	 */
	public function valid() {
		$valid = get_option( 'bring_fraktguiden_pro_valid_to' );
		if ( $valid && $valid < time() ) {
			return false;
		}
		return true;
	}

	/**
	 * Check License
	 */
	public function check_license() {
		$url = get_site_url();
		$url_info = parse_url( $url );
		if ( ! $url_info ) {
			$this->ping();
			return;
		}
		$data = $this->curl_request( [
			'action' => 'check_license',
			'domain' => $url_info[ 'host' ],
		] );
		if ( ! $data ) {
			return;
		}
		$valid = (int) @$data['data']['license']['valid_to'];
		if ( $valid && $valid > 0 ) {
			update_option( 'bring_fraktguiden_pro_valid_to', $valid );
		}
	}

	public function ping() {
		$url = get_site_url();
		$this->curl_request( [
			'action' => 'ping'
		] );
	}
}