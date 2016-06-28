<?php

class WP_Bring_Response {

  const HTTP_STATUS_OK = 200;
  const HTTP_STATUS_CREATED = 201;
  const HTTP_STATUS_NO_CONTENT = 204;
  const HTTP_STATUS_BAD_REQUEST = 400;
  const HTTP_STATUS_UNAUTHORIZED = 401;
  const HTTP_STATUS_NOT_FOUND = 404;
  const HTTP_STATUS_UNPROCESSABLE_ENTITY = 422;

  /** @var array|WP_Error */
  private $response;

  /** @var  int */
  public $status_code;
  /** @var  array */
  public $headers;
  /** @var  string */
  public $body;
  /** @var  bool|string */
  public $errors = [ ];

  /**
   * WC_Bring_Response constructor.
   * @param array|WP_Error $response
   */
  public function __construct( $response ) {
    $this->response = $response;
    $this->process();
  }

  /**
   * @return int
   */
  public function get_status_code() {
    return $this->status_code;
  }

  /**
   * @return array
   */
  public function get_headers() {
    return $this->headers;
  }

  /**
   * @return string
   */
  public function get_body() {
    return $this->body;
  }

  /**
   * @return string
   */
  public function to_array() {
    return [
        'status_code' => $this->status_code,
        'headers'     => $this->headers,
        'body'        => $this->body,
        'errors'      => $this->errors
    ];
  }

  /**
   * @return bool|array
   */
  public function get_errors() {
    return $this->errors;
  }

  /**
   * @return bool
   */
  public function has_errors() {
    return ! empty( $this->errors );
  }

  /**
   * @return array
   */
  protected function process() {

    if ( is_wp_error( $this->response ) ) {
      return $this->handle_error_response();
    }

    // Note Bring reports 400 for unauthorised requests.

    $status_code = wp_remote_retrieve_response_code( $this->response );
    switch ( $status_code ) {
      case self::HTTP_STATUS_OK:
      case self::HTTP_STATUS_CREATED:
        return $this->handle_response();
        break;

      case self::HTTP_STATUS_NO_CONTENT:
      case self::HTTP_STATUS_NOT_FOUND:
      case self::HTTP_STATUS_BAD_REQUEST:
      case self::HTTP_STATUS_UNPROCESSABLE_ENTITY:
      case self::HTTP_STATUS_UNAUTHORIZED:
        return $this->handle_error_response();

      default:
        return $this->response;
    }
  }

  /**
   * @return array
   */
  protected function handle_response() {
    $this->status_code = wp_remote_retrieve_response_code( $this->response );
    $this->headers     = wp_remote_retrieve_headers( $this->response );
    $this->body        = wp_remote_retrieve_body( $this->response );
  }

  /**
   * @return array
   */
  protected function handle_error_response() {
    $this->handle_response();

    if ( is_wp_error( $this->response ) ) {
      foreach ( $this->response->get_error_messages() as $message ) {
        $this->errors[] = "WP_Error: " . $message;
      }
    }

    // Add HTTP code name
    $class = new ReflectionClass( __CLASS__ );
    foreach ( $class->getConstants() as $key => $val ) {
      if ( $val == $this->status_code ) {
        $this->errors[] = 'HTTP ' . $val . ': ' . $key;
        break;
      }
    }
  }

}