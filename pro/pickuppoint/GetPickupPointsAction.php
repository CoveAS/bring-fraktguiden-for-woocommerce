<?php

namespace BringFraktguidenPro\PickUpPoint;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WP_Bring_Request;
use WP_Bring_Response;

class GetPickupPointsAction {
	public function __invoke(string $country, string $postcode): WP_Bring_Response {
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
		return $request->get(
			'https://api.bring.com/pickuppoint/api/pickuppoint/' . $country . '/postalCode/' . $postcode . '.json',
			apply_filters(
				'bring_fraktguiden_get_pickup_points_args',
				$args
			)
		);
	}
}
