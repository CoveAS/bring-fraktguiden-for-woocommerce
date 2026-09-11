<?php

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

$wc_log_dir = '';
if (defined('WC_LOG_DIR')) {
	$wc_log_dir = WC_LOG_DIR;
}
$nordic_countries = Fraktguiden_Helper::get_nordic_countries();
// The store base, read from the options. WC()->countries is not ready this early.
$base_country_code = explode(':', get_option('woocommerce_default_country', ''))[0];
$base_country = $nordic_countries[$base_country_code] ?? __('Choose a country', 'bring-fraktguiden-for-woocommerce');
$base_postcode = get_option('woocommerce_store_postcode', '');

$all_countries = WC()->countries?->get_countries() ?: [];
// The order statuses a booking can set. 'none' leaves the order status alone.
$order_status_options = array_merge(
	['none' => __('None', 'bring-fraktguiden-for-woocommerce')],
	wc_get_order_statuses()
);

$all_services = Fraktguiden_Helper::get_all_services();
$first_service = reset($all_services);

// Filtered services for fallback options: top 3 new services + pakke i postkassen
$fallback_services = [
	'5800' => $all_services['5800'] ?? 'Pakke til hentested',
	'5600' => $all_services['5600'] ?? 'Pakke levert hjem',
	'5000' => $all_services['5000'] ?? 'Pakke til bedrift',
	'3570' => $all_services['3570'] ?? 'Pakke i postkassen (sporbar)',
	'3584' => $all_services['3584'] ?? 'Pakke i postkassen',
];

