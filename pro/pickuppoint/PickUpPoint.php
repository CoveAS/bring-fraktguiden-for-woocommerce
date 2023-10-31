<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\PickUpPoint;

use Bring_Fraktguiden;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use Fraktguiden_Packer;
use WC_Order;
use WC_Product_Simple;
use WC_Shipping_Method_Bring;
use WC_Shipping_Rate;
use WP_Bring_Request;
use WP_Bring_Response;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Fraktguiden_Pickup_Point class
 *
 * Process the checkout
 */
class PickUpPoint
{
	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init()
	{
		// Enqueue checkout Javascript.
		add_action('wp_enqueue_scripts', [ __CLASS__, 'checkout_load_javascript' ] );

		// Display order received and mail.
		add_filter('woocommerce_order_shipping_to_display_shipped_via', [ __CLASS__, 'checkout_order_shipping_to_display_shipped_via' ], 1, 2);

		// Hide shipping metadata from order items (WooCommerce 2.6)
		// See https://github.com/woothemes/woocommerce/issues/9094 for reference.
		add_filter('woocommerce_hidden_order_itemmeta', [ __CLASS__, 'woocommerce_hidden_order_itemmeta' ], 1, 1);
		add_filter('woocommerce_order_item_display_meta_key', [ __CLASS__, 'woocommerce_order_item_display_meta_key' ] );

		// Add pick up point selector after shipping option
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::pick_up_point_picker', 10, 2 );
	}

	/**
	 * Add additional item meta
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public static function woocommerce_hidden_order_itemmeta($fields)
	{
		$fields[] = '_fraktguiden_pickup_point_postcode';
		$fields[] = '_fraktguiden_pickup_point_id';
		$fields[] = '_fraktguiden_pickup_point_info_cached';
		$fields[] = 'pickup_point_id';
		$fields[] = 'bring_product';
		$fields[] = 'expected_delivery_date';

		return $fields;
	}

	/**
	 * Add additional item meta
	 *
	 * @param string $display_key Display key.
	 * @return string
	 */
	public static function woocommerce_order_item_display_meta_key($display_key)
	{

		if ('bring_fraktguiden_time_slot' === $display_key) {
			return __('Selected time slot', 'bring-fraktguiden-for-woocommerce');
		}
		return $display_key;
	}

	/**
	 * Load checkout javascript
	 */
	public static function checkout_load_javascript()
	{

		if (!is_checkout()) {
			return;
		}

		wp_register_script('fraktguiden-common', plugins_url('assets/js/pickup-point-common.js', dirname(__FILE__)), [ 'jquery' ], Bring_Fraktguiden::VERSION, true);
		wp_register_script('fraktguiden-pickup-point-checkout', plugins_url('assets/js/pickup-point-checkout.js', dirname(__FILE__)), [ 'jquery' ], Bring_Fraktguiden::VERSION, true);
		wp_localize_script(
			'fraktguiden-pickup-point-checkout',
			'_fraktguiden_data',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'i18n' => self::get_i18n(),
				'country' => Fraktguiden_Helper::get_option('from_country'),
				'klarna_checkout_nonce' => wp_create_nonce('klarna_checkout_nonce'),
				'nonce' => wp_create_nonce('bring_fraktguiden'),
			]
		);

		wp_enqueue_script('fraktguiden-common');
		wp_enqueue_script('fraktguiden-pickup-point-checkout');
	}


	/**
	 * HTML for checkout recipient page / emails etc.
	 *
	 * @param string $content Content.
	 * @param WC_Order $wc_order Order.
	 * @return string
	 */
	public static function checkout_order_shipping_to_display_shipped_via($content, $wc_order)
	{
		$shipping_methods = $wc_order->get_shipping_methods();

		foreach ($shipping_methods as $shipping_method) {
			if (
				Fraktguiden_Helper::ID . ':servicepakke' === $shipping_method['method_id'] &&
				isset($shipping_method['fraktguiden_pickup_point_info_cached']) &&
				$shipping_method['fraktguiden_pickup_point_info_cached']
			) {
				$info = $shipping_method['fraktguiden_pickup_point_info_cached'];
				$content = $content . '<div class="bring-order-details-pickup-point"><div class="bring-order-details-selected-text">' . self::get_i18n()['PICKUP_POINT'] . ':</div><div class="bring-order-details-info-text">' . str_replace('|', '<br>', $info) . '</div></div>';
			}
		}

		return $content;
	}

	/**
	 * Text translation strings for ui JavaScript.
	 *
	 * @return array
	 */
	public static function get_i18n()
	{
		return [
			'PICKUP_POINT' => __('Pickup point', 'bring-fraktguiden-for-woocommerce'),
			'LOADING_TEXT' => __('Please wait...', 'bring-fraktguiden-for-woocommerce'),
			'VALIDATE_SHIPPING1' => __('Fraktguiden requires the following fields', 'bring-fraktguiden-for-woocommerce'),
			'VALIDATE_SHIPPING_POSTCODE' => __('Valid shipping postcode', 'bring-fraktguiden-for-woocommerce'),
			'VALIDATE_SHIPPING_COUNTRY' => __('Valid shipping postcode', 'bring-fraktguiden-for-woocommerce'),
			'VALIDATE_SHIPPING2' => __('Please update the fields and save the order first', 'bring-fraktguiden-for-woocommerce'),
			'SERVICE_PLACEHOLDER' => __('Please select service', 'bring-fraktguiden-for-woocommerce'),
			'POSTCODE' => __('Postcode', 'bring-fraktguiden-for-woocommerce'),
			'PICKUP_POINT_PLACEHOLDER' => __('Please select pickup point', 'bring-fraktguiden-for-woocommerce'),
			'SELECTED_TEXT' => __('Selected pickup point', 'bring-fraktguiden-for-woocommerce'),
			'PICKUP_POINT_NOT_FOUND' => __('No pickup points found for postcode', 'bring-fraktguiden-for-woocommerce'),
			'GET_RATE' => __('Get Rate', 'bring-fraktguiden-for-woocommerce'),
			'PLEASE_WAIT' => __('Please wait', 'bring-fraktguiden-for-woocommerce'),
			'SERVICE' => __('Service', 'bring-fraktguiden-for-woocommerce'),
			'RATE_NOT_AVAILABLE' => __('Rate is not available for this order. Please try another service', 'bring-fraktguiden-for-woocommerce'),
			'REQUEST_FAILED' => __('Request was not successful', 'bring-fraktguiden-for-woocommerce'),
			'ADD_POSTCODE' => __('Please add postal code', 'bring-fraktguiden-for-woocommerce'),
		];
	}

	public static function pick_up_point_picker(WC_Shipping_Rate $rate, $index)
	{
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return;
		}

		// Only show if supported method has been chosen
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		if ( ! in_array( $rate->get_id(), $chosen_methods ) ) {
			return;
		}

		$metadata = $rate->get_meta_data();
		if ( empty($metadata['bring_product']) ) {
			return;
		}

		$services = Fraktguiden_Service::all();
		$bring_product = strtoupper($metadata['bring_product']);

		if (empty($services[$bring_product])) {
			return;
		}

		$service = $services[$bring_product];

		if (empty($service->settings['pickup_point_cb'])) {
			return;
		}

		$number = (int)($service->settings['pickup_point'] ?? 0);

		echo (new PickUpPointPicker($number))->render();
	}
}
