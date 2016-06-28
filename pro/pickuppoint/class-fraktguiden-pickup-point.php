<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// Only if Klarna is used
add_action( 'klarna_after_kco_confirmation', array( 'Fraktguiden_Pickup_Point', 'checkout_save_pickup_point' ) );
add_action( 'woocommerce_thankyou', array( 'Fraktguiden_Pickup_Point', 'checkout_save_pickup_point' ) );

/**
 * Process the checkout
 */
class Fraktguiden_Pickup_Point {

  const ID = Fraktguiden_Helper::ID;
  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;
  const BASE_URL = 'https://api.bring.com/pickuppoint/api/pickuppoint';

  static function init() {
    // Enqueue checkout Javascript.
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'checkout_load_javascript' ) );
    // Enqueue admin Javascript.
    add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_load_javascript' ) );
    // Checkout update order meta.
    add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'checkout_save_pickup_point' ) );
    // Admin save order items.
    add_action( 'woocommerce_saved_order_items', array( __CLASS__, 'admin_saved_order_items' ), 1, 2 );
    // Ajax
    add_action( 'wp_ajax_bring_get_pickup_points', array( __CLASS__, 'wp_ajax_get_pickup_points' ) );
    add_action( 'wp_ajax_nopriv_bring_get_pickup_points', array( __CLASS__, 'wp_ajax_get_pickup_points' ) );

    add_action( 'wp_ajax_bring_shipping_info_var', array( __CLASS__, 'wp_ajax_get_bring_shipping_info_var' ) );
    add_action( 'wp_ajax_bring_get_rate', array( __CLASS__, 'wp_ajax_get_rate' ) );

    // Validate pickup point.
    if ( Fraktguiden_Helper::get_option( 'pickup_point_required' ) == 'yes' ) {
      add_action( 'woocommerce_checkout_process', array( __CLASS__, 'checkout_validate_pickup_point' ) );
    }

    // Display order received and mail.
    add_filter( 'woocommerce_order_shipping_to_display_shipped_via', array( __CLASS__, 'checkout_order_shipping_to_display_shipped_via' ), 1, 2 );

    // Hide shipping meta data from order items (WooCommerce 2.6)
    // https://github.com/woothemes/woocommerce/issues/9094
    add_filter( 'woocommerce_hidden_order_itemmeta', array( __CLASS__, 'woocommerce_hidden_order_itemmeta' ), 1, 1 );
  }

  /**
   * Load checkout javascript
   */
  static function checkout_load_javascript() {

    if ( is_checkout() ) {
      wp_register_script( 'fraktguiden-common', plugins_url( 'assets/js/pickup-point-common.js', dirname( __FILE__ ) ), array( 'jquery' ), '##VERSION##', true );
      wp_register_script( 'fraktguiden-pickup-point-checkout', plugins_url( 'assets/js/pickup-point-checkout.js', dirname( __FILE__ ) ), array( 'jquery' ), '##VERSION##', true );
      wp_localize_script( 'fraktguiden-pickup-point-checkout', '_fraktguiden_data', [
          'ajaxurl'      => admin_url( 'admin-ajax.php' ),
          'i18n'         => self::get_i18n(),
          'from_country' => Fraktguiden_Helper::get_option( 'from_country' )
      ] );

      wp_enqueue_script( 'fraktguiden-common' );
      wp_enqueue_script( 'fraktguiden-pickup-point-checkout' );
    }
  }

  /**
   * Load admin javascript
   */
  static function admin_load_javascript() {
    $screen = get_current_screen();
    // Only for order edit screen
    if ( $screen->id == 'shop_order' ) {
      global $post;
      $order = new Bring_WC_Order_Adapter( new WC_Order( $post->ID ) );

      $make_items_editable = ! $order->order->is_editable();
      if ( isset( $_GET['booking_step'] ) ) {
        $make_items_editable = false;
      }

      if ( $order->is_booked() ) {
        $make_items_editable = false;
      }

      wp_register_script( 'fraktguiden-common', plugins_url( 'assets/js/pickup-point-common.js', dirname( __FILE__ ) ), array( 'jquery' ), '##VERSION##', true );
      wp_register_script( 'fraktguiden-pickup-point-admin', plugins_url( 'assets/js/pickup-point-admin.js', dirname( __FILE__ ) ), array( 'jquery' ), '##VERSION##', true );
      wp_localize_script( 'fraktguiden-pickup-point-admin', '_fraktguiden_data', [
          'ajaxurl'             => admin_url( 'admin-ajax.php' ),
          'services'            => Fraktguiden_Helper::get_all_services(),
          'i18n'                => self::get_i18n(),
          'make_items_editable' => $make_items_editable
      ] );

      wp_enqueue_script( 'fraktguiden-common' );
      wp_enqueue_script( 'fraktguiden-pickup-point-admin' );
    }
  }

  static function get_bring_shipping_info_for_order() {
    $result = [ ];
    $screen = get_current_screen();
    if ( ( $screen && $screen->id == 'shop_order' ) || is_ajax() ) {
      global $post;
      $post_id = $post ? $post->ID : $_GET['post_id'];
      $order   = new Bring_WC_Order_Adapter( new WC_Order( $post_id ) );
      $result  = $order->get_shipping_data();
    }
    return $result;
  }

  /**
   * Validate pickup point on checkout submit.
   */
  static function checkout_validate_pickup_point() {
    // Check if set, if its not set add an error.
    if ( ! $_COOKIE['_fraktguiden_pickup_point_id'] ) {
      wc_add_notice( __( '<strong>Pickup point</strong> is a required field.', self::TEXT_DOMAIN ), 'error' );
    }
  }

  /**
   * Add pickup point from shop/checkout
   *
   * This method now assumes that the system has only one shipping method per order in checkout.
   *
   * @param int $order_id
   */
  static function checkout_save_pickup_point( $order_id ) {

    if ( $order_id ) {

      $order = new Bring_WC_Order_Adapter( new WC_Order( $order_id ) );

      if ( session_status() == PHP_SESSION_NONE ) {
        session_start();
      }

      if ( $_COOKIE['_fraktguiden_pickup_point_id'] && $_COOKIE['_fraktguiden_pickup_point_postcode'] && $_COOKIE['_fraktguiden_pickup_point_info_cached'] ) {
        $order->checkout_update_pickup_point_data(
            $_COOKIE['_fraktguiden_pickup_point_id'],
            $_COOKIE['_fraktguiden_pickup_point_postcode'],
            $_COOKIE['_fraktguiden_pickup_point_info_cached']
        );

        // Unset cookies.
        // This does not work at the moment as headers has already been sent.
        // @todo: Find an earlier hook
        $expire = time() - 300;
        setcookie('_fraktguiden_pickup_point_id', '', $expire);
        setcookie('_fraktguiden_pickup_point_postcode', '', $expire);
        setcookie('_fraktguiden_pickup_point_info_cached', '', $expire);
      }

      if ( $_SESSION['_fraktguiden_packages'] ) {
        $order->checkout_update_packages( $_SESSION['_fraktguiden_packages'] );
        unset( $_SESSION['_fraktguiden_packages'] );
      }

    }
  }

  /**
   * Updates pickup points from admin/order items.
   *
   * @param $order_id
   * @param $shipping_items
   */
  static function admin_saved_order_items( $order_id, $shipping_items ) {
    $order = new Bring_WC_Order_Adapter( new WC_Order( $order_id ) );
    $order->admin_update_pickup_point( $shipping_items );
  }

  /**
   * HTML for checkout recipient page / emails etc.
   *
   * @param string $content
   * @param WC_Order $wc_order
   * @return string
   */
  static function checkout_order_shipping_to_display_shipped_via( $content, $wc_order ) {
    $shipping_methods = $wc_order->get_shipping_methods();
    foreach ( $shipping_methods as $id => $shipping_method ) {
      if ( $shipping_method['method_id'] == self::ID . ':servicepakke' ) {
        if ( key_exists( 'fraktguiden_pickup_point_info_cached', $shipping_method ) ) {
          $info    = $shipping_method['fraktguiden_pickup_point_info_cached'];
          $content = $content . '<div class="bring-order-details-pickup-point"><div class="bring-order-details-selected-text">' . self::get_i18n()['PICKUP_POINT'] . ':</div><div class="bring-order-details-info-text">' . str_replace( "|", '<br>', $info ) . '</div></div>';
        }
      }
    }
    return $content;
  }

  /**
   * Text translation strings for ui JavaScript.
   *
   * @return array
   */
  static function get_i18n() {
    $text_domain = self::TEXT_DOMAIN;
    return [
        'PICKUP_POINT'               => __( 'Pickup point', $text_domain ),
        'LOADING_TEXT'               => __( 'Please wait...', $text_domain ),
        'VALIDATE_SHIPPING1'         => __( 'Fraktguiden requires the following fields', $text_domain ),
        'VALIDATE_SHIPPING_POSTCODE' => __( 'Valid shipping postcode', $text_domain ),
        'VALIDATE_SHIPPING_COUNTRY'  => __( 'Valid shipping postcode', $text_domain ),
        'VALIDATE_SHIPPING2'         => __( 'Please update the fields and save the order first', $text_domain ),
        'SERVICE_PLACEHOLDER'        => __( 'Please select service', $text_domain ),
        'POSTCODE'                   => __( 'Postcode', $text_domain ),
        'PICKUP_POINT_PLACEHOLDER'   => __( 'Please select pickup point', $text_domain ),
        'SELECTED_TEXT'              => __( 'Selected pickup point', $text_domain ),
        'PICKUP_POINT_NOT_FOUND'     => __( 'No pickup points found for postcode', $text_domain ),
        'GET_RATE'                   => __( 'Get Rate', $text_domain ),
        'PLEASE_WAIT'                => __( 'Please wait', $text_domain ),
        'SERVICE'                    => __( 'Service', $text_domain ),
        'RATE_NOT_AVAILABLE'         => __( 'Rate is not available for this order. Please try another service', $text_domain ),
        'REQUEST_FAILED'             => __( 'Request was not successful', $text_domain ),
        'ADD_POSTCODE'               => __( 'Please add postal code', $text_domain ),
    ];
  }

  /**
   * Prints shipping info json
   *
   * Only available from admin
   */
  static function wp_ajax_get_bring_shipping_info_var() {
    header( 'Content-type: application/json' );
    echo json_encode( array(
        'bring_shipping_info' => self::get_bring_shipping_info_for_order()
    ) );
    die();
  }

  /**
   * Prints rate json for a bring service.
   *
   * Only available from admin.
   * @todo: refactor!!
   */
  static function wp_ajax_get_rate() {
    header( 'Content-type: application/json' );

    $result = [
        'success'  => false,
        'rate'     => null,
        'packages' => null,
    ];

    if ( isset( $_GET['post_id'] ) && isset( $_GET['service'] ) ) {

      $order = new WC_Order( $_GET['post_id'] );
      $items = $order->get_items();

      $fake_cart = [ ];
      foreach ( $items as $item ) {
        $fake_cart[uniqid()] = [
            'quantity' => $item['qty'],
            'data'     => new WC_Product_Simple( $item['product_id'] )
        ];
      }

      //include( '../../common/class-fraktguiden-packer.php' );
      $packer = new Fraktguiden_Packer();

      $product_boxes = $packer->create_boxes( $fake_cart );

      $packer->pack( $product_boxes, true );

      $package_params = $packer->create_packages_params();

      //@todo: share / filter
      $standard_params = array(
          'clientUrl'           => $_SERVER['HTTP_HOST'],
          'from'                => Fraktguiden_Helper::get_option( 'from_zip' ),
          'fromCountry'         => Fraktguiden_Helper::get_option( 'from_country' ),
          'to'                  => $order->shipping_postcode,
          'toCountry'           => $order->shipping_country,
          'postingAtPostOffice' => ( Fraktguiden_Helper::get_option( 'post_office' ) == 'no' ) ? 'false' : 'true',
          'additional'          => ( Fraktguiden_Helper::get_option( 'evarsling' ) == 'yes' ) ? 'evarsling' : '',
      );
      $params          = array_merge( $standard_params, $package_params );

      $url = add_query_arg( $params, WC_Shipping_Method_Bring::SERVICE_URL );

      $url .= '&product=' . $_GET['service'];

      // Make the request.
      $request  = new WP_Bring_Request();
      $response = $request->get( $url );

      if ( $response->status_code == 200 ) {

        $json = json_decode( $response->get_body(), true );

        $service = $json['Product']['Price']['PackagePriceWithoutAdditionalServices'];
        $rate    = Fraktguiden_Helper::get_option( 'vat' ) == 'exclude' ? $service['AmountWithoutVAT'] : $service['AmountWithVAT'];

        $result['success']  = true;
        $result['rate']     = $rate;
        $result['packages'] = json_encode( $package_params );
      }
    }

    echo json_encode( $result );

    die();
  }

  /**
   * Prints pickup points json
   */
  static function wp_ajax_get_pickup_points() {
    if ( isset( $_GET['country'] ) && $_GET['postcode'] ) {
      $request  = new WP_Bring_Request();
      $response = $request->get( self::BASE_URL . '/' . $_GET['country'] . '/postalCode/' . $_GET['postcode'] . '.json' );

      header( "Content-type: application/json" );
      status_header( $response->status_code );

      if ( $response->status_code != 200 ) {
        echo '{}';
      }
      else {
        echo $response->get_body();
      }

    }
    die();
  }

  /**
   * Hide shipping meta data from order items (WooCommerce 2.6)
   * https://github.com/woothemes/woocommerce/issues/9094
   *
   * @param array $hidden_items
   * @return array
   */
  static function woocommerce_hidden_order_itemmeta( $hidden_items ) {
    $hidden_items[] = '_fraktguiden_pickup_point_id';
    $hidden_items[] = '_fraktguiden_pickup_point_postcode';
    $hidden_items[] = '_fraktguiden_pickup_point_info_cached';

    return $hidden_items;
  }

}