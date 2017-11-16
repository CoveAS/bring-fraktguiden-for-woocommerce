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
		$query_string = http_build_query( [
			'action' => 'check_license',
			'domain' => get_site_url(),
		] );
		$json = file_get_contents( self::BASE_URL .'?'. $query_string );
		if ( ! $json ) {
			return true;
		}
		$data = json_decode( $json, true );
		if ( ! $data ) {
			return true;
		}
		$valid = (int) $data['valid-to'];
		if ( $valid && $valid < time() ) {
			update_option( 'bring-fraktguiden-pro-valid-to', $valid );
			return true;
		}

		return false;
	}
}