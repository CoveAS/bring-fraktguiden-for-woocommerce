<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Traits;

use Bring_Fraktguiden\Common\Fraktguiden_Admin_Notices;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;

trait Settings {

	/**
	 * Get setting
	 *
	 * @param string       $key     Key.
	 * @param string|mixed $default Default.
	 *
	 * @return mixed
	 */
	public function get_setting( $key, $default = '' ) {
		return array_key_exists( $key, $this->settings ) ? $this->settings[ $key ] : $default;
	}

	/**
	 * Get Price Setting
	 *
	 * @param string       $key     Key.
	 * @param string|mixed $default Default.
	 *
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
			'plugin_settings'                   => [
				'type'  => 'title',
				'title' => __( 'Bring Settings', 'bring-fraktguiden-for-woocommerce' ),
				'class' => 'separated_title_tab',
			],
			'pro_enabled'                       => [
				'title' => __( 'Activate PRO', 'bring-fraktguiden-for-woocommerce' ),
				'type'  => 'checkbox',
				'label' => '<em class="bring-toggle"></em>' . __( 'Enable/disable PRO features',
						'bring-fraktguiden-for-woocommerce' ),
				'class' => 'bring-toggle-checkbox',
			],
			'test_mode'                         => [
				'title'    => __( 'Enable test mode', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'checkbox',
				'label'    => '<em class="bring-toggle"></em>' . __( 'Use PRO in test-mode. Used for development and testing.',
						'bring-fraktguiden-for-woocommerce' ),
				'desc_tip' => __( 'This setting let\'s you use PRO features without a license and displays a message on the checkout page that this is a test-site',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'no',
				'class'    => 'bring-toggle-checkbox',
			],
			'enabled'                           => [
				'title'   => __( 'Enable', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Bring Fraktguiden', 'bring-fraktguiden-for-woocommerce' ),
				'default' => 'no',
			],

			/**
			 *  Required information
			 */
			'title'                             => [
				'title'    => __( 'Title', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => __( 'This controls the title which the user sees during checkout.',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => __( 'Bring Fraktguiden', 'bring-fraktguiden-for-woocommerce' ),
			],
			'language'                          => [
				'title'    => __( 'Language', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'Choose which language to ask the API to use', 'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'website',
				'options'  => [
					'website' => __( 'Use website language', 'bring-fraktguiden-for-woocommerce' ),
					'no'      => __( 'Norwegian', 'bring-fraktguiden-for-woocommerce' ),
					'en'      => __( 'English', 'bring-fraktguiden-for-woocommerce' ),
					'se'      => __( 'Swedish', 'bring-fraktguiden-for-woocommerce' ),
					'da'      => __( 'Danish', 'bring-fraktguiden-for-woocommerce' ),
					'fi'      => __( 'Finnish', 'bring-fraktguiden-for-woocommerce' ),
				]
			],
			'post_office'                       => [
				'title'    => __( 'Post office', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'checkbox',
				'label'    => __( 'Shipping from post office', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip' => __( 'Flag that tells whether the parcel is delivered at a post office when it is shipped.',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'no',
			],
			'from_zip'                          => [
				'title'       => __( 'From zip', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'placeholder' => __( 'ie: 0159', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'    => __( 'This is the zip code of where you deliver from. For example, the post office.',
					'bring-fraktguiden-for-woocommerce' ),
				'css'         => 'width: 8em;',
				'default'     => '',
			],
			'from_country'                      => [
				'title'    => __( 'From country', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'This is the country of origin where you deliver from (If omitted WooCommerce\'s default location will be used. See WooCommerce - Settings - General)',
					'bring-fraktguiden-for-woocommerce' ),
				'class'    => 'chosen_select',
				'css'      => 'width: 400px;',
				'default'  => $woocommerce->countries->get_base_country(),
				'options'  => Fraktguiden_Helper::get_nordic_countries(),
			],
			'handling_fee'                      => [
				'title'             => __( 'Delivery Fee', 'bring-fraktguiden-for-woocommerce' ),
				'type'              => 'number',
				'placeholder'       => __( '0', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'          => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.',
					'bring-fraktguiden-for-woocommerce' ),
				'css'               => 'width: 8em;',
				'default'           => '',
				'custom_attributes' => [
					'min' => '0',
				],
			],
			'availability'                      => [
				'title'   => __( 'Method availability', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'select',
				'default' => 'all',
				'class'   => 'availability',
				'options' => [
					'all'      => __( 'All allowed countries', 'bring-fraktguiden-for-woocommerce' ),
					'specific' => __( 'Specific Countries', 'bring-fraktguiden-for-woocommerce' ),
				],
			],
			'countries'                         => [
				'title'   => __( 'Specific Countries', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'multiselect',
				'class'   => 'chosen_select',
				'css'     => 'width: 400px;',
				'default' => '',
				'options' => $woocommerce->countries->countries,
			],

			/**
			 * General options setting
			 */
			'general_options_title'             => [
				'type'        => 'title',
				'title'       => __( 'Shipping Options', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Set the default prices for shipping rates and allow free shipping options on those services. You can also set the free shipping limit for each shipping service.',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'separated_title_tab',
			],
			'calculate_by_weight'               => [
				'title'       => __( 'Ignore product dimensions', 'bring-fraktguiden-for-woocommerce' ),
				'label'       => __( 'Calculate shipping costs based on weight only',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => 'no',
				'type'        => 'checkbox',
				'description' => __( 'The shipping cost is normally calculated by a combination of weight and dimensions in order to calculate number of parcels to send and gives a more accurate price. Use this option to disable calculation based on dimensions.',
					'bring-fraktguiden-for-woocommerce' ),
			],
			'enable_multipack'                  => [
				'title'       => __( 'Enable multipack', 'bring-fraktguiden-for-woocommerce' ),
				'label'       => __( 'Automatically pack items into several consignments',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'Use multipack when shipping many small items. This setting is highly recommended for SERVICEPAKKE. This will automatically divide shipped items into boxes with sides no longer than 240 cm and weigh less than 35kg and a circumference less than 360cm. If you\'re shipping a mix of small and big items you should disable this setting. Eg. if you\'re using both SERVICEPAKKE and CARGO you should disable this.',
					'bring-fraktguiden-for-woocommerce' ),
			],
			'shipping_options_full_width'       => [
				'title'       => __( 'Full width', 'bring-fraktguiden-for-woocommerce' ),
				'label'       => __( 'Display shipping options in full-width column.',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'By default WooCommerce displays all the shipping options in a table with two columns, one for the title, "Shipping", and one for the options. This means that the options gets squished into a very tight space. Enable this option to display the shipping options full-width.',
					'bring-fraktguiden-for-woocommerce' ),
			],
			'display_desc'                      => [
				'title'    => __( 'Enhanced descriptions', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'checkbox',
				'label'    => __( 'Display detailed description and additional information for shipping methods in cart totals.',
					'bring-fraktguiden-for-woocommerce' ),
				'desc_tip' => __( 'To help customers, the service description will help explain how the services differ from each other',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'yes',
			],
			'use_customer_number_to_get_prices' => [
				'title'   => __( 'Use customer number', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Use main mybring customer number to get prices from the api.',
					'bring-fraktguiden-for-woocommerce' ),
				'desc_tip' => __( 'Using the customer number when querying the API will return your agreement price (net price), with bring. This is usually cheaper than the list price and you can choose which price to display during checkout. This setting will be overridden by specifying a customer number on individual services.',
					'bring-fraktguiden-for-woocommerce' ),
				'default' => 'yes',
			],
			'price_to_use'                      => [
				'title'   => __( 'Price to use', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'select',
				'options' => [
					'net'  => __( 'Net price' ),
					'list' => __( 'List price' ),
				],
				'desc_tip' => __( 'Net price is the agreement price with bring and will only be used if a customer number is used for the API request',
					'bring-fraktguiden-for-woocommerce' ),
				'default' => 'net',
			],
			'service_sorting'                   => [
				'title'    => __( 'Sorting', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'select',
				'css'      => 'width: 400px;',
				'default'  => 'price',
				'desc_tip' => __( 'The order in which shipping options should be displayed.',
					'bring-fraktguiden-for-woocommerce' ),
				'options'  => [
					'price'      => __( 'Price, low to high', 'bring-fraktguiden-for-woocommerce' ),
					'price_desc' => __( 'Price, high to low', 'bring-fraktguiden-for-woocommerce' ),
					'none'       => __( 'No sorting', 'bring-fraktguiden-for-woocommerce' ),
				],
			],
			'services'                          => [
				'title'   => __( 'Bring products', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'services_table',
				'class'   => 'chosen_select',
				'css'     => 'width: 400px;',
				'default' => '',
				'options' => Fraktguiden_Helper::get_all_services(),
			],

			/**
			 * Sizing is important when packing products to ship.
			 * - Dimensions are limited and we need to use 23 x 13 x 1.
			 * - The weight should be at least 0.01
			 */
			'fallback_options'                  => [
				'type'        => 'title',
				'title'       => __( 'Fallback options', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'With scenarios that fall outside of what Bring can handle, you are able to set prices for cases such as oversized items, minimum sized items, how many items you allow in one shipment and what should happen if Bring is not accessible.',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'separated_title_tab',
			],
			'minimum_sizing_params'             => [
				'type'        => 'title',
				'title'       => __( 'Minimum shipping dimensions', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Bring needs a default shipping size for when products do not contain any dimension information.',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'bring-section-started',
			],
			'minimum_length'                    => [
				'title'             => __( 'Minimum Length in cm', 'bring-fraktguiden-for-woocommerce' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'placeholder'       => __( 'Must be at least 23cm', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'          => __( 'The lowest length for a consignment', 'bring-fraktguiden-for-woocommerce' ),
				'default'           => '23',
				'custom_attributes' => [
					'min' => '1',
				],
			],
			'minimum_width'                     => [
				'title'             => __( 'Minimum Width in cm', 'bring-fraktguiden-for-woocommerce' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'placeholder'       => __( 'Must be at least 13cm', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'          => __( 'The lowest width for a consignment', 'bring-fraktguiden-for-woocommerce' ),
				'default'           => '13',
				'custom_attributes' => [
					'min' => '1',
				],
			],
			'minimum_height'                    => [
				'title'             => __( 'Minimum Height in cm', 'bring-fraktguiden-for-woocommerce' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'placeholder'       => __( 'Must be at least 1cm', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'          => __( 'The lowest height for a consignment', 'bring-fraktguiden-for-woocommerce' ),
				'default'           => '1',
				'custom_attributes' => [
					'min' => '1',
				],
			],
			'minimum_weight'                    => [
				'title'             => __( 'Minimum Weight in kg', 'bring-fraktguiden-for-woocommerce' ),
				'type'              => 'number',
				'css'               => 'width: 8em;',
				'desc_tip'          => __( 'The lowest weight in kilograms for a consignment',
					'bring-fraktguiden-for-woocommerce' ),
				'default'           => '0.01',
				'custom_attributes' => [
					'step' => '0.01',
					'min'  => '0.01',
				],
			],

			/**
			 * Lost / no connection section
			 */
			'no_connection_title'               => [
				'type'        => 'title',
				'title'       => __( 'Bring API offline / No connection', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'If Bring has any technical difficulties, it won\'t be able to fetch prices from the bring server.<br>In these cases, shipping will default to these settings:',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'bring-separate-admin-section',
			],
			'no_connection_handling'            => [
				'title'    => __( 'No API connection handling', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'What pricing should be used if no connection can be made to the bring API',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'no_rate',
				'options'  => [
					'no_rate'   => __( 'Do nothing', 'bring-fraktguiden-for-woocommerce' ),
					'flat_rate' => __( 'Custom flat rate', 'bring-fraktguiden-for-woocommerce' ),
				],
			],
			'no_connection_flat_rate_label'     => [
				'title'   => __( 'Shipping method Label to replace \'API Error\'',
					'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'text',
				'default' => __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ),
			],
			'no_connection_flat_rate'           => [
				'title'       => __( 'Shipping method cost for \'API Error\'', 'bring-fraktguiden-for-woocommerce' ),
				'css'         => 'width: 8em;',
				'type'        => 'number',
				'placeholder' => __( 'ie: 500', 'bring-fraktguiden-for-woocommerce' ),
				'default'     => '0',
			],
			'no_connection_rate_id'             => [
				'title'   => __( 'Service to use for booking', 'bring-fraktguiden-for-woocommerce' ),
				'css'     => '',
				'type'    => 'select',
				'default' => '0',
				'options' => Fraktguiden_Helper::get_all_services(),
			],

			/**
			 * Heavy items section
			 */
			'exceptions_title'                  => [
				'type'        => 'title',
				'title'       => __( 'Heavy and oversized items', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Set a flat rate for packages that exceed the maximum measurements allowed by Bring.',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'bring-separate-admin-section',
			],
			'exception_handling'                => [
				'title'    => __( 'Heavy item handling', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'What method should be used to calculate post rates for items that exceeds the limits set by bring',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'no_rate',
				'options'  => [
					'no_rate'   => __( 'Do nothing', 'bring-fraktguiden-for-woocommerce' ),
					'flat_rate' => __( 'Custom flat rate', 'bring-fraktguiden-for-woocommerce' ),
				],
			],
			'exception_flat_rate_label'         => [
				'title'       => __( 'Shipping method Label for Heavy Items', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'placeholder' => __( 'ie: Cargo shipping', 'bring-fraktguiden-for-woocommerce' ),
				'default'     => __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ),
			],
			'exception_flat_rate'               => [
				'title'       => __( 'Shipping method cost for heavy items', 'bring-fraktguiden-for-woocommerce' ),
				'css'         => 'width: 8em;',
				'type'        => 'number',
				'placeholder' => __( 'ie: 500', 'bring-fraktguiden-for-woocommerce' ),
				'default'     => '0',
			],
			'exception_rate_id'                 => [
				'title'   => __( 'Service to use for booking', 'bring-fraktguiden-for-woocommerce' ),
				'css'     => '',
				'type'    => 'select',
				'default' => '0',
				'options' => Fraktguiden_Helper::get_all_services(),
			],

			/**
			 * Max products section
			 */
			'max_products_title'                => [
				'type'        => 'title',
				'title'       => __( 'Product quantity limit for cart', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'When a cart reaches this limit, you can enable this shipping method.<br><em>For example, when ordering in bulk, the price for a shipping container may be a flat rate</em>',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'bring-separate-admin-section',
			],
			'alt_handling'                      => [
				'title'    => __( 'Maximum product handling', 'bring-fraktguiden-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'We use a packing algorithm to pack items in three dimensions. This algorithm is computationally heavy and to prevent against DDoS attacks we\'ve implemented setting to control the maximum number of items that can be packed per order.',
					'bring-fraktguiden-for-woocommerce' ),
				'default'  => 'no_rate',
				'options'  => [
					'no_rate'   => __( 'Do nothing', 'bring-fraktguiden-for-woocommerce' ),
					'flat_rate' => __( 'Custom flat rate', 'bring-fraktguiden-for-woocommerce' ),
				],
			],
			'max_products'                      => [
				'title'       => __( 'Maximum product limit', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'css'         => 'width: 8em;',
				'placeholder' => 1000,
				'desc_tip'    => __( 'Maximum total quantity of products in the cart before offering a custom price',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => 1000,
			],
			'alt_flat_rate_label'               => [
				'title'       => __( 'Shipping method label', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'placeholder' => __( 'ie: Cargo shipping', 'bring-fraktguiden-for-woocommerce' ),
				'default'     => __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ),
			],
			'alt_flat_rate'                     => [
				'title'       => __( 'Shipping method cost', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'css'         => 'width: 8em;',
				'placeholder' => __( 'ie: 1500', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'    => __( 'Offer a flat rate if the cart reaches max products or a product in the cart does not have the required dimensions',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => 200,
			],
			'alt_flat_rate_id'                  => [
				'title'   => __( 'Service to use for booking', 'bring-fraktguiden-for-woocommerce' ),
				'css'     => '',
				'type'    => 'select',
				'default' => '0',
				'options' => Fraktguiden_Helper::get_all_services(),
			],

			/**
			 * Advanced settings
			 */
			'advanced_settings'                 => [
				'type'        => 'title',
				'title'       => __( 'Advanced', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Advanced configuration and debugging.', 'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'separated_title_tab',
			],
			'debug'                             => [
				'title'       => __( 'Debug mode', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable debug logs', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'    => __( 'Issues from the Bring API will be logged here',
					'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Bring Fraktguiden logs will be saved in',
						'bring-fraktguiden-for-woocommerce' ) . ' <code>' . $wc_log_dir . '</code><p><a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' . __( 'Click here to see the logs',
						'bring-fraktguiden-for-woocommerce' ) . '</a></p>',
				'default'     => 'no',
			],
			'disable_stylesheet'                => [
				'type'        => 'checkbox',
				'title'       => __( 'Disable stylesheet', 'bring-fraktguiden-for-woocommerce' ),
				'label'       => __( 'Remove all plugin styles from the checkout page',
					'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Disable loading the default stylesheet from the Bring Fraktguiden plugin to allow custom styling by the theme',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => 'no',
			],
			'lead_time'                         => [
				'title'       => __( 'Lead time in days', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Number of days before orders are shipped', 'bring-fraktguiden-for-woocommerce' ),
				'default'     => 0,
			],
			'lead_time_cutoff'                  => [
				'title'       => __( 'Lead time cutoff', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'time',
				'description' => __( 'Cutoff time every day. Orders after this time will be processed the next day.',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => '12:00',
			],
			'display_eta'                       => [
				'title'       => __( 'Display ETA', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable expected delivery date', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Display expected delivery date below shipping rates',
					'bring-fraktguiden-for-woocommerce' ),
				'default'     => '12:00',
			],
			'pickup_point_section'              => [
				'type'  => 'title',
				'title' => __( 'Pickup points', 'bring-fraktguiden-for-woocommerce' ),
				'class' => 'bring-section-started',
			],
			'pickup_point_types'                => [
				'title'   => __( 'Pickup point types', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'select',
				//'description' => __( '', 'bring-fraktguiden-for-woocommerce' ),
				'default' => '',
				'options' => [
					''       => __( 'All', 'bring-fraktguiden-for-woocommerce' ),
					'manned' => __( 'Manned', 'bring-fraktguiden-for-woocommerce' ),
					'locker' => __( 'Locker', 'bring-fraktguiden-for-woocommerce' ),
				]
			],
			'pickup_point_style'                => [
				'title'   => __( 'Style', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'select',
				'default' => '',
				'options' => [
					''       => __( 'Regular', 'bring-fraktguiden-for-woocommerce' ),
					'legacy' => __( 'Legacy', 'bring-fraktguiden-for-woocommerce' ),
				]
			],
			'pickup_point_map'                  => [
				'title'   => __( 'Map link', 'bring-fraktguiden-for-woocommerce' ),
				'type'    => 'select',
				'default' => 'postenMapsLink',
				'options' => [
					'postenMapsLink' => __( 'Posten', 'bring-fraktguiden-for-woocommerce' ),
					''               => __( 'Ingen', 'bring-fraktguiden-for-woocommerce' ),
					'googleMapsLink' => __( 'Google', 'bring-fraktguiden-for-woocommerce' ),
				]
			],
			'system_information'                => [
				'title'       => __( 'Debug System information', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'hidden',
				'label'       => __( 'Enable debug logs', 'bring-fraktguiden-for-woocommerce' ),
				'desc_tip'    => __( 'We may ask for this information if you require support',
					'bring-fraktguiden-for-woocommerce' ),
				'description' => sprintf( '<a href="%s" target="_blank">%s</a>',
					admin_url( 'admin-ajax.php?action=bring_system_info' ),
					__( 'View system info', 'bring-fraktguiden-for-woocommerce' ) ),
			],

			/**
			 * Mybring API settings
			 */
			'mybring_title'                     => [
				'title'       => __( 'Mybring.com API', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Enter your API credentials. API authentication is required.',
					'bring-fraktguiden-for-woocommerce' ),
				'class'       => 'separated_title_tab',
				'type'        => 'title',
			],
			'mybring_api_uid'                   => [
				'title'       => __( 'Email', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'label'       => __( 'Email', 'bring-fraktguiden-for-woocommerce' ),
				'placeholder' => 'bring@example.com',
				/* translators: %s: Mybring profile page URL */
				'description' => sprintf( __( 'Find your Email %1$shere%2$s.', 'bring-fraktguiden-for-woocommerce' ),
					'<a href="https://www.mybring.com/useradmin/account/profile" target="_blank">', '</a>' ),
			],
			'mybring_api_key'                   => [
				'title'       => __( 'API key', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'label'       => __( 'API key', 'bring-fraktguiden-for-woocommerce' ),
				'placeholder' => '4abcdef1-4a60-4444-b9c7-9876543219bf',
				/* translators: %s: Mybring API settings page URL */
				'description' => sprintf( __( 'Find your API key %1$shere%2$s.', 'bring-fraktguiden-for-woocommerce' ),
					'<a href="https://www.mybring.com/useradmin/account/settings/api" target="_blank">', '</a>' ),
			],
			'mybring_customer_number'           => [
				'title'       => __( 'API customer number', 'bring-fraktguiden-for-woocommerce' ),
				'type'        => 'text',
				'label'       => __( 'API customer number', 'bring-fraktguiden-for-woocommerce' ),
				'placeholder' => 'PARCELS_NORWAY-100########',
				/* translators: %s: Mybring API settings page URL */
				'description' => sprintf( __( 'Find your API customer number %1$shere%2$s.',
					'bring-fraktguiden-for-woocommerce' ),
					'<a href="https://www.mybring.com/useradmin/account/settings/api" target="_blank">', '</a>' ),
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
	 *
	 * @return void
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
		<h3 class="bring-title-admin-section"><?php echo esc_html( $this->method_title ); ?></h3>
		<p><?php esc_html_e( 'Bring Fraktguiden is a shipping method using Bring.com to calculate rates.',
				'bring-fraktguiden-for-woocommerce' ); ?></p>

		<div class="hash-tabs fraktguiden-options" style="display:none;">
			<article class="tab-container">
				<nav class="tab-nav" role="tablist">
					<ul></ul>
					<div style="clear:both;"></div>
				</nav>
				<div class="tab-pane-container"></div>
			</article>
		</div>

		<table class="form-table">
			<?php if ( $this->is_valid_for_use() ) : ?>
				<?php $this->generate_settings_html( $this->form_fields ); ?>
			<?php else : ?>
				<tr>
					<td>
						<div class="inline error"><p>
								<strong><?php esc_html_e( 'Gateway Disabled',
										'bring-fraktguiden-for-woocommerce' ); ?></strong>
								<br/> <?php printf( __( 'Bring shipping method requires <strong>weight &amp; dimensions</strong> to be enabled. Please enable them on the <a href="%1$s">Catalog tab</a>. <br/> In addition, Bring also requires the <strong>Norweigian Krone</strong> currency. Choose that from the <a href="%2$s">General tab</a>',
									'bring-fraktguiden-for-woocommerce' ),
									'admin.php?page=woocommerce_settings&tab=catalog',
									'admin.php?page=woocommerce_settings&tab=general' ); ?>
							</p></div>
					</td>
				</tr>
			<?php endif; ?>
		</table>

		<script>
			jQuery(function ($) {
				// Move settings into tabs.
				$('.separated_title_tab').each(function () {
					var id = $(this).attr('id');
					var text = $(this).text();

					// Create a new tab/list item.
					var elem = $('<li>').append($('<a>').attr({
						'href': '#' + id
					}).text(text));

					// Append it to the tab-navigation.
					$('.hash-tabs .tab-nav ul').append(elem);

					// Create a new tab-panel.
					elem = $('<section>').attr({
						'id': id
					}).hide();


					// Find the content for this panel.
					// It's always the next p's and <table>.
					var content = $(this).nextUntil('.separated_title_tab');
					elem.append($(this), content);

					// Place the panel in the panels container.
					$('.hash-tabs .tab-pane-container').append(elem);
				});

				var targeted = location.hash;

				// Make the tabs work.
				$('.fraktguiden-options').hashTabs();

				if (!targeted) {
					window.scrollTo(0, 0);
					setTimeout(function () {
						window.scrollTo(0, 0);
					}, 1);
				}

				$('.fraktguiden-options').show();

				var save = $('p.submit');
				$('.fraktguiden-options').after(save);
			});

			jQuery(function ($) {
				function toggle_test_mode() {
					var is_checked = $('#woocommerce_bring_fraktguiden_pro_enabled').prop('checked');

					console.log(bring_fraktguiden_settings.pro_activated);
					bring_fraktguiden_settings.pro_activated.value = is_checked;
					$('#shipping_services').attr('class', is_checked ? 'pro-enabled' : 'pro-disabled');
					$('#woocommerce_bring_fraktguiden_test_mode').closest('tr').toggle(is_checked);

					// Toggle the menu items for pickup points and bring booking.
					$('#5').toggle(is_checked);
				}

				$('#woocommerce_bring_fraktguiden_pro_enabled').change(toggle_test_mode);
				toggle_test_mode();
			});
			jQuery(function ($) {
				var api_fields = [
					'#woocommerce_bring_fraktguiden_mybring_api_uid',
					'#woocommerce_bring_fraktguiden_mybring_api_key',
					'#woocommerce_bring_fraktguiden_mybring_customer_number',
				];

				for (var i = 0; i < api_fields.length; i++) {
					var elem = $(api_fields[i]);
					if (!elem.val()) {
						// Hide everything except Mybring API credentials.
						$('#0, #1, #2, #3, #5').hide();
						$('#4').click();
						return;
					}
				}

			});
		</script>
		<?php
	}

	/**
	 * Process Mybring API credentials
	 *
	 * @return void
	 */
	public function process_mybring_api_credentials() {
		$api_uid_key         = $this->get_field_key( 'mybring_api_uid' );
		$api_key_key         = $this->get_field_key( 'mybring_api_key' );
		$customer_number_key = $this->get_field_key( 'mybring_customer_number' );

		$api_uid         = filter_input( INPUT_POST, $api_uid_key );
		$api_key         = filter_input( INPUT_POST, $api_key_key );
		$customer_number = filter_input( INPUT_POST, $customer_number_key );

		$mybring_authentication = [
			'message'       => '',
			'authenticated' => false,
		];

		$is_credential_missing = false;

		if ( ! $api_uid || ! $api_key ) {
			Fraktguiden_Admin_Notices::add_missing_api_credentials_notice();
			$is_credential_missing = true;
		} else {
			Fraktguiden_Admin_Notices::remove_missing_api_credentials_notice();
		}

		if ( ! $customer_number && Fraktguiden_Helper::pro_activated() && Fraktguiden_Helper::booking_enabled() ) {
			Fraktguiden_Admin_Notices::add_missing_api_customer_number_notice();
			$is_credential_missing = true;
		} else {
			Fraktguiden_Admin_Notices::remove_missing_api_customer_number_notice();
		}

		if ( $is_credential_missing ) {
			$mybring_authentication['message'] = __( 'Missing credentials', 'bring-fraktguiden-for-woocommerce' );
			update_option( 'mybring_authentication', $mybring_authentication );
			update_option( 'mybring_authenticated_key', '', true );

			return;
		}

		$key  = get_option( 'mybring_authenticated_key' );
		$hash = md5( $api_uid . $api_key . $customer_number );

		if ( $key === $hash ) {
			// We already tried this combination, skip this for re-saves.
			return;
		}

		// Try to authenticate.
		$request  = new \WP_Bring_Request();
		$params   = $this->make_shipping_guide_request_body(
			null,
			[ Fraktguiden_Service::find( self::$field_key, 'SERVICEPAKKE' ) ],
			[ [ 'weight_in_grams' => 1000 ] ]
		);
		$options  = [
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			],
			'body'    => json_encode( $params ),
		];
		$response = $request->post( self::SERVICE_URL, [], $options );

		if ( 200 !== $response->status_code ) {
			$this->mybring_error( $response->body );
			$mybring_authentication['message'] = 'Mybring error: ' . $response->body;
			update_option( 'mybring_authentication', $mybring_authentication );
			update_option( 'mybring_authenticated_key', '', true );

			return;
		}

		$result = json_decode( $response->body, true );

		/*
		Check for customer_number authentication error
		May the programming gods have mercy. Bring does not have a authentication endpoint
		and authentication credentials has to be passed on every request. The shipping API is
		simply the easiest api to test against, but only certain products actually require
		auth. I've picked "Servicepakke" because it seems to be the most reliable (hasn't
		changed the last year). Now I wouldn't normally rant like this, I mean it would be
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
					update_option( 'mybring_authentication', $mybring_authentication );
					update_option( 'mybring_authenticated_key', '', true );

					return;
				}
			}
		}

		$mybring_authentication['message']       = __( 'Successfully authenticated',
			'bring-fraktguiden-for-woocommerce' );
		$mybring_authentication['authenticated'] = true;

		// Success. All authentication methods have passed.
		update_option( 'mybring_authentication', $mybring_authentication );
		update_option( 'mybring_authenticated_key', $hash, true );
	}

	/**
	 * Add Mybring error
	 *
	 * @param string $message Error message.
	 *
	 * @return void
	 */
	public function mybring_error( $message ) {
		if ( strpos( $message, 'Authentication failed.' ) === 0 ) {
			$message = sprintf( '<strong>%s:</strong> %s.',
				__( 'Mybring authentication failed', 'bring-fraktguiden-for-woocommerce' ),
				__( "Couldn't connect to Bring with your API credentials. Please check that they are correct",
					'bring-fraktguiden-for-woocommerce' ) );
		}

		Fraktguiden_Admin_Notices::add_notice( 'mybring_error', $message, 'error', false );
	}

	/**
	 * Validate services table field
	 *
	 * @param string      $key   Key.
	 * @param string|null $value Value.
	 *
	 * @return array
	 */
	public function validate_services_table_field( $key, $value = null ) {
		return $this->service_table->validate_services_table_field( $key, $value );
	}

	/**
	 * Generate services table HTML
	 *
	 * @return string
	 */
	public function generate_services_table_html() {
		return $this->service_table->generate_services_table_html();
	}
}
