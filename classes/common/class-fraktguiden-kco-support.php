<?php

use Bring_Fraktguiden\Postcode_Validation;

class Fraktguiden_KCO_Support {

	/**
	 * Setup
	 */
	public static function setup() {
		if ( ! class_exists( 'Klarna_Checkout_For_WooCommerce' ) ) {
			return;
		}

		$kco_settings = get_option( 'woocommerce_kco_settings' );
		if ( empty( $kco_settings ) || 'yes' !== $kco_settings['shipping_methods_in_iframe'] ) {
			add_action( 'woocommerce_review_order_before_shipping', __CLASS__ . '::before_kco_shipping', 50 );
		}
		add_action( 'kco_wc_after_order_review', __CLASS__ . '::before_kco', 50 );
		add_action( 'wp_ajax_bring_post_code_validation', __CLASS__ . '::ajax_post_code_validation' );
		add_action( 'wp_ajax_nopriv_bring_post_code_validation', __CLASS__ . '::ajax_post_code_validation' );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::checkout_load_javascript' );
	}

	/**
	 * Load checkout javascript
	 */
	public static function checkout_load_javascript() {
		if ( is_checkout() ) {
			wp_register_script(
				'fraktguiden-kco',
				plugins_url( 'assets/js/bring-kco.js', dirname( __DIR__ ) ),
				array( 'jquery' ),
				Bring_Fraktguiden::VERSION,
				true
			);
			wp_enqueue_script( 'fraktguiden-kco' );
			wp_localize_script(
				'fraktguiden-kco',
				'_fraktguiden_kco',
				[
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'klarna_checkout_nonce' => wp_create_nonce( 'klarna_checkout_nonce' ),
				]
			);
		}
	}

	/**
	 * Ajax post code validation
	 */
	public static function ajax_post_code_validation() {
		$response = Postcode_Validation::get_postcode_information( $_REQUEST['post_code'], $_REQUEST['country'] );
		if ( is_wp_error( $response ) ) {
			wp_send_json( [
				'error' => implode( "\n", $response->errors ),
			] );
		}
		if ( empty( $response['response']['code'] ) ||  empty( $response['body'] ) ) {
			wp_send_json( [ 'error' => 'The bring API gave an empty response.' ] );
		}
		if ( 200 !== $response['response']['code'] ) {
			wp_send_json( [ 'error' => 'Connection to the bring API failed. HTTP code: ' . $response['response']['code']  ] );
		}
		$data = json_decode( $response['body'], true );
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
	 * Get classes
	 *
	 * @return string Classes
	 */
	public static function get_classes() {
		$postcode = esc_html( WC()->customer->get_shipping_postcode() );
		$classes  = 'bring-enter-postcode';
		// var_dump( $postcode ); die;
		if ( ! $postcode && WC()->cart->needs_shipping() ) {
			$classes .= ' bring-required';
		}
		return $classes;
	}

	/**
	 * Before KCO
	 */
	public static function before_kco_shipping() {
		if ( did_action( 'woocommerce_review_order_before_shipping' ) ) {
			return;
		}
		$classes = self::get_classes();
		?>
		<tr class="<?php echo esc_html( $classes ); ?>">
			<td colspan="4">
				<div>
					<?php self::kco_post_code_html(); ?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Before KCO Shipping
	 */
	public static function before_kco() {
		$classes = self::get_classes();
		?>
		<div class="<?php echo esc_html( $classes ); ?>">
			<?php self::kco_post_code_html(); ?>
		</div>
		<?php
	}

	/**
	 * Klarna Checkout post code selector HTML
	 */
	public static function kco_post_code_html() {
		$postcode  = esc_html( WC()->customer->get_shipping_postcode() );
		$countries = WC()->countries->get_shipping_countries();
		$country   = WC()->customer->get_shipping_country();
		?>
		<?php do_action( 'bring_fraktguiden_before_kco_postcode' ); ?>
		<?php if ( count( $countries ) > 1 ) : ?>
			<label for="bring-country"><?php _e( 'Country', 'woocommerce' ); ?></label>
			<div class="bring-search-box">
				<select id="bring-country" name="bring-country">
					<?php foreach ( $countries as $key => $_country ) : ?>
						<option value="<?php echo $key; ?>" <?php echo ( $country == $key ) ? 'selected="selected"' : ''; ?>>
							<?php echo esc_html( $_country ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php else : ?>
			<input type="hidden" id="bring-country" name="bring-country" value="<?php echo key( $countries ); ?>">
		<?php endif; ?>
		<label for="bring-post-code"><?php _e( 'Enter postcode (4 digits)', 'bring-fraktguiden-for-woocommerce' ); ?></label>
		<div class="bring-search-box">
			<input id="bring-post-code" class="bring-input input-text" type="text" placeholder="<?php _e( '0000', 'bring-fraktguiden-for-woocommerce' ); ?>"  name="bring-post-code" value="<?php echo $postcode; ?>">
			<input class="bring-button button" type="submit" value="<?php _e( 'Get delivery methods', 'bring-fraktguiden-for-woocommerce' ); ?>">
		</div>
		<?php do_action( 'bring_fraktguiden_after_kco_postcode' ); ?>
		<?php
	}
}
