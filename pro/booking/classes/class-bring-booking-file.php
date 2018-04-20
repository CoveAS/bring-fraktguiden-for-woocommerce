<?php

/**
 *
 */
class Bring_Booking_File {
  static $allowed_groups = [ 'label', 'waybill' ];

  public $group;
  public $id;
  public $external_url;
  public $order_id;

  function __construct( $group, $id, $external_url, $order_id = null ) {
    if ( ! in_array( $group, static::$allowed_groups ) ) {
      throw new Exception( "Type, $group, must be one of ". implode( ', ', static::$allowed_groups ) );
    }
    $this->group        = $group;
    $this->id           = $id;
    $this->external_url = $external_url;
    $this->order_id     = $order_id;
  }

  /**
   * Get file extension
   * @return string
   */
  public function get_ext() {
    $ext = 'pdf';
    if ( $this->external_url ) {
      $urlinfo = parse_url( $this->external_url );
      $pathinfo = pathinfo( $urlinfo['path'] );
      return $pathinfo['extension'];
    }
    return $ext;
  }

  /**
   * Get directory
   * @return string
   */
  public function get_dir() {
    return wp_upload_dir()['basedir'] . '/bring_booking_labels';
  }

  /**
   * Get path
   * @return string
   */
  public function get_path() {
    return sprintf(
      '%s/%s',
      $this->get_dir(),
      $this->get_name()
    );
  }

  /**
   * Get url
   * @return string
   */
  public function get_url() {
    return sprintf(
      '%s/bring_booking_labels/%s',
      wp_upload_dir()['baseurl'],
      $this->get_name()
    );
  }

  /**
   * Get name
   * @return string
   */
  public function get_name() {
    return sprintf(
      '%s-%s.%s',
      $this->group,
      $this->id,
      $this->get_ext()
    );
  }

  /**
   * Exists
   * @return boolean
   */
  public function exists() {
    $path = $this->get_path();
    return file_exists( $path );
  }

  /**
   * Download
   * @return boolean
   */
  public function download() {
    $dir = $this->get_dir();
    static::init_dir( $dir );
    if ( ! static::validate_dir( $dir ) ) {
      return false;
    }
    $request = new WP_Bring_Request();
    $response = $request->get( $this->external_url, [], [
      'stream'   => true,
      'filename' => $this->get_path(),
    ] );
    return empty( $response->errors );
  }


  /**
   * Creates the download directory if not exists.
   * Adds a htaccess file in order to prevent direct download
   *
   * @return boolean
   */
  static function init_dir( $dir ) {
    static $protected = [];
    if ( isset( $protected[ $dir ] ) ) {
      return $protected[ $dir ];
    }
    $result = is_dir( $dir ) || wp_mkdir_p( $dir );
    if ( $result ) {
      // Create htaccess file
      if ( ! file_exists( $dir . '/.htaccess' ) )  {
        $result = file_put_contents( $dir . '/.htaccess', 'deny from all' );
      }
    }
    $protected[ $dir ] = $result;
    return $result;
  }

  /**
   * Validate dir
   * @param  string $dir
   * @return boolean
   */
  static function validate_dir( $dir ) {
    $error =  new WP_Error();
    $permission_message = __( 'Please check write permissions for yor uploads folder.', 'bring-fraktguiden' );
    $error->add( 'existence', sprintf( '<div class="notice error"><p><strong>%s</strong><br/>%s</p></div>', __( "Bring Fraktguiden could not create the folder 'uploads/bring_booking_labels'.", "bring-fraktguiden" ), $permission_message ) );
    $error->add( 'unwritable', sprintf( '<div class="notice error"><p><strong>%s</strong><br/>%s</p></div>', __( "Bring Fraktguiden could not write to 'uploads/bring_booking_labels'.", "bring-fraktguiden" ), $permission_message ) ) ;
    if ( ! file_exists( $dir ) ) {
      wp_die( $error->get_error_message( 'existence' ) );
    }
    if ( ! is_writable( $dir ) ) {
      wp_die( $error->get_error_message( 'unwritable' ) );
    }
    return true;
  }

  /**
   * Insert as attachment
   * @return integer Attachment ID
   */
  function insert_as_attachment() {
    $attachment = [
      'post_mime_type' => 'application/pdf',
      'guid' => $this->get_url(),
      'post_title' => $this->get_name(),
    ];
    // Save the data
    return wp_insert_attachment( $attachment, $this->get_path() );
  }

  /**
   * Get download link
   * @return string
   */
  public function get_download_url() {
    return admin_url( '?page=bring_download&order_ids=' . $this->order_id );
  }

  /**
   * Get download link
   * @return string
   */
  public function get_download_link() {
    return sprintf( '<a href="%s" target="_blank">%s</a>', $this->get_download_url(), $this->get_name() );
  }

}