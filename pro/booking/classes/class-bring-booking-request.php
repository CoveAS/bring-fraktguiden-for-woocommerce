<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Request {
  const SCHEMA_VERSION = 1;
  // const BOOKING_URL = 'http://drivdi-1551.rask3.raskesider.no/_public/fraktguiden/test-mybring-booking.php';
  // const BOOKING_URL = 'http://drivdi-1551.rask3.raskesider.no/_public/fraktguiden/test-mybring-booking-errors.php';
  const BOOKING_URL = 'https://api.bring.com/booking/api/booking';

  /** @var WP_Bring_Request */
  private $request;
  /** @var bool */
  private $test_mode;
  /** @var string */
  private $content_type;
  /** @var string */
  private $accept;
  /** @var string */
  private $api_uid;
  /** @var string */
  private $api_key;
  /** @var string */
  private $client_url;
  /** @var array */
  private $data = [ ];

  /**
   * Bring_Booking_Request constructor.
   * @param WP_Bring_Request $request
   */
  public function __construct( $request ) {
    $this->request = $request;
  }

  /**
   * @param bool $test_mode
   * @return $this
   */
  public function set_test_mode( $test_mode ) {
    $this->test_mode = $test_mode;
    return $this;
  }

  /**
   * @return bool
   */
  public function get_test_mode() {
    return $this->test_mode;
  }

  /**
   * @param string $content_type
   * @return $this
   */
  public function set_content_type( $content_type ) {
    $this->content_type = $content_type;
    return $this;
  }

  /**
   * @return string
   */
  public function get_content_type() {
    return $this->content_type;
  }

  /**
   * @param string $accept
   * @return $this
   */
  public function set_accept( $accept ) {
    $this->accept = $accept;
    return $this;
  }

  /**
   * @return string
   */
  public function get_accept() {
    return $this->accept;
  }

  /**
   * @param string $api_uid
   * @return $this
   */
  public function set_api_uid( $api_uid ) {
    $this->api_uid = $api_uid;
    return $this;
  }

  /**
   * @return string
   */
  public function get_api_uid() {
    return $this->api_uid;
  }

  /**
   * @param string $api_key
   * @return $this
   */
  public function set_api_key( $api_key ) {
    $this->api_key = $api_key;
    return $this;
  }

  /**
   * @return string
   */
  public function get_api_key() {
    return $this->api_key;
  }


  /**
   * @param string $api_key
   * @return $this
   */
  public function set_data( $data ) {
    $this->data = $data;
    return $this;
  }

  /**
   * @return string
   */
  public function get_data() {
    return $this->data;
  }

  /**
   * @param string $client_url
   * @return $this
   */
  public function set_client_url( $client_url ) {
    $this->client_url = $client_url;
    return $this;
  }

  /**
   * @return string
   */
  public function get_client_url() {
    return $this->client_url;
  }

  /**
   * @todo
   * @return bool
   */
  public function is_valid() {
    return true;
  }

  /**
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
        'body' => json_encode( $this->get_data() )
    ];
    $response = $this->request->post( self::BOOKING_URL, array(), $args );
    if( $response->get_status_code() != 200 ) {
      // var_dump( $response->get_body() );die;
    }
    return $response;
  }

}
