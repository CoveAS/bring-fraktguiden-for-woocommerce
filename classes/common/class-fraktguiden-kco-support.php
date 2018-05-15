<?php


class Fraktguiden_KCO_Support {

  static function setup() {
    if ( ! class_exists( 'Klarna_Checkout_For_WooCommerce' ) ) {
      return;
    }
    add_action( 'kco_wc_before_snippet', __CLASS__ .'::before_kco' );
    add_action( 'kco_wc_after_snippet',  __CLASS__ .'::after_kco' );
    add_action( 'wp_ajax_bring_post_code_validation',         __CLASS__. '::ajax_post_code_validation' );
    add_action( 'wp_ajax_nopriv_bring_post_code_validation',  __CLASS__. '::ajax_post_code_validation' );
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'checkout_load_javascript' ) );
  }


  /**
   * Load checkout javascript
   */
  static function checkout_load_javascript() {
    if ( is_checkout() ) {
      wp_register_script(
        'fraktguiden-kco',
        plugins_url( 'assets/js/bring-kco.js', dirname( __DIR__ ) ),
        array( 'jquery' ),
        Bring_Fraktguiden::VERSION,
        true
      );
      wp_enqueue_script( 'fraktguiden-kco' );
    }
  }


  /**
   * Ajax post code validation
   */
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
    // We have to forget the order because klarna does not allow changes to an existing order.
    WC()->session->set( 'kco_wc_order_id', null );
    // Set the billing and shipping code
    WC()->customer->set_shipping_postcode( $_REQUEST['post_code'] );
    WC()->customer->set_billing_postcode(  $_REQUEST['post_code'] );
    wp_send_json( $data );
  }

  /**
   * Before KCO
   */
  static function before_kco() {
    self::kco_post_code_html();
    // $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
    // if ( ! empty( $chosen_shipping_methods ) ) {
    //   return;
    // }
    // ob_start();
  }

  /**
   * After KCO
   */
  static function after_kco() {
    // $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
    // if ( ! empty( $chosen_shipping_methods ) ) {
    //   return;
    // }
    // ob_end_clean();
  }

  /**
   * Klarna Checkout post code selector HTML
   */
  static function kco_post_code_html() {
    $postcode = esc_html( WC()->customer->get_shipping_postcode() );
    ?>
    <div class="bring-enter-postcode">
      <h1>The absolute what?</h1>
      <form>
        <label><?php _e( 'Postcode', 'bring-fraktguiden' ); ?>
          <input class="bring-input input-text" type="text" value="<?php echo $postcode; ?>">
        </label>
        <input class="bring-button button" type="submit" value="<?php _e( 'Search', 'bring-fraktguiden' )?>">
      </form>
    </div>
    <?php
  }
}