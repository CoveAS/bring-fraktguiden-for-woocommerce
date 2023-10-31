<?php

namespace BringFraktguidenPro\PickUpPoint;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WP_Bring_Request;

class GetRawPickupPointsAction {
	public function __invoke(?string $country, ?string $postcode): array {

		$country = esc_html( apply_filters('bring_pickup_point_country', $country ?? WC()->customer->get_shipping_country() ) );
		$postcode = esc_html( apply_filters('bring_pickup_point_postcode', $postcode ?? WC()->customer->get_shipping_postcode()) );

		if (! $postcode || ! $country) {
			return [];
		}

		$request = new WP_Bring_Request();
		$customer = WC()->customer;
		$args = [];
		if ($customer) {
			$args['street'] = $customer->get_shipping_address();
		}
		if ('manned' === Fraktguiden_Helper::get_option('pickup_point_types')) {
			$args['pickupPointType'] = 'manned';
		}
		if ('locker' === Fraktguiden_Helper::get_option('pickup_point_types')) {
			$args['pickupPointType'] = 'locker';
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
