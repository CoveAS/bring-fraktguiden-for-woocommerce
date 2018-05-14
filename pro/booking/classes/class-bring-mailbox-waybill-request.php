<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Mailbox_Waybill_Request {

  public $customer_number;
  public $package_numbers;

  /**
   * @param $customer_number
   * @param $package_numbers
   */
  function __construct( $customer_number, $package_numbers ) {
    $this->customer_number = $customer_number;
    $this->package_numbers = array_values( $package_numbers );
  }

  /**
   * Get Endpoint URL
   * @return string
   */
  public function get_endpoint_url() {
    return 'https://api.bring.com/order/to-mailbox/label/order';
  }

  /**
   * Post
   * @return WP_Bring_Response
   */
  public function post() {
    $request_data = [
      'timeout' => 60,
      'headers' => [
        'Content-Type'       => 'application/json',
        'Accept'             => 'application/json',
      ],
      'body' => json_encode( [
        'data' => [
          'type'       => 'label_orders',
          'attributes' => [
            'testIndicator'  => false,
            'customerNumber' => $this->customer_number,
            'packageNumbers' => $this->package_numbers,
          ]
        ]
      ] )
    ];
    $request = new WP_Bring_Request();
    return $request->post( $this->get_endpoint_url(), [], $request_data );
  }
}
