<?php

namespace Bring_Fraktguiden;

trait Settings {


	/**
	 * Get setting
	 *
	 * @param  string       $key
	 * @param  string|mixed $default
	 * @return mixed
	 */
	public function get_setting( $key, $default = '' ) {
		return array_key_exists( $key, $this->settings ) ? $this->settings[ $key ] : $default;
	}

	/**
	 * Get Price Setting
	 *
	 * @param  string       $key
	 * @param  string|mixed $default
	 * @return float
	 */
	public function get_price_setting( $key, $default = '' ) {
		$price = floatval( $this->get_setting( $key, $default ) );
		$price = $this->calculate_excl_vat( $price );
		return $price;
	}
	/**
	 * Default settings.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		global $woocommerce;

		// @todo
		$wc_log_dir = '';
		if ( defined( 'WC_LOG_DIR' ) ) {
			$wc_log_dir = WC_LOG_DIR;
		}

		if ( $this->instance_id ) {
				$this->init_instance_form_fields();
				return;
		}

		$this->form_fields = [
			/**
			 * Plugin settings
			 */
			'plugin_settings'               => [
				'type'  => 'title',
				'title' => __( 'Bring Settings', 'bring-fraktguiden' ),
				'class' => 'separated_title_tab',
			],
			'pro_enabled'                   => [
				'title' => __( 'Activate PRO', 'bring-fraktguiden' ),
				'type'  => 'checkbox',
				'label' => '<em class="bring-toggle"></em>' . __( 'Enable/disable PRO features', 'bring-fraktguiden' ),
			],
			'test_mode'                     => [
				'title'   => __( 'Enable test mode', 'bring-fraktguiden' ),
				'type'    => 'checkbox',
				'label'   => '<em class="bring-toggle"></em>' . __( 'Use PRO in test-mode. Used for development', 'bring-fraktguiden' ),
				'default' => 'no',
			],

