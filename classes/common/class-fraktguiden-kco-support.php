<?php


class Fraktguiden_KCO_Support {

  static function setup() {
    if ( ! class_exists( 'Klarna_Checkout_For_WooCommerce' ) ) {
      return;
    }
    add_action( 'kco_wc_before_snippet', __CLASS__ .'::before_kco' );
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
    if ( WC()->session->get( 'bring_fraktguiden_reset_kco' ) ) {
      // We have to forget the order because klarna does not allow changes to an existing order.
      WC()->session->set( 'kco_wc_order_id', null );
    } else {
      // On a blank session the order has not been changed by the user yet
      // Only reset it on subsequent requests
      WC()->session->set( 'bring_fraktguiden_reset_kco', true );
    }
    // Set the billing and shipping code
    WC()->customer->set_shipping_postcode( $_REQUEST['post_code'] );
    WC()->customer->set_billing_postcode( $_REQUEST['post_code'] );
    WC()->customer->set_shipping_country( $_REQUEST['country'] );
    wp_send_json( $data );
  }

  /**
   * Before KCO
   */
  static function before_kco() {
    self::kco_post_code_html();
  }

  /**
   * Klarna Checkout post code selector HTML
   */
  static function kco_post_code_html() {
    $postcode = esc_html( WC()->customer->get_shipping_postcode() );
    $countries = WC()->countries->get_shipping_countries();
    $country =  WC()->customer->get_shipping_country();
    ?>
    <div class="bring-enter-postcode">
      <form>
        <?php if ( count( $countries ) > 1 ): ?>
          <label for="bring-country"><?php _e( 'Country', 'woocommerce' ); ?></label>
          <div class="bring-search-box">
              <select id="bring-country" name="bring-country">
              <?php foreach ( $countries as $key => $_country ): ?>
                <option value="<?php echo $key; ?>" <?php echo ( $country == $key ) ? 'selected="selected"':''; ?>>
                  <?php echo $_country; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php else: ?>
          <input type="hidden" id="bring-country" name="bring-country" value="<?php echo key( $countries ); ?>">
        <?php endif; ?>
        <label for="bring-post-code"><?php _e( 'Postcode', 'woocommerce' ); ?></label>
        <div class="bring-search-box">
          <input id="bring-post-code" class="bring-input input-text" type="text" name="bring-post-code" value="<?php echo $postcode; ?>">
          <input class="bring-button button" type="submit" value="<?php _e( 'Search', 'bring-fraktguiden' )?>">
        </div>
      </form>
    </div>
    <?php
  }
}