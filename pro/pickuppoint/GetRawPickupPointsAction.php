<?php

namespace BringFraktguidenPro\PickUpPoint;

use WP_Bring_Request;

class GetRawPickupPointsAction {
	/**
	 * @param string|null $pickup_point_type 'manned', 'locker' or an empty
	 *                                       string for both. Null asks
	 *                                       PickupPointType what the enabled
	 *                                       services need.
	 */
	public function __invoke(?string $country, ?string $postcode, ?string $pickup_point_type = null): array {

		if (! $postcode || ! $country) {
			return [];
		}

		$request = new WP_Bring_Request();
		$customer = WC()->customer;
		$args = [];
		if ($customer) {
			$args['street'] = $customer->get_shipping_address();
		}
		$pickup_point_type ??= PickupPointType::fetch_type();
		if (in_array($pickup_point_type, ['manned', 'locker'], true)) {
			$args['pickupPointType'] = $pickup_point_type;
		}
		$response = $request->get(
			'https://api.bring.com/pickuppoint/api/pickuppoint/' . $country . '/postalCode/' . $postcode . '.json',
			apply_filters(
				'bring_fraktguiden_get_pickup_points_args',
				$args
			)
		);
		// On error return empty array
		if (is_wp_error($response) || 200 !== $response->status_code) {
			return [];
		}
		// Decode data
		$data = json_decode($response->get_body(), true);
		if (empty($data['pickupPoint'])) {
			return [];
		}

		return $data['pickupPoint'];
	}
}
