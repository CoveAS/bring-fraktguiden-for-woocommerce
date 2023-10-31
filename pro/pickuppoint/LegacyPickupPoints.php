<?php

namespace BringFraktguidenPro\PickupPoint;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;

class LegacyPickupPoints {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init()
	{
		// Pickup points.
		// if ( 'yes' === Fraktguiden_Helper::get_option( 'pickup_point_enabled' ) ) {
		add_filter('bring_shipping_rates', __CLASS__ . '::insert_pickup_points', 10, 2);
		// add_filter( 'bring_pickup_point_limit', __CLASS__ . '::limit_pickup_points' );
		// }
	}

	/**
	 * Limit pickup points
	 *
	 * @param int $default_limit Default limit.
	 * @return int
	 */
	public static function limit_pickup_points($default_limit)
	{
		return Fraktguiden_Helper::get_option('pickup_point_limit') ?: $default_limit;
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
		$postcode = esc_html(
			apply_filters('bring_pickup_point_postcode', WC()->customer->get_shipping_postcode())
		);
		$country = esc_html(
			apply_filters('bring_pickup_point_country', WC()->customer->get_shipping_country())
		);
		$response = self::get_pickup_points($country, $postcode);

		if (200 !== $response->status_code) {
			sleep(1);
			$response = self::get_pickup_points($country, $postcode);
		}

		if (200 !== $response->status_code) {
			return $rates;
		}

		// Remove service package.
		unset($rates[$rate_key]);

		$pickup_point_count = 1;
		$pickup_points = json_decode($response->get_body(), 1);
		$new_rates = [];

		foreach ($pickup_points['pickupPoint'] as $pickup_point) {
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
