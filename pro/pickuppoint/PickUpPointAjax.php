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
class PickUpPointAjax
{
	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init(): void
	{
		// Ajax.
		add_action('wp_ajax_bring_get_pickup_points', __CLASS__ . '::bring_get_pickup_points');
		add_action('wp_ajax_nopriv_bring_get_pickup_points', __CLASS__ . '::bring_get_pickup_points');

		add_action('wp_ajax_bring_shipping_info_var', [ __CLASS__, 'bring_shipping_info_var' ] );
		add_action('wp_ajax_bring_get_rate', [ __CLASS__, 'bring_get_rate' ] );

		// set pickup point
		add_action('wp_ajax_bfg_select_pick_up_point', __CLASS__ . '::bfg_select_pick_up_point');
		add_action('wp_ajax_nopriv_bfg_select_pick_up_point', __CLASS__ . '::bfg_select_pick_up_point');

		// get pickup points
		add_action('wp_ajax_bfg_get_pick_up_points', __CLASS__ . '::bfg_get_pick_up_points');
		add_action('wp_ajax_nopriv_bfg_get_pick_up_points', __CLASS__ . '::bfg_get_pick_up_points');
	}

	/**
	 * Prints shipping info json
	 *
	 * Only available from admin
	 */
	public static function bring_shipping_info_var()
	{
		$result = [];
		$screen = get_current_screen();

		if (($screen && 'shop_order' === $screen->id) || is_ajax()) {
			// Comment to future self: wow, this code is utter trash 🤦‍
			global $post;

			$post_id = $post ? $post->ID : filter_input(INPUT_GET, 'post_id');
			$order = new Bring_WC_Order_Adapter(new WC_Order($post_id));
			$result = $order->get_shipping_data();
		}

		wp_send_json( [ 'bring_shipping_info' => $result ] );
	}

	/**
	 * Prints rate json for a bring service.
	 *
	 * Only available from admin.
	 */

	public static function bring_get_rate()
	{
		$result = [
			'success' => false,
			'rate' => null,
			'packages' => null,
		];

		$service = filter_input(INPUT_GET, 'service');

		// Return false if neither integer nor string variable is representing a positive integer.
		$post_id = filter_var(
			filter_input(INPUT_GET, 'post_id'),
			FILTER_VALIDATE_INT,
			[ 'options' => [ 'min_range' => 1 ] ]
		);

		if (is_null($service) || false === $post_id) {
			wp_send_json($result);
		}

		$order = wc_get_order($post_id);

		$country = filter_input(INPUT_GET, 'country');

		if (is_null($country)) {
			$country = $order->get_shipping_country();
		}

		$postcode = filter_input(INPUT_GET, 'postcode');

		if (is_null($postcode)) {
			$postcode = $order->get_shipping_postcode();
		}

		$items = $order->get_items();

		$fake_cart = [];

		foreach ($items as $item) {
			$fake_cart[uniqid()] = [
				'quantity' => $item['qty'],
				'data' => new WC_Product_Simple($item['product_id']),
			];
		}

		$packer = new Fraktguiden_Packer();

		$product_boxes = $packer->create_boxes($fake_cart);

		$packer->pack($product_boxes, true);

		$package_params = $packer->create_packages_params();

		// @todo: share / filter
		$standard_params = [
			'clientUrl' => Fraktguiden_Helper::get_client_url(),
			'frompostalcode' => Fraktguiden_Helper::get_option('from_zip'),
			'fromcountry' => Fraktguiden_Helper::get_option('from_country'),
			'topostalcode' => $postcode,
			'tocountry' => $country,
			'postingatpostoffice' => (Fraktguiden_Helper::get_option('post_office') === 'no') ? 'false' : 'true',
		];

		$shipping_method = new WC_Shipping_Method_Bring();

		$field_key = $shipping_method->get_field_key('services');
		$evarsling = Fraktguiden_Service::vas_for($field_key, $service, ['2084', 'EVARSLING']);

		$standard_params['additionalservice'] = ($evarsling ? 'EVARSLING' : '');

		$params = array_merge($standard_params, $package_params);

		$url = add_query_arg($params, WC_Shipping_Method_Bring::SERVICE_URL);
		$url .= '&product=' . strtoupper($service);

		// Make the request.
		$request = new WP_Bring_Request();
		$response = $request->get($url);

		if (200 !== $response->status_code) {
			wp_send_json($params);
		}

		$json = json_decode($response->get_body(), true);
		$rates = $shipping_method->get_services_from_response($json);

		if (empty($rates)) {
			wp_send_json($params);
		}

		$rate = reset($rates);
		$result['success'] = true;
		$result['rate'] = $rate['cost'];
		$result['packages'] = wp_json_encode($package_params);

		wp_send_json($result);
	}

	/**
	 * Get pickup points via AJAX
	 */
	public static function bring_get_pickup_points()
	{
		$pick_up_points = (new GetRawPickupPointsAction)(
			filter_input(INPUT_GET, 'country'),
			filter_input(INPUT_GET, 'postcode')
		);

		if (empty($pick_up_points)) {
			wp_die();
		}

		wp_send_json($pick_up_points);
	}

	/**
	 * Select pickup point via AJAX
	 */
	public static function bfg_select_pick_up_point(): void {
		$id = filter_input(INPUT_POST, 'id');
		WC()->session->set('bring_fraktguiden_pick_up_point', $id);
		wp_send_json(['message' => 'success']);
	}

	/**
	 * Get pickup points via AJAX
	 */
	public static function bfg_get_pick_up_points(): void {
		$result = (new GetRawPickupPointsAction)(null, null);

		if (empty($result)) {
			wp_die();
		}

		$pick_up_points = PickUpPointData::rawCollection($result);
		wp_send_json(
			[
				'pick_up_points' => $pick_up_points,
				'selected_pick_up_point' => (new GetSelectedPickUpPointAction())($pick_up_points),
			]
		);
	}
}
