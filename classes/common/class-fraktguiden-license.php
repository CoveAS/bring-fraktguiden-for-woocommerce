<?php

class fraktguiden_license {
	const BASE_URL = 'http://bring.driv.digital/';

	protected static $instance;
	static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new fraktguiden_license();
		}
		return self::$instance;
	}
	public function valid() {
		$valid = get_option( 'bring-fraktguiden-pro-valid-to' );
		if ( $valid && $valid < time() ) {
			return true;
		}

		$url = get_site_url();
		$url_info = parse_url( $url );
		if ( ! $url_info ) {
			$query_string = http_build_query( [
				'action' => 'ping',
				'domain' => $url,
			] );
		} else {
			$query_string = http_build_query( [
				'action' => 'check_license',
				'domain' => $url_info[ 'host' ],
			] );
		}

		// Get cURL resource
		$ch = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array( $ch, [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => self::BASE_URL .'?'. $query_string,
			CURLOPT_USERAGENT => 'Bring plugin @ '. get_site_url()
		] );
		// Send the request & save response to $resp
		$json = curl_exec( $ch );
		// Close request to clear up some resources

		// handle error; error output
		if( curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200 ) {
			return true;
		}

		curl_close( $ch );

		if ( ! $json ) {
			return true;
		}

		$data = json_decode( $json, true );
		if ( empty( $data ) ) {
			return true;
		}

		$valid = (int) @$data['data']['license']['valid_to'];

		if ( $valid && $valid > time() ) {
			update_option( 'bring-fraktguiden-pro-valid-to', $valid );
			return true;
		}

		return false;
	}
}