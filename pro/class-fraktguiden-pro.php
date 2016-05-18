<?php

Bring_Fraktguiden_Pro::init();

class Bring_Fraktguiden_Pro {

  static function init() {
    // Add option settings.
    add_filter( 'bring_fraktguiden_admin_form_fields', 'Bring_Fraktguiden_Pro::add_admin_options', 1, 1 );

    if ( self::get_woo_setting( 'pickup_point' ) == 'yes' ) {

      // Enqueue checkout Javascript.
      add_action( 'wp_enqueue_scripts', 'Bring_Fraktguiden_Pro::enqueue_checkout_javascript' );

      // Enqueue admin Javascript.
      add_action( 'admin_enqueue_scripts', 'Bring_Fraktguiden_Pro::enqueue_admin_javascript' );

      // Ajax
      add_action( 'wp_ajax_fg_get_pickup_point', 'Bring_Fraktguiden_Pro::ajax_get_pickup_point' );
      add_action( 'wp_ajax_nopriv_fg_get_pickup_point', 'Bring_Fraktguiden_Pro::ajax_get_pickup_point' );
      add_action( 'wp_ajax_fg_get_services', 'Bring_Fraktguiden_Pro::ajax_get_fraktguiden_services' );
      add_action( 'wp_ajax_nopriv_fg_get_services', 'Bring_Fraktguiden_Pro::ajax_get_fraktguiden_services' );

      // Inject pickup point data to admin order page.
      add_action( 'admin_print_scripts', 'Bring_Fraktguiden_Pro::enqueue_admin_javascript' );

      // Update order with pickup point id.
      add_action( 'woocommerce_checkout_update_order_meta', 'Bring_Fraktguiden_Pro::checkout_update_order_meta' );
    }
  }

  /**
   * Adds form options fields to the admin page.
   *
   * @todo: translate
   *
   * @param array $fields
   * @return array
   */
  static function add_admin_options( $fields ) {
    $fields['pickup_point'] = [
        'title'       => 'Pickup Point',
        'label'       => 'Activate pickup point',
        'type'        => 'checkbox',
        'description' => 'TODO: Description',
        'default'     => 'no'
    ];
    return $fields;
  }

  static function enqueue_checkout_javascript() {
    if ( is_checkout() ) {
      wp_register_script( 'fraktguiden-pickup-point-checkout', plugins_url( 'js/pickup-point-checkout.js', __FILE__ ), array( 'jquery' ), '##VERSION##', true );
      wp_localize_script( 'fraktguiden-pickup-point-checkout', '_fraktguiden_pickup_point', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
      wp_enqueue_script( 'fraktguiden-pickup-point-checkout' );
    }
  }

  /**
   * Enqueue javascript to the admin order page.
   */
  static function enqueue_admin_javascript() {
    $screen = get_current_screen();
    // Only for order edit screen
    if ( $screen->id == 'shop_order' ) {
      wp_register_script( 'fraktguiden-pickup-point-admin', plugins_url( 'js/pickup-point-admin.js', __FILE__ ), array( 'jquery' ), '##VERSION##', true );
      wp_enqueue_script( 'fraktguiden-pickup-point-admin' );

      // Inject a JS object to the document.
      wp_localize_script( 'fraktguiden-pickup-point-admin', '_fraktguiden_pickup_point', array(
          'ajaxurl'     => admin_url( 'admin-ajax.php' ),
          'items' => self::get_pickup_points_for_order()
      ) );
    }
  }

  static function get_pickup_points_for_order() {
    $screen = get_current_screen();

    if ( $screen->id == 'shop_order' ) {
      global $post;

      $result = [ ];

      $order            = new WC_Order( $post->ID );
      $shipping_methods = $order->get_shipping_methods();

      foreach ( $shipping_methods as $id => $method_item ) {
        $pickup_point_id = wc_get_order_item_meta( $id, '_fraktguiden_pickup_point_id', true );
        if ( $pickup_point_id ) {
          $response = wp_remote_get( 'https://api.bring.com/pickuppoint/api/pickuppoint/' . $order->get_address()['country'] . '/id/' . $pickup_point_id . '.json' );
          if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
            $result[] = [
                'item_id'      => $id,
                'pickup_point' => json_decode( $response['body'] )->pickupPoint[0]
            ];
          }
        }
      }

      return $result;

//      if ( ! empty( $json ) ) {
//        echo '<script>';
//        echo 'var _fraktguiden_order_items_data = ' . json_encode( $json );
//        echo '</script>';
//      }
    }
  }

  /**
   * @param int $order_id
   */
  static function checkout_update_order_meta( $order_id ) {

    $order            = new WC_Order( $order_id );
    $shipping_methods = $order->get_shipping_methods();

    foreach ( $shipping_methods as $id => $method_item ) {
      $method_id = wc_get_order_item_meta( $id, 'method_id', true );

      if ( $method_id == 'bring_fraktguiden:servicepakke' ) {
        // Assume it is only one shipping method in checkout for now.
        if ( ! empty( $_POST['_fraktguiden_pickup_point_id'] ) ) {
          wc_add_order_item_meta( $id, '_fraktguiden_pickup_point_id', $_POST['_fraktguiden_pickup_point_id'] );
        }
      }
    }

//    if ( ! empty( $_POST['_fraktguiden_pickup_point_id'] ) ) {
//      update_post_meta( $order_id, '_fraktguiden_pickup_point_id', sanitize_text_field( $_POST['_fraktguiden_pickup_point_id'] ) );
//    }
  }

  /**
   * Gets a Woo admin setting by key
   * Returns false if key is not found.
   *
   * @todo: There must be an API in woo for this. Investigate.
   *
   * @param string $key
   * @return string|bool

   */
  static function get_woo_setting( $key ) {
    $options = get_option( 'woocommerce_' . WC_Shipping_Method_Bring::ID . '_settings' );
    return array_key_exists( $key, $options ) ? $options[$key] : false;
  }

  static function ajax_get_available_services() {
    //Bring_Fraktguiden_Services
    die();
  }

  static function ajax_get_pickup_point() {
    if ( isset( $_GET['country'] ) && $_GET['postcode'] ) {
      self::get_pickup_point( $_GET['country'], $_GET['postcode'] );
      die();
    }
  }

  static function ajax_get_fraktguiden_services() {
    echo json_encode( Bring_Fraktguiden_Services::get_available() );
    die();
  }

  static function get_pickup_point( $country, $postcode ) {
    echo( file_get_contents( 'https://api.bring.com/pickuppoint/api/pickuppoint/' . $country . '/postalCode/' . $postcode . '.json' ) );
  }


}
