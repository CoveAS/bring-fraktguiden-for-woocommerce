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
 * Bring_Booking_Request class
 */
class Bring_Booking_Request {

	const SCHEMA_VERSION = 1;

	const BOOKING_URL = 'https://api.bring.com/booking/api/booking';

	/**
	 * Request
	 *
	 * @var WP_Bring_Request
	 */
	private $request;

	/**
	 * Test mode
	 *
	 * @var boolean
	 */
	private $test_mode;

	/**
	 * Content type
	 *
	 * @var string
	 */
	private $content_type;

	/**
	 * Accept
	 *
	 * @var string
	 */
	private $accept;

	/**
	 * API UID
	 *
	 * @var string
	 */
	private $api_uid;

	/**
	 * API Key
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Client URL
	 *
	 * @var string
	 */
	private $client_url;

	/**
	 * Data
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Bring_Booking_Request constructor.
	 *
	 * @param WP_Bring_Request $request WP Bring Request.
	 */
	public function __construct( $request ) {
		$this->request = $request;
	}

	/**
	 * Set test mode
	 *
	 * @param boolean $test_mode Test mode.
	 *
	 * @return $this
	 */
	public function set_test_mode( $test_mode ) {
		$this->test_mode = $test_mode;

		return $this;
	}

	/**
	 * Get test mode
	 *
	 * @return boolean
	 */
	public function get_test_mode() {
		return $this->test_mode;
	}

	/**
	 * Set content type
	 *
	 * @param string $content_type Content type.
	 *
	 * @return $this
	 */
	public function set_content_type( $content_type ) {
		$this->content_type = $content_type;
		return $this;
	}

	/**
	 * Get content type.
	 *
	 * @return string
	 */
	public function get_content_type() {
		return $this->content_type;
	}

	/**
	 * Set accept
	 *
	 * @param string $accept Accept.
	 *
	 * @return $this
	 */
	public function set_accept( $accept ) {
		$this->accept = $accept;
		return $this;
	}

	/**
	 * Get accept
	 *
	 * @return string
	 */
	public function get_accept() {
		return $this->accept;
	}

	/**
	 * Set API UID
	 *
	 * @param string $api_uid API UID.
	 *
	 * @return $this
	 */
	public function set_api_uid( $api_uid ) {
		$this->api_uid = $api_uid;
		return $this;
	}

	/**
	 * Get API UID
	 *
	 * @return string
	 */
	public function get_api_uid() {
		return $this->api_uid;
	}

	/**
	 * Set API Key
	 *
	 * @param string $api_key API Key.
	 *
	 * @return $this
	 */
	public function set_api_key( $api_key ) {
		$this->api_key = $api_key;
		return $this;
	}

	/**
	 * Get API Key
	 *
	 * @return string
	 */
	public function get_api_key() {
		return $this->api_key;
	}


	/**
	 * Set data
	 *
	 * @param string $data Data.
	 *
	 * @return $this
	 */
	public function set_data( $data ) {
		$this->data = $data;
		return $this;
	}

	/**
	 * Get data
	 *
	 * @return string
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set client URL
	 *
	 * @param string $client_url Client URL.
	 *
	 * @return $this
	 */
	public function set_client_url( $client_url ) {
		$this->client_url = $client_url;
		return $this;
	}

	/**
	 * Get Client URL
	 *
	 * @return string
	 */
	public function get_client_url() {
		return $this->client_url;
	}

	/**
	 * Is valid
	 *
	 * @todo
	 *
	 * @return boolean
	 */
	public function is_valid() {
		return true;
	}

	/**
	 * Send
	 *
	 * @return WP_Bring_Response
	 */
	public function send() {
		$args = [
			'headers' => [
				'Content-Type'       => $this->get_content_type(),
				'Accept'             => $this->get_accept(),
				'X-MyBring-API-Uid'  => $this->get_api_uid(),
				'X-MyBring-API-Key'  => $this->get_api_key(),
				'X-Bring-Client-URL' => $this->get_client_url(),
			],
			'body'    => wp_json_encode( $this->get_data() ),
		];

		$response = $this->request->post( self::BOOKING_URL, array(), $args );

		if ( 200 !== $response->get_status_code() ) {
			// var_dump( $response->get_body() );die;
		}

		return $response;
	}

}
