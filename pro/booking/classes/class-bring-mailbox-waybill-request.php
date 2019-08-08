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
 * Bring_Mailbox_Waybill_Request class
 */
class Bring_Mailbox_Waybill_Request {

	/**
	 * Customer number
	 *
	 * @var string
	 */
	public $customer_number;

	/**
	 * Package numbers
	 *
	 * @var array
	 */
	public $package_numbers;

	/**
	 * Construct
	 *
	 * @param string $customer_number Customer number.
	 * @param array  $package_numbers Package numbers.
	 */
	public function __construct( $customer_number, $package_numbers ) {
		$this->customer_number = $customer_number;
		$this->package_numbers = array_values( $package_numbers );
	}

	/**
	 * Get Endpoint URL
	 *
	 * @return string
	 */
	public function get_endpoint_url() {
		return 'https://api.bring.com/order/to-mailbox/label/order';
	}

	/**
	 * Post
	 *
	 * @return WP_Bring_Response
	 */
	public function post() {
		$request_data = [
			'timeout' => 60,
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			],
			'body'    => wp_json_encode(
				[
					'data' => [
						'type'       => 'label_orders',
						'attributes' => [
							'testIndicator'  => false,
							'customerNumber' => $this->customer_number,
							'packageNumbers' => $this->package_numbers,
						],
					],
				]
			),
		];

		$request = new WP_Bring_Request();

		return $request->post( $this->get_endpoint_url(), [], $request_data );
	}
}
