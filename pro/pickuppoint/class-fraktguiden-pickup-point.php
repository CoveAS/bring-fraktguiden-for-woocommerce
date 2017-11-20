<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// Load the depreceated methods
require_once __DIR__ .'/class-fraktguiden-pickup-point-depreceated.php';

// Only if Klarna is used
add_action( 'klarna_after_kco_confirmation', array( 'Fraktguiden_Pickup_Point', 'checkout_save_pickup_point' ) );
add_action( 'woocommerce_thankyou', array( 'Fraktguiden_Pickup_Point', 'checkout_save_pickup_point' ) );

/**
 * Process the checkout
 */
class Fraktguiden_Pickup_Point {

  const ID = Fraktguiden_Helper::ID;
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
    add_action( 'wp_ajax_bring_get_pickup_points',            __CLASS__. '::ajax_get_pickup_points' );
    add_action( 'wp_ajax_nopriv_bring_get_pickup_points',     __CLASS__. '::ajax_get_pickup_points' );
    add_action( 'wp_ajax_bring_post_code_validation',         __CLASS__. '::ajax_post_code_validation' );
    add_action( 'wp_ajax_nopriv_bring_post_code_validation',  __CLASS__. '::ajax_post_code_validation' );

    add_action( 'wp_ajax_bring_shipping_info_var', array( __CLASS__, 'wp_ajax_get_bring_shipping_info_var' ) );
    add_action( 'wp_ajax_bring_get_rate', array( __CLASS__, 'wp_ajax_get_rate' ) );

    // Display order received and mail.
    add_filter( 'woocommerce_order_shipping_to_display_shipped_via', array( __CLASS__, 'checkout_order_shipping_to_display_shipped_via' ), 1, 2 );

    // Hide shipping meta data from order items (WooCommerce 2.6)
    // https://github.com/woothemes/woocommerce/issues/9094
    add_filter( 'woocommerce_hidden_order_itemmeta', array( __CLASS__, 'woocommerce_hidden_order_itemmeta' ), 1, 1 );

    // Klarna checkout specific html
    add_action( 'kco_widget_after', array( __CLASS__, 'kco_post_code_html' ) );
    add_filter( 'kco_create_order', array( __CLASS__, 'set_kco_postal_code' ) );
    add_filter( 'kco_update_order', array( __CLASS__, 'set_kco_postal_code' ) );