			'enabled'                       => array(
				'title'   => __( 'Enable', 'bring-fraktguiden' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Bring Fraktguiden', 'bring-fraktguiden' ),
				'default' => 'no',
			),

			/**
			 *  Required information
			 */

			'title'                         => array(
				'title'    => __( 'Title', 'bring-fraktguiden' ),
				'type'     => 'text',
				'desc_tip' => __( 'This controls the title which the user sees during checkout.', 'bring-fraktguiden' ),
				'default'  => __( 'Bring Fraktguiden', 'bring-fraktguiden' ),
			),

			'post_office'                   => array(
				'title'    => __( 'Post office', 'bring-fraktguiden' ),
				'type'     => 'checkbox',
				'label'    => __( 'Shipping from post office', 'bring-fraktguiden' ),
				'desc_tip' => __( 'Flag that tells whether the parcel is delivered at a post office when it is shipped.', 'bring-fraktguiden' ),
				'default'  => 'no',
			),

			'from_zip'                      => array(
				'title'       => __( 'From zip', 'bring-fraktguiden' ),
				'type'        => 'text',
				'placeholder' => __( 'ie: 0159', 'bring-fraktguiden' ),
				'desc_tip'    => __( 'This is the zip code of where you deliver from. For example, the post office.', 'bring-fraktguiden' ),
				'css'         => 'width: 8em;',
				'default'     => '',
			),

			'from_country'                  => array(
				'title'    => __( 'From country', 'bring-fraktguiden' ),
				'type'     => 'select',
				'desc_tip' => __( 'This is the country of origin where you deliver from (If omitted WooCommerce\'s default location will be used. See WooCommerce - Settings - General)', 'bring-fraktguiden' ),
				'class'    => 'chosen_select',
				'css'      => 'width: 400px;',
				'default'  => $woocommerce->countries->get_base_country(),
				'options'  => \Fraktguiden_Helper::get_nordic_countries(),
			),

			'handling_fee'                  => array(
				'title'             => __( 'Delivery Fee', 'bring-fraktguiden' ),
				'type'              => 'number',
				'placeholder'       => __( '0', 'bring-fraktguiden' ),
				'desc_tip'          => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', 'bring-fraktguiden' ),
				'css'               => 'width: 8em;',
				'default'           => '',
				'custom_attributes' => [
					'min' => '0',
				],
			),
			'evarsling'                     => array(
				'title'       => __( 'Recipient notification', 'bring-fraktguiden' ),
				'type'        => 'checkbox',
				'label'       => __( 'Recipient notification over SMS or E-Mail', 'bring-fraktguiden' ),
				'description' => __(
					'<strong>Note:</strong> If not enabled, Fraktguiden will add a fee for paper based recipient notification.<br/>
							If enabled, the recipient will receive notification over SMS or E-mail when the parcel has arrived.<br/>
							This only applies to <u>Bedriftspakke</u>, <u>Kliman&oslash;ytral Servicepakke</u> and <u>Bedriftspakke Ekspress-Over natten 09</u>',
					'bring-fraktguiden'
				),
				'default'     => 'no',
			),
			'availability'                  => array(
				'title'   => __( 'Method availability', 'bring-fraktguiden' ),
				'type'    => 'select',
				'default' => 'all',
				'class'   => 'availability',
				'options' => array(
					'all'      => __( 'All allowed countries', 'bring-fraktguiden' ),
					'specific' => __( 'Specific Countries', 'bring-fraktguiden' ),
				),
			),
			'countries'                     => array(
				'title'   => __( 'Specific Countries', 'bring-fraktguiden' ),
				'type'    => 'multiselect',
				'class'   => 'chosen_select',
				'css'     => 'width: 400px;',
				'default' => '',
				'options' => $woocommerce->countries->countries,
			),

			/**
			 * General options setting
			 */
			'general_options_title'         => [
				'type'        => 'title',
				'title'       => __( 'Shipping Options', 'bring-fraktguiden' ),
				'description' => __( 'Set the default prices for shipping rates and allow free shipping options on those services. You can also set the free shipping limit for each shipping service.', 'bring-fraktguiden' ),
				'class'       => 'separated_title_tab',
			],
			'calculate_by_weight'           => [
				'title'       => __( 'Ignore product dimensions', 'bring-fraktguiden' ),
				'label'       => __( 'Calculate shipping costs based on weight only', 'bring-fraktguiden' ),
				'default'     => 'no',
				'type'        => 'checkbox',
				'description' => __( 'The shipping cost is normally calculated by a combination of weight and dimensions in order to calculate number of parcels to send and gives a more accurate price. Use this option to disable calculation based on dimensions.', 'bring-fraktguiden' ),
			],
			'enable_multipack'              => [
				'title'       => __( 'Enable multipack', 'bring-fraktguiden' ),
				'label'       => __( 'Automatically pack items into several consignments', 'bring-fraktguiden' ),
				'default'     => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'Use multipack when shipping many small items. This setting is highly recommended for SERVICEPAKKE. This will automatically divide shipped items into boxes with sides no longer than 240 cm and weigh less than 35kg and a circumference less than 360cm. If you\'re shipping a mix of small and big items you should disable this setting. Eg. if you\'re using both SERVICEPAKKE and CARGO you should disable this.', 'bring-fraktguiden' ),
			],
			'service_name'                  => [
				'title'       => __( 'Display Service As', 'bring-fraktguiden' ),
				'type'        => 'select',
				'desc_tip'    => __( 'The service name displayed to the customer on the cart / checkout', 'bring-fraktguiden' ),
				'description' => __( 'Display name: <strong>"At the post office"</strong>,<br/>Product name: <strong>"Climate Neutral Service Pack"</strong>', 'bring-fraktguiden' ),
				'default'     => 'displayName',
				'options'     => [
					'displayName' => __( 'Display Name', 'bring-fraktguiden' ),
					'productName' => __( 'Product Name', 'bring-fraktguiden' ),
					'CustomName'  => __( 'Custom Name', 'bring-fraktguiden' ),
				],
			],
			'display_desc'                  => array(
				'title'    => __( 'Display Description', 'bring-fraktguiden' ),
				'type'     => 'checkbox',
				'label'    => __( 'Add description after the service', 'bring-fraktguiden' ),
				'desc_tip' => __( 'Show service description after the name of the service', 'bring-fraktguiden' ),
				'default'  => 'no',
			),
			'services'                      => array(
				'title'   => __( 'Services', 'bring-fraktguiden' ),
				'type'    => 'services_table',
				'class'   => 'chosen_select',
				'css'     => 'width: 400px;',
				'default' => '',
				'options' => \Fraktguiden_Helper::get_all_services(),
			),

