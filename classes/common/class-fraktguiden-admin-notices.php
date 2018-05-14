<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Class Fraktguiden_Helper
 *
 * Shared between regular and pro version
 */
class Fraktguiden_Admin_Notices {

  protected static $notices = [];

  /**
   * Init
   */
  static function init() {
    if ( defined( 'DISABLE_NAG_NOTICES' ) && DISABLE_NAG_NOTICES ) {
      return;
    }

    add_action( 'admin_notices', __CLASS__.'::render_notices' );
    add_action( 'wp_ajax_bring_dismiss_notice', __CLASS__.'::ajax_dismiss_notice' );

    if ( ! Fraktguiden_Helper::pro_activated( true ) ) {
      // If PRO is available and you've not activated it yet
      $message = __( 'Bring Fraktguiden PRO is now available, <a href="%s">Click here to upgrade to PRO.</a>', 'bring-fraktguiden' );
      $message = sprintf( $message, Fraktguiden_Helper::get_settings_url() );
      self::add_notice( 'pro_available', $message );
    } else if ( ! Fraktguiden_Helper::valid_license() ) {
      // If You've activated PRO and you've not bought a license
      $days = Fraktguiden_Helper::get_pro_days_remaining();
      if ( $days < 0 ) {
        // If you've used up all your trial days
        $message = __( 'Bring Fraktguiden PRO features have been deactivated.', 'bring-fraktguiden' );
      } else {
        // If you had a few days remaining
        $message = sprintf( __( 'The Bring Fraktguiden PRO license has not yet been activated. You have %s remaining before PRO features are disabled.', 'bring-fraktguiden' ), "$days " . _n( 'day', 'days', $days, 'bring-fraktguiden' ) );
      }
      $message = $message .'<br>'. Fraktguiden_Helper::get_pro_terms_link( __( 'Click here to buy a license', 'bring-fraktguiden' ) );
      self::add_notice( 'pro_available', $message, 'warning', ( $days >= 0 ) );
    }

    if ( ! Fraktguiden_Helper::get_option( 'from_zip' ) ) {
      // If you haven't set a default postcode for the origin of where packages are being sent
      $message = __( 'Bring requires a postcode to show where packages are being sent from. Please update your postcode on the <a href="%s">settings page.</a>', 'bring-fraktguiden' );
      $message = sprintf( $message, Fraktguiden_Helper::get_settings_url() );
      self::add_notice( 'from_zip_error', $message, 'error', false );
    }
  }

  /**
   * Update notice
   * @param   string  $key
   * @param   string  $message
   * @param   string  $type
   * @param   boolean $dismissable
   * @return  boolean
   */
  static function update_notice( $key, $message, $type = 'info', $dismissable = true ) {
    if ( ! $key ) {
      return false;
    }
    if ( ! in_array( $type, [ 'info', 'warning', 'error' ] ) ) {
      $type = 'info';
    }
    self::$notices[$key] = [
      'message'     => $message,
      'type'        => $type,
      'dismissable' => $dismissable,
    ];
    return true;
  }

  /**
   * Add notice
   * @param   string  $key
   * @param   string  $message
   * @param   string  $type
   * @param   boolean $dismissable
   * @return  boolean
   */
  static function add_notice( $key, $message, $type = 'info', $dismissable = true ) {
    if ( isset( self::$notices[ $key ] ) ) {
      return false;
    }
    return self::update_notice( $key, $message, $type, $dismissable );
  }

  /**
   * Remove notice
   * @param  string  $key
   * @return boolean
   */
  static function remove_notice( $key ) {
    if ( ! $key || ! isset( self::$notices[ $key ] ) ) {
      return false;
    }
    unset( self::$notices[$key] );
    return true;
  }

  static function get_dismissed_notices() {
    $dismissed = Fraktguiden_Helper::get_option( 'dismissed_notices' );
    if ( ! is_array( $dismissed ) ) {
      $dismissed = [];
    }
    return $dismissed;
  }

  /**
   * Dismiss notice
   * @param  string $key
   * @return boolean
   */
  static function dismiss_notice( $key ) {
    $dismissed = self::get_dismissed_notices();
    if ( ! $key ) {
      return false;
    }
    if ( in_array( $key, $dismissed ) ) {
      return true;
    }
    $dismissed[] = $key;
    Fraktguiden_Helper::update_option( 'dismissed_notices', $dismissed );
    return true;
  }

  /**
   * Recall notice
   * @param  string $key
   */
  static function recall_notice( $key ) {
    $dismissed = self::get_dismissed_notices();
    $notice_id = array_search( $key, $dismissed );
    if ( ! $key || false === $notice_id ) {
      return;
    }
    unset( $dismissed[ $notice_id ] );
    Fraktguiden_Helper::update_option( 'dismissed_notices', $dismissed );
  }

  /**
   * Render notices
   */
  static function render_notices() {
    $dismissed = self::get_dismissed_notices();
    foreach (self::$notices as $key => $notice ) {
      if ( $notice['dismissable'] && in_array( $key, $dismissed ) ) {
        continue;
      }
      $messages    = $notice['message'];
      $type        = $notice['type'];
      $dismissable = $notice['dismissable'];
      if ( is_string( $messages ) ) {
        $messages = [ $messages ];
      }
      require FRAKTGUIDEN_PLUGIN_PATH .'/includes/admin/pro-notices.php';
    }
  }

  /**
   * Ajax dismiss notice
   */
  static function ajax_dismiss_notice() {
    $notice_id = sanitize_key( $_POST['notice_id'] );
    if ( ! self::dismiss_notice( $notice_id ) ) {
      wp_send_json( [
        'code' => 'failure',
        'message' => $notice_id .' was not dismissed',
      ] );
    }
    wp_send_json( [
      'code' => 'success',
      'message' => $notice_id .' was dismissed',
    ] );
  }
}
