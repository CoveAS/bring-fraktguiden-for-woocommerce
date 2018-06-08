<?php

include_once 'class-wp-bring-response.php';

/**
 * Bring Request
 *
 * @todo make Bring_Booking_Request extend this class
 */
class WP_Bring_Request {

  /** @var array WP_HTTP args */
  protected $default_options = [
      'timeout'    => 15,
      'user-agent' => 'bring-fraktguiden-for-woocommerce/'. Bring_Fraktguiden::VERSION .' (https://wordpress.org/plugins/bring-fraktguiden-for-woocommerce) PHP'
  ];

  public function __construct() {
    /**/
  }

  /**
   * Get
   *
   * @param string $url The url
   * @param array $params Associative array representing url parameters
   * @param array $options WP_HTTP args
   * @return WP_Bring_Response
   */
  public function get( $url, $params = [ ], $options = [ ] ) {
    $url     = $this->build_url( $url, $params );
    $options = $this->merge_options( $options );
    $options = $this->add_authentication( $options );
    $url     = $this->add_customer_number( $url );
    $result  = wp_remote_get( $url, $options );
    return new WP_Bring_Response( $result );
  }

  /**
   * Post
   *
   * Looks like this is never used. @TODO: deprecate this function
   *
   * @param string $url The url
   * @param array $params Associative array representing url parameters
   * @param array $options WP_HTTP args
   * @return WP_Bring_Response
   */
  public function post( $url, $params = [ ], $options = [ ] ) {
    $url     = $this->build_url( $url, $params );
    $options = $this->merge_options( $options );
    $options = $this->add_authentication( $options );
    $url     = $this->add_customer_number( $url );
    $result  = wp_remote_post( $url, $options );
    return new WP_Bring_Response( $result );
  }

  /**
   * @param string $url
   * @param array $params
   * @return string
   */
  protected function build_url( $url, $params = [ ] ) {
    $result = $url;
    $result .= ( strpos( $url, '?' ) === false ) ? '?' : '&';
    $parameters = http_build_query( $params );
    $result .= $parameters;

    return esc_url_raw( $result );
  }

  /**
   * @param array $options WP_HTTP args
   * @return array
   */
  protected function merge_options( $options ) {
    return array_merge( $this->default_options, $options );
  }

  /**
   * Get Var
   * Get the field value from either the POST or the saved value
   * @param  string $key
   * @return string
   */
  protected function get_var( $key ) {
    if ( isset( $_POST[ 'woocommerce_bring_fraktguiden_'. $key ] ) ) {
      return $_POST[ 'woocommerce_bring_fraktguiden_'. $key ];
    }
    return Fraktguiden_Helper::get_option( $key );
  }

  /**
   * Add Authentication
   * @param @array $options
   */
  protected function add_authentication( $options ) {
    $mybring_api_uid = $this->get_var( 'mybring_api_uid' );
    $mybring_api_key = $this->get_var( 'mybring_api_key' );

    if ( $mybring_api_key && $mybring_api_uid ) {
      $options['headers']['X-MyBring-API-Uid']  = $mybring_api_uid;
      $options['headers']['X-MyBring-API-Key']  = $mybring_api_key;
      $options['headers']['X-Bring-Client-URL'] = $_SERVER['HTTP_HOST'];
    }
    return $options;
  }

  /**
   * Add Customer Number
   * @param @array $options [description]
   */
  protected function add_customer_number( $url ) {
    $mybring_api_uid = $this->get_var( 'mybring_api_uid' );
    $mybring_api_key = $this->get_var( 'mybring_api_key' );
    $customer_number = $this->get_var( 'mybring_customer_number' );
    if ( $mybring_api_key && $mybring_api_uid && $customer_number ) {
      if ( '?' != substr( $url, -1 ) ) {
        $url .= '&';
      }
      $url .= 'customerNumber='. $customer_number;
    }
    return $url;
  }
}