			/**
			 * Sizing is important when packing products to ship.
			 * - Dimensions are limited and we need to use 23 x 13 x 1.
			 * - The weight should be at least 0.01
			 */
			'fallback_options'              => [
				'type'        => 'title',
				'title'       => __( 'Fallback options', 'bring-fraktguiden' ),
				'description' => __( 'With scenarios that fall outside of what Bring can handle, you are able to set prices for cases such as oversized items, minimum sized items, how many items you allow in one shipment and what should happen if Bring is not accessible.', 'bring-fraktguiden' ),
				'class'       => 'separated_title_tab',
			],
			'minimum_sizing_params'         => [
				'type'        => 'title',
				'title'       => __( 'Minimum shipping dimensions', 'bring-fraktguiden' ),
				'description' => __( 'Bring needs a default shipping size for when products do not contain any dimension information.', 'bring-fraktguiden' ),
				'class'       => 'bring-section-started',
			],
			'minimum_length'                => array(
				'title'             => __( 'Minimum Length in cm', 'bring-fraktguiden' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'placeholder'       => __( 'Must be at least 23cm', 'bring-fraktguiden' ),
				'desc_tip'          => __( 'The lowest length for a consignment', 'bring-fraktguiden' ),
				'default'           => '23',
				'custom_attributes' => [
					'min' => '1',
				],
			),
			'minimum_width'                 => array(
				'title'             => __( 'Minimum Width in cm', 'bring-fraktguiden' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'placeholder'       => __( 'Must be at least 13cm', 'bring-fraktguiden' ),
				'desc_tip'          => __( 'The lowest width for a consignment', 'bring-fraktguiden' ),
				'default'           => '13',
				'custom_attributes' => [
					'min' => '1',
				],
			),
			'minimum_height'                => array(
				'title'             => __( 'Minimum Height in cm', 'bring-fraktguiden' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'placeholder'       => __( 'Must be at least 1cm', 'bring-fraktguiden' ),
				'desc_tip'          => __( 'The lowest height for a consignment', 'bring-fraktguiden' ),
				'default'           => '1',
				'custom_attributes' => [
					'min' => '1',
				],
			),
			'minimum_weight'                => array(
				'title'             => __( 'Minimum Weight in kg', 'bring-fraktguiden' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'desc_tip'          => __( 'The lowest weight in kilograms for a consignment', 'bring-fraktguiden' ),
				'default'           => '0.01',
				'custom_attributes' => [
					'step' => '0.01',
					'min'  => '0.01',
				],
			),

			/**
			 * Lost / no connection section
			 */
			'no_connection_title'           => [
				'type'        => 'title',
				'title'       => __( 'Bring API offline / No connection', 'bring-fraktguiden' ),
				'description' => __( 'If Bring has any technical difficulties, it won\'t be able to fetch prices from the bring server.<br>In these cases, shipping will default to these settings:', 'bring-fraktguiden' ),
				'class'       => 'bring-separate-admin-section',
			],
			'no_connection_handling'        => array(
				'title'    => __( 'No API connection handling', 'bring-fraktguiden' ),
				'type'     => 'select',
				'desc_tip' => __( 'What pricing should be used if no connection can be made to the bring API', 'bring-fraktguiden' ),
				'default'  => 'no_rate',
				'options'  => [
					'no_rate'   => __( 'Do nothing', 'bring-fraktguiden' ),
					'flat_rate' => __( 'Custom flat rate', 'bring-fraktguiden' ),
				],
			),
			'no_connection_flat_rate_label' => array(
				'title'   => __( 'Shipping method Label to replace \'API Error\'', 'bring-fraktguiden' ),
				'type'    => 'text',
				'default' => __( 'Shipping', 'bring-fraktguiden' ),
			),
			'no_connection_flat_rate'       => array(
				'title'       => __( 'Shipping method cost for \'API Error\'', 'bring-fraktguiden' ),
				'css'         => 'width: 8em;',
				'type'        => 'number',
				'placeholder' => __( 'ie: 500', 'bring-fraktguiden' ),
				'default'     => '0',
			),
			'no_connection_rate_id'         => array(
				'title'   => __( 'Service to use for booking', 'bring-fraktguiden' ),
				'css'     => '',
				'type'    => 'select',
				'default' => '0',
				'options' => \Fraktguiden_Helper::get_all_services(),
			),

			/**
			 * Heavy items section
			 */
			'exceptions_title'              => [
				'type'        => 'title',
				'title'       => __( 'Heavy and oversized items', 'bring-fraktguiden' ),
				'description' => __( 'Set a flat rate for packages that exceed the maximum measurements allowed by Bring.', 'bring-fraktguiden' ),
				'class'       => 'bring-separate-admin-section',
			],
			'exception_handling'            => array(
				'title'    => __( 'Heavy item handling', 'bring-fraktguiden' ),
				'type'     => 'select',
				'desc_tip' => __( 'What method should be used to calculate post rates for items that exceeds the limits set by bring', 'bring-fraktguiden' ),
				'default'  => 'no_rate',
				'options'  => [
					'no_rate'   => __( 'Do nothing', 'bring-fraktguiden' ),
					'flat_rate' => __( 'Custom flat rate', 'bring-fraktguiden' ),
				],
			),
			'exception_flat_rate_label'     => array(
				'title'       => __( 'Shipping method Label for Heavy Items', 'bring-fraktguiden' ),
				'type'        => 'text',
				'placeholder' => __( 'ie: Cargo shipping', 'bring-fraktguiden' ),
				'default'     => __( 'Shipping', 'bring-fraktguiden' ),
			),
			'exception_flat_rate'           => array(
				'title'       => __( 'Shipping method cost for heavy items', 'bring-fraktguiden' ),
				'css'         => 'width: 8em;',
				'type'        => 'number',
				'placeholder' => __( 'ie: 500', 'bring-fraktguiden' ),
				'default'     => '0',
			),
			'exception_rate_id'             => array(
				'title'   => __( 'Service to use for booking', 'bring-fraktguiden' ),
				'css'     => '',
				'type'    => 'select',
				'default' => '0',
				'options' => \Fraktguiden_Helper::get_all_services(),
			),

			/**
			 * Max products section
			 */
			'max_products_title'            => [
				'type'        => 'title',
				'title'       => __( 'Product quantity limit for cart', 'bring-fraktguiden' ),
				'description' => __( 'When a cart reaches this limit, you can enable this shipping method.<br><em>For example, when ordering in bulk, the price for a shipping container may be a flat rate</em>', 'bring-fraktguiden' ),
				'class'       => 'bring-separate-admin-section',
			],
			'alt_handling'                  => array(
				'title'    => __( 'Maximum product handling', 'bring-fraktguiden' ),
				'type'     => 'select',
				'desc_tip' => __( 'We use a packing algorithm to pack items in three dimensions. This algorithm is computationally heavy and to prevent against DDoS attacks we\'ve implemented setting to control the maximum number of items that can be packed per order.', 'bring-fraktguiden' ),
				'default'  => 'no_rate',
				'options'  => [
					'no_rate'   => __( 'Do nothing', 'bring-fraktguiden' ),
					'flat_rate' => __( 'Custom flat rate', 'bring-fraktguiden' ),
				],
			),
			'max_products'                  => array(
				'title'       => __( 'Maximum product limit', 'bring-fraktguiden' ),
				'type'        => 'text',
				'css'         => 'width: 8em;',
				'placeholder' => 1000,
				'desc_tip'    => __( 'Maximum total quantity of products in the cart before offering a custom price', 'bring-fraktguiden' ),
				'default'     => 1000,
			),
			'alt_flat_rate_label'           => array(
				'title'       => __( 'Shipping method label', 'bring-fraktguiden' ),
				'type'        => 'text',
				'placeholder' => __( 'ie: Cargo shipping', 'bring-fraktguiden' ),
				'default'     => __( 'Shipping', 'bring-fraktguiden' ),
			),
			'alt_flat_rate'                 => array(
				'title'       => __( 'Shipping method cost', 'bring-fraktguiden' ),
				'type'        => 'text',
				'css'         => 'width: 8em;',
				'placeholder' => __( 'ie: 1500', 'bring-fraktguiden' ),
				'desc_tip'    => __( 'Offer a flat rate if the cart reaches max products or a product in the cart does not have the required dimensions', 'bring-fraktguiden' ),
				'default'     => 200,
			),
			'alt_flat_rate_id'              => array(
				'title'   => __( 'Service to use for booking', 'bring-fraktguiden' ),
				'css'     => '',
				'type'    => 'select',
				'default' => '0',
				'options' => \Fraktguiden_Helper::get_all_services(),
			),

			/**
			 * Developer settings
			 */
			'developer_settings'            => [
				'type'        => 'title',
				'title'       => __( 'Developer', 'bring-fraktguiden' ),
				'description' => __( 'For debugging and testing the plugin', 'bring-fraktguiden' ),
				'class'       => 'separated_title_tab',
			],
			'disable_stylesheet'            => array(
				'title'   => __( 'Disable stylesheet', 'bring-fraktguiden' ),
				'type'    => 'checkbox',
				'label'   => __( 'Remove fraktguiden styling from the checkout', 'bring-fraktguiden' ),
				'default' => 'no',
			),
			'debug'                         => array(
				'title'       => __( 'Debug mode', 'bring-fraktguiden' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable debug logs', 'bring-fraktguiden' ),
				'desc_tip'    => __( 'Issues from the Bring API will be logged here', 'bring-fraktguiden' ),
				'description' => __( 'Bring Fraktguiden logs will be saved in', 'bring-fraktguiden' ) . ' <code>' . $wc_log_dir . '</code><a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' . __( 'Click here to see the logs' ) . '</a>',
				'default'     => 'no',
			),
			'enable_kco_support'            => array(
				'title'       => __( 'Klarna checkout support', 'bring-fraktguiden' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable legacy Klarna support', 'bring-fraktguiden' ),
				'desc_tip'    => __( 'Loads additional JavaScript on the checkout.', 'bring-fraktguiden' ),
				'description' => __( 'Bring Fraktguiden will hide Klarna Checkout until a shipping method is selected in order to ensure that a shipping method has been selected before payment is made.', 'bring-fraktguiden' ) . '</a>',
				'default'     => \Fraktguiden_Helper::get_kco_support_default(),
			),
			'system_information'            => array(
				'title'       => __( 'Debug System information', 'bring-fraktguiden' ),
				'type'        => 'hidden',
				'label'       => __( 'Enable debug logs', 'bring-fraktguiden' ),
				'desc_tip'    => __( 'We may ask for this information if you require support', 'bring-fraktguiden' ),
				'description' => sprintf( '<a href="%s" target="_blank">%s</a>', admin_url( 'admin-ajax.php?action=bring_system_info' ), __( 'View system info', 'bring-fraktguiden' ) ),
			),

			/**
			 * MyBring API settings
			 */
			'mybring_title'                 => [
				'title'       => __( 'Mybring.com API', 'bring-fraktguiden' ),
				'description' => __( 'If you are a Mybring user you can enter your API credentials for additional features. API authentication is required for some services such as "Package in mailbox (PAKKE_I_POSTKASSEN)".', 'bring-fraktguiden' ),
				'class'       => 'separated_title_tab',
				'type'        => 'title',
			],
			'mybring_api_uid'               => [
				'title'       => __( 'API User ID', 'bring-fraktguiden' ),
				'type'        => 'text',
				'label'       => __( 'API User ID', 'bring-fraktguiden' ),
				'placeholder' => 'bring@example.com',
			],
			'mybring_api_key'               => [
				'title'       => __( 'API Key', 'bring-fraktguiden' ),
				'type'        => 'text',
				'label'       => __( 'API Key', 'bring-fraktguiden' ),
				'placeholder' => '4abcdef1-4a60-4444-b9c7-9876543219bf',
			],
			'mybring_customer_number'       => [
				'title'       => __( 'Customer number', 'bring-fraktguiden' ),
				'type'        => 'text',
				'label'       => __( 'Customer number', 'bring-fraktguiden' ),
				'placeholder' => 'PARCELS_NORWAY-100########',
			],
		];

		if ( class_exists( 'WC_Shipping_Zones' ) ) {
			unset( $this->form_fields['availability'] );
			unset( $this->form_fields['enabled'] );
			unset( $this->form_fields['countries'] );
		}
	}

	/**
	 * Initialize form fields
	 */
	public function init_instance_form_fields() {
		$this->form_fields = [];
	}

	/**
	 * Display settings in HTML
	 *
	 * @return void
	 */
	public function admin_options() {
		?>
		<!-- -->
		<h3 class="bring-separate-admin-section"><?php esc_html( $this->method_title ); ?></h3>
		<p><?php esc_html_e( 'Bring Fraktguiden is a shipping method using Bring.com to calculate rates.', 'bring-fraktguiden' ); ?></p>
		<!-- -->

		<div class="hash-tabs fraktguiden-options" style="display:none;">
			<article class="tab-container">
				<nav class="tab-nav" role="tablist"><ul></ul><div style="clear:both;"></div></nav>
				<div class="tab-pane-container"></div>
			</article>
		</div>

		<table class="form-table">
			<?php if ( $this->is_valid_for_use() ) : ?>
				<?php $this->generate_settings_html( $this->form_fields ); ?>
			<?php else : ?>
				<tr><td><div class="inline error"><p>
						<strong><?php esc_html_e( 'Gateway Disabled', 'bring-fraktguiden' ); ?></strong>
						<br/> <?php printf( __( 'Bring shipping method requires <strong>weight &amp; dimensions</strong> to be enabled. Please enable them on the <a href="%1$s">Catalog tab</a>. <br/> In addition, Bring also requires the <strong>Norweigian Krone</strong> currency. Choose that from the <a href="%2$s">General tab</a>', 'bring-fraktguiden' ), 'admin.php?page=woocommerce_settings&tab=catalog', 'admin.php?page=woocommerce_settings&tab=general' ); ?>
					</p></div></td></tr>
			<?php endif; ?>
		</table>

		<script>
		jQuery( function( $ ) {
			// Move settings into tabs
			$( '.separated_title_tab' ).each( function() {
				var id = $( this ).attr('id');
				var text = $( this ).text();
				// Create a new tab/list item
				var elem = $('<li>').append( $( '<a>' ).attr( {
					'href': '#' + id
				} ).text( text ) );
				// Append it to the tab-navigation
				$( '.hash-tabs .tab-nav ul' ).append( elem );

				// Create a new tab-panel
				elem = $( '<section>' ).attr( {
					'id': id
				} ).hide();


				// Find the content for this panel
				// It's always the next p's and <table>
				var content = $( this ).nextUntil( '.separated_title_tab' );
				elem.append( $(this), content );

				// Place the panel in the panels container
				$( '.hash-tabs .tab-pane-container' ).append( elem );
			} );

			var targeted = location.hash;

			// Make the tabs work
			$( '.fraktguiden-options' ).hashTabs();

			if ( ! targeted ) {
				window.scrollTo(0, 0);
				setTimeout(function() {
					window.scrollTo(0, 0);
				}, 1);
			}

			$( '.fraktguiden-options' ).show();

			var save = $( 'p.submit');
			$( '.fraktguiden-options' ).after( save );

		} );

		jQuery( function( $ ) {
			function toggle_test_mode() {
				var is_checked = $( '#woocommerce_bring_fraktguiden_pro_enabled' ).prop( 'checked' );
				$( '#woocommerce_bring_fraktguiden_test_mode' ).closest( 'tr' ).toggle( is_checked );
				// Toggle the menu items for pickup points and bring booking
				$( '#5, #6' ).toggle( is_checked );
			}
			$( '#woocommerce_bring_fraktguiden_pro_enabled' ).change( toggle_test_mode );
			toggle_test_mode();
		} );
		</script>
		<?php
	}

	/**
	 * Process MyBring API credentials
	 *
	 * @return void
	 */
	public function process_mybring_api_credentials() {
		$api_uid_key         = $this->get_field_key( 'mybring_api_uid' );
		$api_key_key         = $this->get_field_key( 'mybring_api_key' );
		$customer_number_key = $this->get_field_key( 'mybring_customer_number' );

		$api_uid         = $_POST[ $api_uid_key ];
		$api_key         = $_POST[ $api_key_key ];
		$customer_number = $_POST[ $customer_number_key ];

		$fields = [
			'api_uid',
			'api_key',
			'customer_number',
		];

		if ( ! $api_uid || ! $api_key ) {
			\Fraktguiden_Admin_Notices::add_missing_api_credentials_notice();
			return;
		}

		\Fraktguiden_Admin_Notices::remove_missing_api_credentials_notice();

		if ( ! $customer_number && \Fraktguiden_Helper::booking_enabled() ) {
			$this->mybring_error( __( 'You need to enter a customer number', 'bring-fraktguiden' ) );
			return;
		}

		$key  = get_option( 'mybring_authenticated_key' );
		$hash = md5( $api_uid . $api_key . $customer_number );

		if ( $key === $hash ) {
			// We already tried this combination, skip this for re-saves.
			return;
		}

		// Try to atuhenticate.
		$request           = new \WP_Bring_Request();
		$params            = $this->create_standard_url_params();
		$params['product'] = 'SERVICEPAKKE';
		$params['weight']  = 100;
		$response          = $request->get( self::SERVICE_URL, $params );
		if ( 200 != $response->status_code ) {
			$this->mybring_error( $response->body );
			return;
		}

		$result = json_decode( $response->body, true );

		/*
		Check for customer_number authentication error
		May the programming gods have mercy. Bring does not have a authentication endpoint
		and authentication credentials has to be passed on every request. The shipping API is
		simply the easiest api to test against, but only certain products actually require
		auth. I've picked "Servicepakke" because it seems to be the most reliable (hasn't
		change the last year). Now I wouldn't normally rant like this, I mean it would be
		fine if the API just threw a 400 error if you half authenticate, but NO, it just
		silently fails and doesn't give the rates. UGH! Here's a hacky workaround. I'm
		reading the TraceMessage for all the results to see if the customer_number was
		authenticated.
		*/
		if ( isset( $result['traceMessages'] ) ) {
			foreach ( $result['traceMessages'] as $messages ) {
				if ( ! is_array( $messages ) ) {
					$messages = [ $messages ];
				}
				foreach ( $messages as $message ) {
					if ( false === strpos( $message, 'does not have access to customer' ) ) {
						continue;
					}
					$this->mybring_error( $message );
					$this->validation_messages = sprintf( '<p class="error-message">%s</p>', $message );
					return;
				}
			}
		}

		// Success. All authentication methods have passed.
		update_option( 'mybring_authenticated_key', $hash, true );
	}

	/**
	 * Add MyBring error
	 *
	 * @param  string $message Error message.
	 * @return void
	 */
	public function mybring_error( $message ) {
		if ( strpos( $message, 'Authentication failed.' ) === 0 ) {
			$message = sprintf( '<strong>%s:</strong> %s.', __( 'MyBring Authentication failed', 'bring-fraktguiden' ), __( 'Couldn\'t connect to Bring with your API credentials. Please check that they are correct', 'bring-fraktguiden' ) );
		}
		\Fraktguiden_Admin_Notices::add_notice( 'mybring_error', $message, 'error' );
	}

	/**
	 * Validate services table field
	 *
	 * @param string      $key Key.
	 * @param string|null $value Value.
	 */
	public function validate_services_table_field( $key, $value = null ) {
		return $this->service_table->validate_services_table_field( $key, $value );
	}

	/**
	 * Generate services table HTML
	 */
	public function generate_services_table_html() {
		return $this->service_table->generate_services_table_html();
	}
}