return [
	'home' => [
		'fields' => [
			'pro_enabled' => [
				'title' => __('Activate PRO', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => __('Activate PRO', 'bring-fraktguiden-for-woocommerce'),
				'class' => 'bring-toggle-checkbox',
				'description' => __('A license is required to use PRO features on a live website. Activating PRO first gives you a free 7-day trial. After the trial, PRO features will be disabled until a license is activated.', 'bring-fraktguiden-for-woocommerce'),
			],
			'test_url' => [
				'type' => 'text',
				'label' => __('License key', 'bring-fraktguiden-for-woocommerce'),
				'placeholder' => 'XXXX-XXXX-XXXX-XXXX',
				'default' => '',
			],
		],
	],

	/**
	 * Pro page - license management and feature overview
	 * Note: Uses pro_enabled and test_url fields from 'home' section
	 */
	'pro' => [
		'title' => __('Bring Fraktguiden Pro', 'bring-fraktguiden-for-woocommerce'),
		'fields' => [],
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
				'label' => __('Posting at post office', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Enable if you drop off packages at a post office. Leave unchecked if Bring picks up from your location.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'from_zip' => [
				'title' => __('From zip', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'placeholder' => '0010',
				'css' => 'width: 100px; text-align: right;',
				'default' => $base_postcode,
			],
			'from_country' => [
				'title' => __('From country', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'class' => 'chosen_select',
				'css' => 'width: 200px;',
				'default' => $base_country_code,
				'placeholder' => $base_country,
				'options' => $nordic_countries,
			],
			'handling_fee' => [
				'title' => __('Handling Fee', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'placeholder' => __('0', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Add an additional fee on top of the calculated shipping rates. All bring shipping options will have their prices increased by this amount.',
					'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 100px; text-align: right;',
				'default' => '',
				'custom_attributes' => [
					'min' => '0',
					'class' => 'bfg-suffixed-number-lg',
				],
			],
			'shipping_options_full_width' => [
				'label' => __('Display shipping options full-width',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
				'type' => 'checkbox',
				'description' => __('Moves the "Shipping" heading to its own row so shipping options can use the full width. Makes them easier to read at checkout.',
					'bring-fraktguiden-for-woocommerce'),
			],
			'display_desc' => [
				'type' => 'checkbox',
				'label' => __('Display detailed shipping option description', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Displays service logos and additional info for each shipping option.',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
			],
			'use_customer_number_to_get_prices' => [
				'title' => __('Use customer number', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'checkbox',
				'label' => __('Use main mybring customer number to get prices from the api.',
					'bring-fraktguiden-for-woocommerce'),
				'desc_tip' => __('Uses your Mybring customer number to get discounted business rates instead of standard prices.',
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
	 * Booking page
	 */
	'booking' => [
		'fields' => [
			'booking_enabled' => [
				'type' => 'checkbox',
				'label' => __('Enable MyBring booking', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Allow booking shipments directly from WooCommerce order pages', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'booking_without_bring' => [
				'type' => 'checkbox',
				'label' => __('Allow booking without Bring shipping', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Enable booking for orders that don\'t use Bring shipping methods', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'booking_test_mode_enabled' => [
				'type' => 'checkbox',
				'label' => __('Enable test mode for MyBring booking', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('When enabled, bookings will not be invoiced or fulfilled by Bring', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
			],
			'booking_use_custom_address' => [
				'type' => 'checkbox',
				'label' => __('Use a different shipping address', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('Enable this if you ship from a different address than your WooCommerce store address.', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'booking_address_store_name' => [
				'title' => __('Store Name', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'description' => __('Your business name as it appears on shipping labels.', 'bring-fraktguiden-for-woocommerce'),
				'placeholder' => get_bloginfo('name'),
				'custom_attributes' => ['maxlength' => 35],
			],
			'booking_address_street1' => [
				'title' => __('Street Address 1', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'custom_attributes' => [
					'maxlength' => 35,
					'autocomplete' => 'address-line1',
				],
			],
			'booking_address_street2' => [
				'title' => __('Street Address 2', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'custom_attributes' => [
					'maxlength' => 35,
					'autocomplete' => 'address-line2',
				],
			],
			'booking_address_postcode' => [
				'title' => __('Postcode', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'custom_attributes' => ['autocomplete' => 'postal-code'],
			],
			'booking_address_city' => [
				'title' => __('City', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'custom_attributes' => ['autocomplete' => 'address-level2'],
			],
			'booking_address_country' => [
				'title' => __('Country', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'options' => $all_countries,
				'default' => $base_country_code,
			],
			'booking_address_reference' => [
				'title' => __('Reference', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'description' => __('The store\'s reference printed on the shipping label. Usually {order_id}, but can also be {products}.', 'bring-fraktguiden-for-woocommerce'),
				'placeholder' => __('e.g. {order_id}', 'bring-fraktguiden-for-woocommerce'),
				'custom_attributes' => [
					'maxlength' => 35,
					'aria-required' => 'true',
					'required' => 'required',
				],
			],
			'booking_address_contact_person' => [
				'title' => __('Contact Person', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'custom_attributes' => [
					'autocomplete' => 'name',
					'aria-required' => 'true',
					'required' => 'required',
				],
			],
			'booking_address_phone' => [
				'title' => __('Phone', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'tel',
				'custom_attributes' => [
					'autocomplete' => 'tel',
					'aria-required' => 'true',
					'required' => 'required',
				],
			],
			'booking_address_email' => [
				'title' => __('Email', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'email',
				'custom_attributes' => [
					'autocomplete' => 'email',
					'aria-required' => 'true',
					'required' => 'required',
				],
			],
			'customs_consent' => [
				'type' => 'checkbox',
				'label' => __('I confirm the customs data of my shipments', 'bring-fraktguiden-for-woocommerce'),
				'description' => __('The goods description, the value and the HS code of every order line are correct and complete, and the goods are neither dangerous nor prohibited. Bring prints this confirmation as your signature on the customs declaration.', 'bring-fraktguiden-for-woocommerce'),
				'default' => 'no',
			],
			'customs_exporter_number' => [
				'title' => __('Exporter number', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'text',
				'description' => __('Your VAT number or EORI number. Bring sends it with the customs declaration of a shipment that leaves Norway.', 'bring-fraktguiden-for-woocommerce'),
				'default' => '',
			],
			'auto_set_status_after_booking_success' => [
				'title' => __('Order status after booking', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'description' => __('Order status will be automatically set when successfully booked', 'bring-fraktguiden-for-woocommerce'),
				'options' => $order_status_options,
				'default' => 'wc-bring-shipment',
			],
			'auto_set_status_after_print_label_success' => [
				'title' => __('Order status after printing', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'description' => __('Order status will be automatically set when a label is downloaded', 'bring-fraktguiden-for-woocommerce'),
				'options' => $order_status_options,
				'default' => 'none',
			],
			'booking_home_delivery_package_type' => [
				'title' => __('Package type for home delivery', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'select',
				'description' => __('Only applies to home delivery services', 'bring-fraktguiden-for-woocommerce'),
				'options' => [
					'hd_eur' => 'HD_EUR_PALLET',
					'hd_half' => 'HD_HALF_PALLET',
					'hd_quarter' => 'HD_QUARTER_PALLET',
					'hd_loose' => 'HD_SPECIAL_PALLET',
				],
				'default' => 'hd_eur',
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
				'default' => 'no',
				'type' => 'checkbox',
				'description' => __('Split large orders into multiple shipments. This increases shipping costs proportionally. Consider using a fallback rate instead for bulk orders.', 'bring-fraktguiden-for-woocommerce'),
			],
			'dimension_packing_side' => [
				'title' => __('Side', 'bring-fraktguiden-for-woocommerce'),
				'css' => 'width: 8em;',
				'placeholder' => '240',
				'default' => 240,
				'type' => 'number',
				'dependencies' => ['enable_multipack' => true],
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
				'default' => 360,
				'type' => 'number',
				'dependencies' => ['enable_multipack' => true],
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
				'default' => 35,
				'type' => 'number',
				'dependencies' => ['enable_multipack' => true],
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
				'default' => '0',
				'options' => [
					__('No shipping'),
					...$all_services,
				],
			],

			'calculate_by_weight' => [
				'label' => __('Calculate shipping costs based on weight only',
					'bring-fraktguiden-for-woocommerce'),
				'default' => 'yes',
				'type' => 'checkbox',
				'description' => __('Uses weight only for shipping rates. Works best if you pack orders in standard-size boxes.', 'bring-fraktguiden-for-woocommerce'),
			],

			// Maximum product limit
			'max_products' => [
				'title' => __('Maximum product limit', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'number',
				'css' => 'width: 8em;',
				'placeholder' => 1000,
				'description' => __('Limit how many cart items are processed for shipping calculation. Higher values may slow down checkout. Default: 1000.', 'bring-fraktguiden-for-woocommerce'),
				'default' => 1000,
				'dependencies' => ['calculate_by_weight' => false],
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
				'default' => '0',
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
				'default' => '0',
				'options' => [
					__('No shipping'),
					...$all_services,
				],
			],
		],
	],
];
