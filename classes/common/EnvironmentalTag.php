<?php

namespace Bring_Fraktguiden\Common;

use DateTime;
use Exception;
use WC_Shipping_Rate;

/**
 * Checkout Modifications
 */
class EnvironmentalTag {
	/**
	 * Add opening hours to a full label
	 *
	 * @param WC_Shipping_Rate $rate Shipping rate.
	 *
	 * @throws Exception
	 */
	public static function add_environmental_tag( WC_Shipping_Rate $rate ): void {
		$meta_data = $rate->get_meta_data();
		$alt = $meta_data['bring_environmental_description'] ?? null;
		$logo = $meta_data['bring_environmental_tag_url'] ?? null;
		if ( !$alt || !$logo ) {
			return;
		}

		printf(
			'<img class="bring-fraktguiden-environmental-tag" alt="%s" src="%s">',
			$alt,
			$logo
		);
	}
}
