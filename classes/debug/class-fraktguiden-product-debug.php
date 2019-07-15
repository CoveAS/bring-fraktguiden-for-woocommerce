<?php

/**
 * Fraktguiden Product Debug
 */
class Fraktguiden_Product_Debug {

	public static function setup() {
		add_action( 'add_meta_boxes', __CLASS__ . '::add_events_metaboxes' );
		add_action( 'wp_ajax_bring_debug_product_rates', __CLASS__ . '::ajax_get_rates' );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
	}

	public static function add_events_metaboxes( $post_type ) {
		if ( 'product' != $post_type ) {
			return;
		}
		add_meta_box(
			'bring_fraktguiden_product_tester',
			'Bring Fraktguiden Product Tester',
			__CLASS__ . '::layout_of_meta_box_content'
		);
	}

	/**
	 * Admin enqueue script
	 * Add custom styling and javascript to the admin options
	 *
	 * @param  string $hook
	 */
	public static function admin_enqueue_scripts( $hook ) {
		if ( 'post.php' !== $hook || 'product' !== get_post_type() ) {
			return;
		}
		$plugin_path = dirname( __DIR__ );
		wp_enqueue_script(
			'bring-admin-debug-js',
			plugins_url( 'assets/js/bring-fraktguiden-admin-debug.js', $plugin_path ),
			[ 'jquery' ],
			'1.0.0'
		);
		wp_localize_script(
			'bring-admin-debug-js',
			'bring_fraktguiden_debug',
			[
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'id'              => get_the_ID(),
				'loading_message' => esc_html( __( 'Loading.', 'bring-fraktguiden' ) ),
			]
		);
	}

	public static function layout_of_meta_box_content() {
		?>

	<div class="bring-debug">

	  <div class="test-plane">
		<h4><?php esc_html_e( __( 'Can this product be shipped with bring?', 'bring-fraktguiden' ) ); ?></h4>
		<p><?php esc_html_e( __( 'Here\'s some information about your product', 'bring-fraktguiden' ) ); ?></p>
		<?php self::render(); ?>
		<h4><?php esc_html_e( __( 'API response', 'bring-fraktguiden' ) ); ?></h4>
		<div class="bring-debug__rates"></div>
	  </div>

	  <div class="test-plane">
		<h4>Run the test</h4>
		<p>You can check if Bring accepts this product for shipping</p>
		<label>
		  <span>Post code</span>
		  <input class="bring-debug__post-code" type="text" name="bring-debug-post-code" value="<?php echo esc_html( WC()->countries->get_base_postcode() ); ?>">
		</label>
		<?php
		woocommerce_form_field(
			'bring-debug-country',
			[
				'label' => 'Country',
				'class' => [ 'bring-debug__country' ],
				'type'  => 'country',
			],
			WC()->countries->get_base_country()
		);
		?>
		  <?php printf( '<a class="button button-primary button-large get-rates" href="%s">%s</a>', '#test', 'Test bring' ); ?>
	  </div>
	</div>
	<style>
	  .bring-debug__pickup-point > div,
	  .bring-debug {
		display: grid;
		grid-template-columns: 2fr 1fr;
		grid-gap: 1rem;
	  }
	  .bring-debug__rate span {
		float: right;
	  }
	  .bring-debug__rate {
		background-color: #DCEDC8;
		padding: 0.4rem 0.5rem 0.5rem;
		margin-bottom: 0.25rem;
	  }
	  .bring-debug__trace-message {
		background: #fff9d9;
		padding: 0.4rem 0.5rem 0.5rem;
		border: 1px solid #a0935f;
	  }

	</style>
		<?php
	}

	public static function render() {
		WC()->frontend_includes();
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
		WC()->session  = new $session_class();
		WC()->customer = new WC_Customer( get_current_user_id(), false );
		$post          = get_post();
		// $cart_id = $this->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
		$product = wc_get_product( $post->ID );
		$package = self::get_package( $product );

		$dims = $product->get_length() && $product->get_width() && $product->get_height();
		if ( ! $dims ) {
			echo "No dimensions.\n";
		}
		$weight = $product->get_weight();
		if ( ! $weight ) {
			echo "No weight.\n";
		}
		if ( ! $weight && ! $dims ) {
			echo "The product needs to have either dimensions or weight specified.\n";
			return;
		}

		$zone = wc_get_shipping_zone( $package );
		if ( ! $zone ) {
			echo "There are no shipping zone matches.\n";
			return;
		}
		$bring = self::get_bring( $zone );
		if ( ! $bring ) {
			echo "Bring is not enabled for the current Zone.\n";
			return;
		}

		esc_html_e( __( 'No problems detected', 'bring-fraktguiden' ) );
	}

