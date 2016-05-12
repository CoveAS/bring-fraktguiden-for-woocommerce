<?php


//Notice: is_page was called incorrectly. Conditional query tags do not work before the query is run. Before then, they always return false. Please see Debugging in WordPress for more information. (This message was added in version 3.1.) in /vagrant/wpplugins.dev/wp-includes/functions.php on line 3792
//Notice: wp_register_script was called incorrectly. Scripts and styles should not be registered or enqueued until the wp_enqueue_scripts, admin_enqueue_scripts, or login_enqueue_scripts hooks. Please see Debugging in WordPress for more information. (This message was added in version 3.3.) in /vagrant/wpplugins.dev/wp-includes/functions.php on line 3792
//https://api.bring.com/pickuppoint/api/pickuppoint/{countryCode}/id/{id}.json

Bring_Fraktguiden_Pro::init();

class Bring_Fraktguiden_Pro {

  static function init() {
    // Add option settings.
    add_filter( 'bring_fraktguiden_admin_form_fields', 'Bring_Fraktguiden_Pro::add_admin_options', 1, 1 );

    if ( self::get_woo_setting( 'pickup_point' ) == 'yes' ) {

      // Enqueue checkout Javascript.
      add_action( 'wp_enqueue_scripts', 'Bring_Fraktguiden_Pro::enqueue_checkout_script' );

      // Inject pickup point data to admin order page.
      add_action( 'admin_print_scripts', 'Bring_Fraktguiden_Pro::inline_pickup_point_data_to_admin_order' );
      // Enqueue admin Javascript.
      add_action( 'admin_enqueue_scripts', 'Bring_Fraktguiden_Pro::enqueue_admin_script' );

      // Update order with pickup point id.
      add_action( 'woocommerce_checkout_update_order_meta', 'Bring_Fraktguiden_Pro::checkout_update_order_meta' );
    }
  }

  /**
   * Adds form options fields to the admin page.
   *
   * @param array $fields
   * @return array
   */
  static function add_admin_options( $fields ) {
    //@todo: translate
    $fields['pickup_point'] = array(
        'title'       => 'Pickup Point',
        'label'       => 'Activate pickup point',
        'type'        => 'checkbox',
        'description' => 'TODO: Description',
        'default'     => 'no'
    );
    return $fields;
  }

  /**
   * Enqueue javascript to the checkout page.
   */
  static function enqueue_checkout_script() {
    if ( is_checkout() ) {
      wp_register_script( 'fraktguiden-pickup-point-checkout', plugins_url( 'js/pickup-point-checkout.js', __FILE__ ), array( 'jquery' ), '##VERSION##', true );
      wp_enqueue_script( 'fraktguiden-pickup-point-checkout' );
    }
  }

  /**
   * Enqueue javascript to the admin order page.
   */
  static function enqueue_admin_script() {
    $screen = get_current_screen();
    if ( $screen->id == 'shop_order' ) {
      wp_register_script( 'fraktguiden-pickup-point-admin', plugins_url( 'js/pickup-point-admin.js', __FILE__ ), array( 'jquery' ), '##VERSION##', true );
      wp_enqueue_script( 'fraktguiden-pickup-point-admin' );
    }
  }

  static function inline_pickup_point_data_to_admin_order() {
    $screen = get_current_screen();

    if ( $screen->id == 'shop_order' ) {
      global $post;

      $pickup_point_id = get_post_meta( $post->ID, '_fraktguiden_pickuppoint_id', true );

      if ( $pickup_point_id ) {
        $order = new WC_Order( $post->ID );

        $res = wp_remote_get( 'https://api.bring.com/pickuppoint/api/pickuppoint/' . $order->get_address()['country'] . '/id/' . $pickup_point_id . '.json' );

        file_put_contents('/vagrant/debug.log', print_r($res,1) . PHP_EOL, FILE_APPEND);

        if ( $res['response']['code'] == 200 ) {
          echo '<script>';
          echo 'var _fraktguiden_pickup_point = ' . $res['body'];
          echo '</script>';
        }
      }
    }
  }

  /**
   * Store the pickup point id on order checkout.
   *
   * @param int $order_id
   */
  static function checkout_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['_fraktguiden_pickuppoint_id'] ) ) {
      update_post_meta( $order_id, '_fraktguiden_pickuppoint_id', sanitize_text_field( $_POST['_fraktguiden_pickuppoint_id'] ) );
    }
  }

  /**
   * Gets a Woo admin setting by key
   * Returns false if key is not found.
   *
   * @param string $key
   * @return string|bool
   *
   * @todo: There must be an API in woo for this. Investigate.
   */
  static function get_woo_setting( $key ) {
    $options = get_option( 'woocommerce_' . WC_Shipping_Method_Bring::ID . '_settings' );
    return array_key_exists( $key, $options ) ? $options[$key] : false;
  }

}
