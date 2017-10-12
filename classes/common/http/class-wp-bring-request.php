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
    $u    = $this->build_url( $url, $params );
    $opts = $this->merge_options( $options );
    $res  = wp_remote_get( $u, $opts );
    return new WP_Bring_Response( $res );
  }

  /**
   * @param string $url The url
   * @param array $params Associative array representing url parameters
   * @param array $options WP_HTTP args
   * @return WP_Bring_Response
   */
  public function post( $url, $params = [ ], $options = [ ] ) {
    $u    = $this->build_url( $url, $params );
    $opts = $this->merge_options( $options );
    $res  = wp_remote_post( $u, $opts );
    return new WP_Bring_Response( $res );
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

}