	public static function get_package( $product, $country = false, $post_code = false ) {
		if ( false === $country ) {
			$country = WC()->countries->get_base_postcode();
		}
		if ( false === $post_code ) {
			$post_code = WC()->countries->get_base_postcode();
		}
		return [
			'destination' => [
				'country'  => $country,
				'state'    => '',
				'postcode' => $post_code,
			],
			'contents'    => [
				[
					'key'          => 'NAN',
					'product_id'   => $product->get_id(),
					'variation_id' => null,
					'variation'    => null,
					'quantity'     => 1,
					'data'         => $product,
				],
			],
		];
	}

	public static function get_bring( $zone ) {
		$bring   = false;
		$methods = $zone->get_shipping_methods();
		foreach ( $methods as $method ) {
			if ( get_class( $method ) == 'WC_Shipping_Method_Bring_Pro' ) {
				$bring = $method;
				break;
			}
			if ( get_class( $method ) == 'WC_Shipping_Method_Bring' ) {
				$bring = $method;
				break;
			}
		}
		return $bring;
	}

	public static function ajax_get_rates() {
		$product   = wc_get_product( $_GET['id'] );
		$country   = sanitize_text_field( $_GET['country'] );
		$post_code = sanitize_text_field( $_GET['post_code'] );
		$package   = self::get_package( $product, $country, $post_code );
		$zone      = wc_get_shipping_zone( $package );
		$bring     = self::get_bring( $zone );
		$rates     = $bring->get_rates_for_package( $package );
		$messages  = $bring->get_trace_messages();
		echo '<ul >';
		foreach ( $messages as $message ) {
			printf( '<li class="bring-debug__trace-message">%s</li>', $message );
		}
		if ( empty( $rates ) ) {
			echo "<li class=\"bring-debug__trace-message\">Bring did not return any shipping rates.</li>\n";
			return;
		}
		echo '</ul>';
		foreach ( $rates as $rate_id => $rate ) {
			printf(
				'<div class="bring-debug__rate"><strong>%s</strong> <span>%s</span><div class="bring-debug__pickup-point">%s</div></div>',
				$rate->get_label(),
				wc_price( $rate->get_cost() ),
				self::get_pickup_point_meta( $rate )
			);
		}
		die;
	}

	public static function get_pickup_point_meta( $rate ) {
		$meta = $rate->get_meta_data();
		if ( empty( $meta['pickup_point_data'] ) ) {
			return '';
		}

		$pickup_point_data = $meta['pickup_point_data'];

		$html = '';

		$fields = [
			'address'            => __( 'Address', 'bring-fraktguiden' ),
			'postalCode'         => __( 'Post code', 'bring-fraktguiden' ),
			'city'               => __( 'City', 'bring-fraktguiden' ),
			'countryCode'        => __( 'Country code', 'bring-fraktguiden' ),
			'municipality'       => __( 'Municipality', 'bring-fraktguiden' ),
			'county'             => __( 'County', 'bring-fraktguiden' ),
			'visitingAddress'    => __( 'Visiting address', 'bring-fraktguiden' ),
			'visitingPostalCode' => __( 'Visiting post code', 'bring-fraktguiden' ),
			'visitingCity'       => __( 'Visiting city', 'bring-fraktguiden' ),
		];
		foreach ( $fields as $field => $label ) {
			$html .= sprintf(
				'<div class="pickup-point__%s"><div class="pickup-point__label">%s</div><div class="pickup-point__value">%s</div></div>',
				esc_html( $field ),
				esc_html( $label ),
				esc_html( $pickup_point_data[ $field ] )
			);
		}

		$fields = [
			'openingHoursNorwegian',
			'openingHoursEnglish',
			'openingHoursFinnish',
			'openingHoursDanish',
			'openingHoursSwedish',
		];

		return $html;
	}
}
