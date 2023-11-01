<?php

namespace BringFraktguidenPro\PickUpPoint;

class GetSelectedPickUpPointAction {
	public function __invoke(array $pick_up_points) {
		$selected_pick_up_point_id = WC()->session->get('bring_fraktguiden_pick_up_point', null);
		$filtered = $selected_pick_up_point_id ? array_filter(
			$pick_up_points,
			fn ($pick_up_point) => $pick_up_point->id === $selected_pick_up_point_id
		) : [];
		return empty($filtered) ? reset($pick_up_points) : reset($filtered);
	}
}
