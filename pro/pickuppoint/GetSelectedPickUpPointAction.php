<?php

namespace BringFraktguidenPro\PickUpPoint;

class GetSelectedPickUpPointAction {
	/**
	 * @param PickUpPointData[] $pick_up_points
	 * @param string            $type 'manned', 'locker' or an empty string for
	 *                                both.
	 */
	public function __invoke(array $pick_up_points, string $type = '') {
		if ($type) {
			$pick_up_points = array_filter(
				$pick_up_points,
				fn ($pick_up_point) => $pick_up_point->pickupPointType === $type
			);
		}
		$selected_pick_up_point_id = WC()->session->get(PickupPointType::session_key($type), null);
		$filtered = $selected_pick_up_point_id ? array_filter(
			$pick_up_points,
			fn ($pick_up_point) => $pick_up_point->id === $selected_pick_up_point_id
		) : [];
		return empty($filtered) ? reset($pick_up_points) : reset($filtered);
	}
}
