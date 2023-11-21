<?php

namespace Bring_Fraktguiden\Common;

use DateTime;
use Exception;
use WC_Shipping_Rate;

/**
 * Checkout Modifications
 */
class EnvironmentalDescription {
	/**
	 * Add opening hours to a full label
	 *
	 * @param WC_Shipping_Rate $rate Shipping rate.
	 *
	 * @throws Exception
	 */
	public static function add_environmental_description( WC_Shipping_Rate $rate ): void {
		$meta_data = $rate->get_meta_data();
		$description = $meta_data['bring_environmental_description'] ?? null;
		$logo = $meta_data['bring_environmental_logo_url'] ?? null;
		if ( !$description || !$logo ) {
			return;
		}

		printf(
			'<div class="bring-fraktguiden-environmental">
			<img class="environmental-logo" src="%s">
			<span>%s</span>
			</div>',

			$logo,
			$description
		);
	}
}
