<?php

include_once 'class-wp-bring-response.php';

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
   * Add Authentication
   * @param @array $options
   */
  protected function add_authentication( $options ) {
    $mybring_api_uid = Fraktguiden_Helper::get_option( 'mybring_api_uid' );
    $mybring_api_key = Fraktguiden_Helper::get_option( 'mybring_api_key' );
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
    $mybring_api_uid = Fraktguiden_Helper::get_option( 'mybring_api_uid' );
    $mybring_api_key = Fraktguiden_Helper::get_option( 'mybring_api_key' );
    $customer_number = Fraktguiden_Helper::get_option( 'mybring_customer_number' );
    if ( $mybring_api_key && $mybring_api_uid && $customer_number ) {
      $url .= '&customerNumber='. $customer_number;
    }
    return $url;
  }
}