    // Pickup points
    add_filter( 'bring_shipping_rates', __CLASS__ .'::insert_pickup_points' );
  }

  /**
   * Set Klarna Checkout postal code
   * @param array Klarna order data
   */
  static function set_kco_postal_code( $order ) {
    $postcode = esc_html( WC()->customer->get_shipping_postcode() );
    $order['shipping_address']['postal_code'] = $postcode;
    return $order;
  }

  static function woocommerce_hidden_order_itemmeta( $fields ) {
    $fields[] = '_fraktguiden_pickup_point_postcode';
    $fields[] = '_fraktguiden_pickup_point_id';
    $fields[] = '_fraktguiden_pickup_point_info_cached';
    return $fields;
  }

  /**
   * Klarna Checkout post code selector HTML
   */
  static function kco_post_code_html() {
    $i18n = self::get_i18n();
    $postcode = esc_html( WC()->customer->get_shipping_postcode() );
    ?>
    <div class="bring-enter-postcode">
      <form>
        <label><?php echo $i18n['POSTCODE']; ?>
          <input class="input-text" type="text" value="<?php echo $postcode; ?>">
        </label>
        <input class="button" type="submit" value="Hent Leveringsmetoder">
      </form>
    </div>
    <?php
    // Check if
    if ( 'yes' != apply_filters( 'bring_clone_shipping_methods', Fraktguiden_Helper::get_option( 'bring_clone_shipping_methods', 'yes' ) ) ) {
      return;
    }
    ?>
    <div class="bring-select-shipping">
      <h3>
        <?php echo apply_filters(
          'bring_fraktguiden_select_delivery_method_title',
          __( 'Select shipping method', 'bring-fraktguiden' )
        ); ?>
      </h3>
      <div class="bring-select-shipping--options">
      </div>
    </div>
    <?php
  }

  /**
   * Load checkout javascript
   */
  static function checkout_load_javascript() {

    if ( is_checkout() ) {
      wp_register_script( 'fraktguiden-common', plugins_url( 'assets/js/pickup-point-common.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
      wp_register_script( 'fraktguiden-pickup-point-checkout', plugins_url( 'assets/js/pickup-point-checkout.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
      wp_localize_script( 'fraktguiden-pickup-point-checkout', '_fraktguiden_data', [
          'ajaxurl'               => admin_url( 'admin-ajax.php' ),
          'i18n'                  => self::get_i18n(),
          'country'               => Fraktguiden_Helper::get_option( 'from_country' ),
          'klarna_checkout_nonce' => wp_create_nonce( 'klarna_checkout_nonce' ),
          'nonce'                 => wp_create_nonce( 'bring_fraktguiden' ),
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

      wp_register_script( 'fraktguiden-common', plugins_url( 'assets/js/pickup-point-common.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
      wp_register_script( 'fraktguiden-pickup-point-admin', plugins_url( 'assets/js/pickup-point-admin.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
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
   * Add pickup point from shop/checkout
   *
   * This method now assumes that the system has only one shipping method per order in checkout.
   *
   * @param int $order_id
   */
  static function checkout_save_pickup_point( $order_id ) {

    if ( $order_id ) {

      $order = new Bring_WC_Order_Adapter( new WC_Order( $order_id ) );

      $expire = time() - 300;

      if ( isset( $_COOKIE['_fraktguiden_packages'] ) ) {
        $order->checkout_update_packages( $_COOKIE['_fraktguiden_packages'] );
        setcookie( '_fraktguiden_packages', '', $expire );
      }
      Fraktguiden_Pickup_Point_Depreceated::checkout_save_pickup_point( $order );
      $shipping_methods = $order->order->get_shipping_methods();
      foreach ( $shipping_methods as $item_id => $method ) {
        $method_id = wc_get_order_item_meta( $item_id, 'method_id', true );
        $method = Fraktguiden_Helper::parse_shipping_method_id( $method_id );
        if ( $method['service'] == 'SERVICEPAKKE' && $method['pickup_point_id'] ) {
          $order->checkout_update_pickup_point_data(
              $method['pickup_point_id'],
              ( isset( $_POST['postal_code'] ) ? $_POST['postal_code'] : false ),
              false
          );
        }
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
      if (
        $shipping_method['method_id'] == self::ID . ':servicepakke' &&
        isset( $shipping_method['fraktguiden_pickup_point_info_cached'] ) &&
        $shipping_method['fraktguiden_pickup_point_info_cached']
      ) {
        $info    = $shipping_method['fraktguiden_pickup_point_info_cached'];
        $content = $content . '<div class="bring-order-details-pickup-point"><div class="bring-order-details-selected-text">' . self::get_i18n()['PICKUP_POINT'] . ':</div><div class="bring-order-details-info-text">' . str_replace( "|", '<br>', $info ) . '</div></div>';
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
    return [
        'PICKUP_POINT'               => __( 'Pickup point', 'bring-fraktguiden' ),
        'LOADING_TEXT'               => __( 'Please wait...', 'bring-fraktguiden' ),
        'VALIDATE_SHIPPING1'         => __( 'Fraktguiden requires the following fields', 'bring-fraktguiden' ),
        'VALIDATE_SHIPPING_POSTCODE' => __( 'Valid shipping postcode', 'bring-fraktguiden' ),
        'VALIDATE_SHIPPING_COUNTRY'  => __( 'Valid shipping postcode', 'bring-fraktguiden' ),
        'VALIDATE_SHIPPING2'         => __( 'Please update the fields and save the order first', 'bring-fraktguiden' ),
        'SERVICE_PLACEHOLDER'        => __( 'Please select service', 'bring-fraktguiden' ),
        'POSTCODE'                   => __( 'Postcode', 'bring-fraktguiden' ),
        'PICKUP_POINT_PLACEHOLDER'   => __( 'Please select pickup point', 'bring-fraktguiden' ),
        'SELECTED_TEXT'              => __( 'Selected pickup point', 'bring-fraktguiden' ),
        'PICKUP_POINT_NOT_FOUND'     => __( 'No pickup points found for postcode', 'bring-fraktguiden' ),
        'GET_RATE'                   => __( 'Get Rate', 'bring-fraktguiden' ),
        'PLEASE_WAIT'                => __( 'Please wait', 'bring-fraktguiden' ),
        'SERVICE'                    => __( 'Service', 'bring-fraktguiden' ),
        'RATE_NOT_AVAILABLE'         => __( 'Rate is not available for this order. Please try another service', 'bring-fraktguiden' ),
        'REQUEST_FAILED'             => __( 'Request was not successful', 'bring-fraktguiden' ),
        'ADD_POSTCODE'               => __( 'Please add postal code', 'bring-fraktguiden' ),
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
  static function ajax_post_code_validation() {
    $params = http_build_query( [
      'clientUrl' => get_site_url(),
      'country'   => $_REQUEST['country'],
      'pnr'       => $_REQUEST['post_code'],
    ] );
    $content = file_get_contents( 'https://api.bring.com/shippingguide/api/postalCode.json?' . $params );
    if ( ! $content ) {
      wp_send_json( '{ "error" : "Could not connect to api.bring.com" }' );
    }
    $data =  json_decode( $content );
    if ( ! $data ) {
      wp_send_json( '{ "error" : "Recieved invalid JSON from api.bring.com" }' );
    }

    return wp_send_json( $data );
  }

  static function ajax_get_pickup_points() {
    $response = self::get_pickup_points( $_REQUEST['country'], $_REQUEST['postcode'] );
    if ( 200 != $response->status_code ) {
      die;
    }
    echo $response->get_body();
    die;
  }
  static function get_pickup_points( $country, $postcode ) {
    $request = new WP_Bring_Request();
    return $request->get( self::BASE_URL . '/' . $country . '/postalCode/' . $postcode . '.json' );
  }

  /**
   * Filter: Insert pickup points
   * @hook bring_shipping_rates
   */
  static function insert_pickup_points( $rates ) {
    $rate_key = false;
    $service_package = false;
    foreach ( $rates as $key => $rate ) {
      if ( $rate['id'] == 'bring_fraktguiden:servicepakke' ) {
        // Service package identified
        $service_package = $rate;
        // Remove this package
        $rate_key = $key;
        break;
      }
    }

    if ( ! $service_package ) {
      // Service package is not available.
      // That means it's the end of the line for pickup points
      return;
    }

    $pickup_point_limit = apply_filters( 'bring_pickup_point_limit', 999 );
    $postcode = esc_html( WC()->customer->get_shipping_postcode() );
    $country  = esc_html( WC()->customer->get_shipping_country() );
    $response = self::get_pickup_points( $country, $postcode );

    if ( 200 != $response->status_code ) {
      sleep( 1 );
      $response = self::get_pickup_points( $country, $postcode );
    }
    if ( 200 != $response->status_code ) {
      return $rates;
    }
    // Remove service package
    unset( $rates[ $rate_key ] );

    $pickup_point_count = 0;
    $pickup_points = json_decode( $response->get_body(), 1 );
    $new_rates = [];
    foreach ( $pickup_points['pickupPoint'] as $pickup_point ) {
      $rate = [
          'id'    => 'bring_fraktguiden:servicepakke-'. $pickup_point['id'],
          'cost'  => $service_package['cost'],
          'label' => $pickup_point['name'],
      ];
      $new_rates[] = $rate;
      if ( ++$pickup_point_count >= $pickup_point_limit ) {
        break;
      }
    }
    foreach ( $rates as $key => $rate ) {
      $new_rates[] = $rate;
    }

    return $new_rates;
  }

}
