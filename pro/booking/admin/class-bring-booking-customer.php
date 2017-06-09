<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Customer {
  //const CUSTOMERS_URL = 'http://drivdi-1551.rask3.raskesider.no/_public/fraktguiden/test-mybring-customers.php';
  const CUSTOMERS_URL = 'https://api.bring.com/booking/api/customers.json';

  /**
   * @return array
   */
  static function get_customer_numbers_formatted() {

    $args = [
        'headers' => [
            'Content-Type'       => 'application/json',
            'Accept'             => 'application/json',
            'X-MyBring-API-Uid'  => Bring_Booking::get_api_uid(),
            'X-MyBring-API-Key'  => Bring_Booking::get_api_key(),
            'X-Bring-Client-URL' => Bring_Booking::get_client_url(),
        ]
    ];

    $request  = new WP_Bring_Request();
    $response = $request->get( self::CUSTOMERS_URL, array(), $args );

    if ( $response->has_errors() ) {
      throw new Exception( $response->get_body() );
    }
    $result = [];
    $json = json_decode( $response->get_body() );
    foreach ( $json->customers as $customer ) {
      $result[$customer->customerNumber] = '[' . $customer->countryCode . '] ' . $customer->name;
    }
    return $result;
  }

}
