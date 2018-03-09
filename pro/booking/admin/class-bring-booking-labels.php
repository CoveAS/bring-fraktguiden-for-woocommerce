<?php
if ( ! defined( 'ABSPATH' ) ) {
  die; // Exit if accessed directly
}

// Create a menu item for PDF download.
add_action( 'admin_menu', 'Bring_Booking_Labels::open_pdfs' );

class Bring_Booking_Labels {

  /**
   * @return string
   */
  static function get_local_dir() {
    return wp_upload_dir()['basedir'] . '/bring_booking_labels';
  }

  /**
   * Creates the download directory if not exists.
   * Adds a htaccess file in order to prevent direct download
   *
   * @return bool|int
   */
  static function init_local_dir() {
    $dir    = self::get_local_dir();
    $result = is_dir( $dir ) || wp_mkdir_p( $dir );
    if ( $result ) {
      // Create htaccess file
      $result = file_exists( $dir . '/.htaccess' );
      if ( ! $result ) {
        $result = file_put_contents( $dir . '/.htaccess', 'deny from all' );
      }
    }
    return $result;
  }

  /**
   * @param string $order_ids Comma separated string with order ids.
   *
   * @return string
   */
  static function create_download_url( $order_ids ) {
    return admin_url( '?page=bring_labels&order_ids=' . $order_ids );
  }

  /**
   * @param $order_id
   * @param $consignment_number
   * @return string
   */
  static function get_file_path( $order_id, $consignment_number ) {
    return self::get_local_dir() . '/labels-' . $order_id . '-' . $consignment_number . '.pdf';
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function download_to_local( $order ) {

    if ( ! $order->has_booking_consignments() ) {
      return;
    }

    self::init_local_dir();

    $consignments = $order->get_booking_consignments();
    foreach ( $consignments as $consignment ) {
      $consignment_number = $consignment->confirmation->consignmentNumber;
      $source             = $consignment->confirmation->links->labels;
      $destination        = self::get_file_path( $order->order->get_id(), $consignment_number );
      $desitnation_dir    = dirname( $destination );

      $error =  new WP_Error();
      $permission_message = __( 'Please check write permissions for yor uploads folder.', 'bring-fraktguiden' );
      $error->add( 'existence', sprintf( '<div class="notice error"><p><strong>%s</strong><br/>%s</p></div>', __( "Bring Fraktguiden could not create the folder 'uploads/bring_booking_labels'.", "bring-fraktguiden" ), $permission_message ) );
      $error->add( 'unwritable', sprintf( '<div class="notice error"><p><strong>%s</strong><br/>%s</p></div>', __( "Bring Fraktguiden could not write to 'uploads/bring_booking_labels'.", "bring-fraktguiden" ), $permission_message ) ) ;

      if ( ! file_exists( $desitnation_dir ) ) {
        wp_die( $error->get_error_message( 'existence' ) );
      }
      if ( ! is_writable( $desitnation_dir ) ) {
        wp_die( $error->get_error_message( 'unwritable' ) );
      }

      $request = new WP_Bring_Request();

      $request->get( $source, array(), array(
          'stream'   => true,
          'filename' => $destination
      ) );
    }
  }

  static function open_pdfs() {
    add_submenu_page( null, 'Download', 'Download', 'manage_woocommerce', 'bring_labels', __CLASS__.'::merge_pdfs' );
  }

  /**
   * Check if current user role can
   * access Bring labels
   */
  static function check_cap() {
    $current_user = wp_get_current_user();

    // ID 0 is a not an user.
    if ( $current_user->ID == 0 ) {
      return false;
    }

    $required_caps = apply_filters( 'bring_booking_capabilities' , [
      'administrator',
      'manage_woocommerce',
      'warehouse_team',
      'bring_labels'
    ]);

    // Check user against required roles/caps
    foreach ( $required_caps as $cap ) {
      if ( user_can( $current_user->ID, $cap ) ) {
        return true;
      }
    }
    return false;
  }

  static function merge_pdfs() {
    if ( ! isset( $_GET['order_ids'] ) || $_GET['order_ids'] == '' ) {
      return;
    }

    // Check if user can see the labels
    if ( ! self::check_cap() ) {
      wp_die(
        sprintf(
          '<div class="notice error"><p><strong>%s</strong></p></div>',
          __( "Sorry, Labels are only available for Administrators, Warehouse Teams and Store Managers. Please contact the administrator to enable access.", 'bring-fraktguiden' ) ),
        __( 'Insufficient permissions', 'bring-fraktguiden' )
      );
    }

    $order_ids      = explode( ',', $_GET['order_ids'] );
    $files_to_merge = [ ];

    foreach ( $order_ids as $order_id ) {
      $order = new Bring_WC_Order_Adapter( new WC_Order( $order_id ) );
      if ( ! $order->has_booking_consignments() ) {
        continue;
      }

      $consignments = $order->get_booking_consignments();
      foreach ( $consignments as $consignment ) {
        $confirmation       = $consignment->confirmation;
        $consignment_number = $confirmation->consignmentNumber;
        $file_path = Bring_Booking_Labels::get_file_path( $order->order->get_id(), $consignment_number );
        if ( ! file_exists( $file_path ) ) {
          self::download_to_local( $order );
        }
        if ( ! file_exists( $file_path ) ) {
          continue;
        }
        $files_to_merge[] = Bring_Booking_Labels::get_file_path( $order->order->get_id(), $consignment_number );
      }
    }

    if ( empty( $files_to_merge ) ) {
      echo "No files to merge";
      die;
    }

    if ( count( $files_to_merge ) == 1 ) {
      $merge_result_file = reset( $files_to_merge );
    } else {
      include_once( FRAKTGUIDEN_PLUGIN_PATH .'/includes/pdfmerger/PDFMerger.php' );
      $merger = new PDFMerger();
      foreach ( $files_to_merge as $file ) {
        $merger->addPDF( $file );
      }
      $merge_result_file = Bring_Booking_Labels::get_local_dir() . '/labels-merged.pdf';
      $merger->merge( 'file', $merge_result_file );
    }

    header("Content-Length: " . filesize ( $merge_result_file ) );
    header("Content-type: application/pdf");
    header("Content-disposition: inline; filename=".basename($merge_result_file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    // Workaround for Chrome's inline pdf viewer
    ob_clean();
    flush();
    readfile($merge_result_file);
    die;
  }
}
