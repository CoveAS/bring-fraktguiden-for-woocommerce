<?php
if ( ! defined( 'ABSPATH' ) ) {
  die; // Exit if accessed directly
}

// Create a menu item for PDF download.
add_action( 'admin_menu', 'Bring_Booking_Labels::open_pdfs' );

class Bring_Booking_Labels {

  /**
   * @param string $order_ids Comma separated string with order ids.
   *
   * @return string
   */
  static function create_download_url( $order_ids ) {
    if ( is_array( $order_ids ) ) {
      $order_ids = implode( ',', $order_ids );
    }
    return admin_url( '?page=bring_download&order_ids=' . $order_ids );
  }

  /**
   * Open PDF's
   * @return [type] [description]
   */
  static function open_pdfs() {
    add_submenu_page( null, 'Download', 'Download', 'manage_woocommerce', 'bring_download', __CLASS__.'::download_page' );
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

  /**
   * Download page
   */
  static function download_page() {
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
    $zpls          = [];
    $pdfs_to_merge = [];
    $orders_to_merge = [];

    foreach ( $order_ids as $order_id ) {
      $adapter = new Bring_WC_Order_Adapter( new WC_Order( $order_id ) );
      // Get the booking consignments from the adapter
      $consignments = $adapter->get_booking_consignments();
      foreach ( $consignments as $consignment ) {
        // Get the label file
        $file = $consignment->get_label_file();
        // Try to download the file if it doesn't exist
        if ( ! $file->exists() && ! $file->download() ) {
          continue;
        }
        if ( 'zpl' == $file->get_ext() ) {
          $zpls[$order_id] = $file;
          continue;
        }
        // Add the file path to the list of pdf's
        $pdfs_to_merge[] = $file;
        if ( ! in_array( $order_id, $orders_to_merge ) ) {
          $orders_to_merge[] = $order_id;
        }
      }
    }

    if ( empty( $pdfs_to_merge ) && empty( $zpls ) ) {
      echo "No files to download";
      return;
    }

    // Merge the pdfs
    if ( ! empty( $pdfs_to_merge ) ) {
      if ( count( $pdfs_to_merge ) == 1 ) {
        $merge_result_file = $pdfs_to_merge[0]->get_path();
      } else {
        include_once( FRAKTGUIDEN_PLUGIN_PATH .'/includes/pdfmerger/PDFMerger.php' );
        $merger = new PDFMerger();
        foreach ( $pdfs_to_merge as $file ) {
          $merger->addPDF( $file->get_path() );
        }
        $merge_result_file = $file->get_dir(). '/labels-merged.pdf';
        $merger->merge( 'file', $merge_result_file );
      }
    }
    // If there are more than 1 ZPL file or a combination of zpl and pdf
    if ( ! empty( $zpls ) && ! empty( $pdfs_to_merge ) || count( $zpls ) > 1 ) {
      echo '<h3>'.__('Downloads', 'bring-fraktguiden') .'</h3><ul>';
      foreach ( $zpls as $order_id => $zpl ) {
        self::render_download_link( [ $order_id ], $zpl->get_name() );
      }
      if ( ! empty( $orders_to_merge ) ) {
        self::render_download_link( $orders_to_merge, __( 'Merged PDF labels', 'bring-fraktguiden' ) );
      }
      echo '</ul>';
      return;
    }
    if ( ! empty( $pdfs_to_merge ) ) {
      static::render_file_content( $merge_result_file );
    }
    else if ( ! empty( $zpls ) ) {
      $zpl = reset( $zpls );
      static::render_file_content( $zpl->get_path() );
    }
  }

  /**
   * Render file content
   * @param  string $file
   */
  static function render_file_content( $file ) {
    // $mime = mime_content_type( $file );
    header('Content-Length: ' . filesize ( $file ) );
    if ( preg_match('/\.pdf$/', $file ) ) {
      header( 'Content-type: application/pdf' );
    } else {
      header( 'Content-type: application/octet-stream' );
    }
    header('Content-disposition: inline; filename='.basename( $file ));
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    // Workaround for Chrome's inline pdf viewer
    ob_clean();
    flush();
    readfile( $file );
    die;
  }

  /**
   * Render download link
   * @param  array  $order_ids
   * @param  string $name
   */
  static function render_download_link( $order_ids, $name ) {
    printf(
      '<li><a href="%s" target="_blank">%s</a></li>',
      static::create_download_url( $order_ids ),
      $name
    );
  }
}
