<?php

namespace BringFraktguidenPro\PickUpPoint;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;

class LegacyPickupPoints {

	public static function setup()
	{
		add_filter('woocommerce_shipping_chosen_method', [LegacyPickupPoints::class, 'chosen_method'], 10, 3);
	}
	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init()
	{
		// Pickup points.
		add_filter('bring_shipping_rates', __CLASS__ . '::insert_pickup_points', 10, 2);


		// Enable enhanced descriptions if the option is ticked.
		if ( 'yes' === Fraktguiden_Helper::get_option( 'display_desc' ) ) {
			LegacyPickUpPointEnhancement::setup();
		}
	}

	public static function chosen_method($default, $rates, $chosen_method)
	{
		$id = WC()->session->get( 'bring_fraktguiden_pick_up_point' );
		$keys = array_keys($rates);

		if (preg_match('/^bring_fraktguiden:\d+$/', $chosen_method)) {
			// Going from legacy to new
			$key = $chosen_method . '-' . $id;
			if (in_array($key, $keys)) {
				return $key;
			}
		}

		if (preg_match('/^bring_fraktguiden:\d+-\d+$/', $chosen_method)) {
			// Going from new to legacy (eg. klarna checkout)
			$key = preg_replace('/^(bring_fraktguiden:\d+)-\d+$/', '$1', $chosen_method);
			if (in_array($key, $keys)) {
				return $key;
			}
		}

		return $default;
	}

	/**
	 * Filter: Insert pickup points
	 *
	 * @param array $rates Rates.
	 * @hook bring_shipping_rates
	 *
	 * @return array
	 */
	public static function insert_pickup_points($rates, $shipping_rate)
	{

		$field_key = $shipping_rate->get_field_key('services');
		$services = Fraktguiden_Service::all($field_key);

		$rate_key = false;
		$service_package = false;
		$bring_product = false;

		foreach ($rates as $key => $rate) {
			// Service package identified.
			$service_package = $rate;
			$bring_product = strtoupper($rate['bring_product']);

			if (empty($services[$bring_product])) {
				continue;
			}

			$service = $services[$bring_product];

			if (empty($service->settings['pickup_point_cb'])) {
				continue;
			}
			// Remove this package.
			$rate_key = $key;
			break;
		}


		if (false === $rate_key) {
			// Service package is not available.
			// That means it's the end of the line for pickup points.
			return $rates;
		}

		$pickup_point_limit = apply_filters('bring_pickup_point_limit', (int)$service->settings['pickup_point']);
		$pickup_points = (new GetRawPickupPointsAction)(null, null);

		if (empty($pickup_points)) {
			return $rates;
		}

		// Remove service package.
		unset($rates[$rate_key]);

		$pickup_point_count = 1;
		$new_rates = [];

		foreach ($pickup_points as $pickup_point) {
			$rate = [
				'id' => "bring_fraktguiden:{$bring_product}-{$pickup_point['id']}",
				'bring_product' => $bring_product,
				'expected_delivery_date' => $service_package['expected_delivery_date'],
				'cost' => $service_package['cost'],
				'label' => $pickup_point['name'],
				'meta_data' => [
					'pickup_point_id' => $pickup_point['id'],
					'pickup_point_data' => $pickup_point,
				],
			];

			$new_rates[] = $rate;
			if ($pickup_point_limit && $pickup_point_limit <= $pickup_point_count) {
				break;
			}
			$pickup_point_count++;
		}

		foreach ($rates as $key => $rate) {
			$new_rates[] = $rate;
		}

		return $new_rates;
	}
}
