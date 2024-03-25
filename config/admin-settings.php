<?php

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

$wc_log_dir = '';
if (defined('WC_LOG_DIR')) {
	$wc_log_dir = WC_LOG_DIR;
}
$nordic_countries = Fraktguiden_Helper::get_nordic_countries();
$base_country_code = WC()->countries?->get_base_country();
$base_country = $nordic_countries[$base_country_code] ?? __('Choose a country', 'bring-fraktguiden-for-woocommerce');
$base_postcode = WC()->countries?->get_base_postcode();

$all_services = Fraktguiden_Helper::get_all_services();
$first_service = reset($all_services);
return [
	'home' => [
		'fields' => [
			'pro_enabled' => [
				'title' => __('Activate PRO', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => '<em class="bring-toggle"></em>' . __('Enable/disable PRO features',
						'bring-fraktguiden-for-woocommerce'),
				'class' => 'bring-toggle-checkbox',
				'description' => __('Please note that using the PRO features on a live website requires a license. First time activation starts a free 7 day trial. After the free period the PRO features will be disabled pending a valid license.', 'bring-fraktguiden-for-woocommerce'),
			],
			'test_mode' => [
				'title' => __('Enable test mode', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => '<em class="bring-toggle"></em>' . __('Test and staging mode',
						'bring-fraktguiden-for-woocommerce'),
				'desc_tip' => __('Removes the license requirement and lets you use all of the pro features. A message will be displayed on the cart and checkout page that this is a test-site.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
				'class' => 'bring-toggle-checkbox',
			],
		],
	],
	/**
	 * General options setting
	 */
	'settings' => [
		'title' => __('Shipping Options', 'bring-fraktguiden-for-woocommerce'),
		'description' => __('Set the default prices for shipping rates and allow free shipping options on those services. You can also set the free shipping limit for each shipping service.',
			'bring-fraktguiden-for-woocommerce'),
		'fields' => [
			'language' => [
				'title' => __('Language', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'description' => __('Choose the language you want to use for the names and descriptions of shipping rates.', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'website',
				'options' => [
					'website' => __('Use website language', 'bring-fraktguiden-for-woocommerce'),
					'no' => __('Norwegian', 'bring-fraktguiden-for-woocommerce'),
					'en' => __('English', 'bring-fraktguiden-for-woocommerce'),
					'se' => __('Swedish', 'bring-fraktguiden-for-woocommerce'),
					'da' => __('Danish', 'bring-fraktguiden-for-woocommerce'),
					'fi' => __('Finnish', 'bring-fraktguiden-for-woocommerce'),
				]
			],
			'post_office' => [
				'title' => __('Post office', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => __('Shipping from post office', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Enable this option if you deliver packages to a post office.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'from_zip' => [
				'title' => __('From zip', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'placeholder' => $base_postcode,
				'css' => 'width: 100px; text-align: right;',
				'default' => '',
			],
			'from_country' => [
				'title' => __('From country', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'class' => 'chosen_select',
				'css' => 'width: 200px;',
				'default' => '',
				'placeholder' => $base_country,
				'options' => [
					'' => '',
					...Fraktguiden_Helper::get_nordic_countries(),
				],
			],
			'handling_fee' => [
				'title' => __('Handling Fee', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'placeholder' => __('0', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Use this setting if you want to add an additional fee on top of the calculated shipping rates. All services will have their prices increased by this amount.',
					'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 100px; text-align: right;',
				'default' => '',
				'custom_attributes' => [
					'min' => '0',
					'class' => 'bfg-suffixed-number-lg',
				],
			],
			'calculate_by_weight' => [
				'label' => __('Calculate shipping costs based on weight only',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
				'type' => 'checkbox',
				'description' => __('The shipping cost is normally calculated by a combination of weight and dimensions in order to calculate number of parcels to send and gives a more accurate price. Use this option to disable calculation based on dimensions.',
					'bring-fraktguiden-for-woocommerce'),
			],
			'shipping_options_full_width' => [
				'label' => __('Display shipping options full-width',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
				'type' => 'checkbox',
				'description' => __('By default WooCommerce displays all the shipping options in a table with two columns, one for the title, "Shipping", and one for the options. This means that the options gets squished into a very tight space. Enable this option to display the shipping options full-width.',
					'bring-fraktguiden-for-woocommerce'),
			],
			'display_desc' => [
				'type' => 'checkbox',
				'label' => __('Display detailed shipping option description', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Show more details for each shipping option on the checkout page. This includes logo and environmental description for the service.', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
			],
			'use_customer_number_to_get_prices' => [
				'title' => __('Use customer number', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => __('Use main mybring customer number to get prices from the api.',
					'bring-fraktguiden-for-woocommerce'),
				'desc_tip' => __('Using the customer number when querying the API will return your agreement price (net price), with bring. This is usually cheaper than the list price and you can choose which price to display during checkout. This setting will be overridden by specifying a customer number on individual services.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
			],
			'price_to_use' => [
				'title' => __('Price type', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'options' => [
					'net' => __('Net price'),
					'list' => __('List price'),
				],
				'desc_tip' => __('Net price is the agreement price with bring and will only be used if a customer number is used for the API request',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'net',
			],
			'service_sorting' => [
				'title' => __('Sorting', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'css' => 'width: 400px;',
				'default' => 'price',
				'desc_tip' => __('The order in which shipping options should be displayed.',
					'bring-fraktguiden-for-woocommerce'),
				'options' => [
					'price' => __('Price, low to high', 'bring-fraktguiden-for-woocommerce'),
					'price_desc' => __('Price, high to low', 'bring-fraktguiden-for-woocommerce'),
					'none' => __('No sorting', 'bring-fraktguiden-for-woocommerce'),
				],
			],
			'mybring_api_uid' => [
				'title' => __('Email', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'label' => __('Email', 'bring-fraktguiden-for-woocommerce'),
				'placeholder' => 'bring@example.com',
				/* translators: %s: Mybring profile page URL */
				'description' => sprintf(__('Find your Email %1$shere%2$s.', 'bring-fraktguiden-for-woocommerce'),
					'<a href="https://www.mybring.com/useradmin/account/profile" target="_blank">', '</a>'),
			],
			'mybring_api_key' => [
				'title' => __('API key', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'label' => __('API key', 'bring-fraktguiden-for-woocommerce'),
				'placeholder' => '4abcdef1-4a60-4444-b9c7-9876543219bf',
				/* translators: %s: Mybring API settings page URL */
				'description' => sprintf(__('Find your API key %1$shere%2$s.', 'bring-fraktguiden-for-woocommerce'),
					'<a href="https://www.mybring.com/useradmin/account/settings/api" target="_blank">', '</a>'),
			],
			'mybring_customer_number' => [
				'title' => __('API customer number', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'label' => __('API customer number', 'bring-fraktguiden-for-woocommerce'),
				'placeholder' => 'PARCELS_NORWAY-100########',
				/* translators: %s: Mybring API settings page URL */
				'description' => sprintf(__('Find your API customer number %1$shere%2$s.',
					'bring-fraktguiden-for-woocommerce'),
					'<a href="https://www.mybring.com/useradmin/account/settings/api" target="_blank">', '</a>'),
			],
			'debug' => [
				'title' => __('Debug mode', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => __('Enable debug logs', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Bring Fraktguiden logs will be saved in',
						'bring-fraktguiden-for-woocommerce') . ' <br><code>' . $wc_log_dir . '</code><br><a href="' . admin_url('admin.php?page=wc-status&tab=logs') . '">' . __('Click here to see the logs',
						'bring-fraktguiden-for-woocommerce') . '</a>',
				'default' => 'no',
			],
			'disable_stylesheet' => [
				'type' => 'checkbox',
				'title' => __('Disable stylesheet', 'bring-fraktguiden-for-woocommerce'),
				'label' => __('Remove all plugin styles from the checkout page',
					'bring-fraktguiden-for-woocommerce'),
				'description' => __('Disable loading the default stylesheet from the Bring Fraktguiden plugin to allow custom styling by the theme',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'lead_time' => [
				'title' => __('Lead time in days', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'description' => __('Number of days before orders are shipped', 'bring-fraktguiden-for-woocommerce'),
				'default' => 0,
				'css' => 'width: 90px',
			],
			'lead_time_cutoff' => [
				'title' => __('Lead time cutoff', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'time',
				'description' => __('Cutoff time every day. Orders after this time will be processed the next day.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => '12:00',
				'css' => 'width: 90px',
			],
			'display_eta' => [
				'title' => __('Display ETA', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => __('Enable expected delivery date', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Display expected delivery date below shipping rates',
					'bring-fraktguiden-for-woocommerce'),
				'default' => '12:00',
			],			'minimum_length' => [
				'title' => __('Length', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 75px;',
				'default' => '23.0',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '1',
					'class' => 'bfg-suffixed-number'
				],
			],
			'minimum_width' => [
				'title' => __('Width', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 75px;',
				'default' => '13.0',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '1',
					'class' => 'bfg-suffixed-number'
				],
			],
			'minimum_height' => [
				'title' => __('Height', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 75px;',
				'default' => '1.0',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '1',
					'class' => 'bfg-suffixed-number'
				],
			],
			'minimum_weight' => [
				'title' => __('Weight', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 75px;',
				'default' => '0.01',
				'custom_attributes' => [
					'step' => '0.01',
					'min' => '0.01',
					'class' => 'bfg-suffixed-number'
				],
			],
			'pickup_point_types' => [
				'title' => __('Pickup point types', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				//'description' => __( '', 'bring-fraktguiden-for-woocommerce' ),
				'default' => '',
				'options' => [
					'' => __('All', 'bring-fraktguiden-for-woocommerce'),
					'manned' => __('Manned', 'bring-fraktguiden-for-woocommerce'),
					'locker' => __('Locker', 'bring-fraktguiden-for-woocommerce'),
				]
			],
			'pickup_point_style' => [
				'title' => __('Style', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => __('Regular', 'bring-fraktguiden-for-woocommerce'),
					'legacy' => __('Legacy', 'bring-fraktguiden-for-woocommerce'),
				]
			],
			'pickup_point_map' => [
				'title' => __('Map link', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'default' => 'postenMapsLink',
				'options' => [
					'postenMapsLink' => __('Posten', 'bring-fraktguiden-for-woocommerce'),
					'' => __('Ingen', 'bring-fraktguiden-for-woocommerce'),
					'googleMapsLink' => __('Google', 'bring-fraktguiden-for-woocommerce'),
				]
			],
			'system_information' => [
				'title' => __('Debug System information', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'info',
				'label' => __('Enable debug logs', 'bring-fraktguiden-for-woocommerce'),
				'desc_tip' => __('We may ask for this information if you require support',
					'bring-fraktguiden-for-woocommerce'),
				'description' => sprintf('<a href="%s" target="_blank">%s</a>',
					admin_url('admin-ajax.php?action=bring_system_info'),
					__('View system info', 'bring-fraktguiden-for-woocommerce')),
			],
		],
	],

	/**
	 * Fallback options page
	 */
	'fallback' => [
		'fields' => [
			'enable_multipack' => [
				'label' => __('Pack in multiple boxes', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
				'type' => 'checkbox',
				'description' => __('
This will automatically divide cart items into boxes with sides less than 240 cm and weigh less than 35kg and a circumference less than 360cm.
If you have a specific size of box that you ship you can customize these values in the settings below.
				', 'bring-fraktguiden-for-woocommerce'),
			],
			'dimension_packing_side' => [
				'title' => __('Side', 'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 8em;',
				'placeholder' => '240',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '0',
					'class' => 'bfg-suffixed-number',
				],
			],
			'dimension_packing_circumference' => [
				'title' => __('Circumference', 'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 8em;',
				'placeholder' => '360',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '0',
					'class' => 'bfg-suffixed-number',
				],
			],
			'dimension_packing_weight' => [
				'title' => __('Weight', 'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 8em;',
				'placeholder' => '35',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '0',
					'class' => 'bfg-suffixed-number',
				],
			],

			// Offline
			'no_connection_flat_rate_label' => [
				'title' => __('Label',
					'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'default' => __('Shipping', 'bring-fraktguiden-for-woocommerce'),
			],
			'no_connection_flat_rate' => [
				'title' => __('Fixed price', 'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 8em;',
				'type' => 'number',
				'placeholder' => __('ie: 500', 'bring-fraktguiden-for-woocommerce'),
				'default' => '0',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '0',
					'class' => 'bfg-suffixed-number-lg',
				],
			],
			'no_connection_rate_id' => [
				'title' => __('Service to use', 'bring-fraktguiden-for-woocommerce'),
				'css' => '',
				'type' => 'select',
				'default' => '1',
				'options' => [
					__('No shipping'),
					...$all_services,
				],
			],

			// Maximum product limit
			'max_products' => [
				'title' => __('Maximum product limit', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 8em;',
				'placeholder' => 1000,
				'desc_tip' => __('Use this setting to set a threshold for how many cart items the plugin will attempt to process. A large quantity will impact the speed of the website during the checkout and we recommend keeping this number at 1000 or lower to ensure good performance.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 1000,
				'custom_attributes' => [
					'step' => '1',
					'min' => '0',
				],
			],
			'alt_flat_rate_label' => [
				'title' => __('Label', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'placeholder' => __('ie: Cargo shipping', 'bring-fraktguiden-for-woocommerce'),
				'default' => __('Shipping', 'bring-fraktguiden-for-woocommerce'),
			],
			'alt_flat_rate' => [
				'title' => __('Fixed price', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 8em;',
				'placeholder' => __('ie: 1500', 'bring-fraktguiden-for-woocommerce'),
				'default' => 0,
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '0',
					'class' => 'bfg-suffixed-number-lg',
				],
			],
			'alt_flat_rate_id' => [
				'title' => __('Service to use', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Show this service as a shipping rate if the maximum product limit is exceeded.', 'bring-fraktguiden-for-woocommerce'),
				'css' => '',
				'type' => 'select',
				'default' => 1,
				'options' => [
					__('No shipping'),
					...$all_services,
				],
			],

			// Heavy and oversized
			'exception_flat_rate_label' => [
				'title' => __('Label', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'placeholder' => __('ie: Cargo shipping', 'bring-fraktguiden-for-woocommerce'),
				'default' => __('Shipping', 'bring-fraktguiden-for-woocommerce'),
			],
			'exception_flat_rate' => [
				'title' => __('Fixed price', 'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 8em;',
				'type' => 'number',
				'placeholder' => __('ie: 500', 'bring-fraktguiden-for-woocommerce'),
				'default' => '0',
				'custom_attributes' => [
					'step' => '0.1',
					'min' => '0',
					'class' => 'bfg-suffixed-number-lg',
				],
			],
			'exception_rate_id' => [
				'title' => __('Service to use', 'bring-fraktguiden-for-woocommerce'),
				'css' => '',
				'type' => 'select',
				'default' => 1,
				'options' => [
					__('No shipping'),
					...$all_services,
				],
			],
		],
	],
];
