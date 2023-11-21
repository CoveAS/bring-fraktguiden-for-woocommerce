<?php

namespace Bring_Fraktguiden\Common;

use DateTime;
use Exception;
use WC_Shipping_Rate;

/**
 * Checkout Modifications
 */
class RateDescription {
	/**
	 * Add opening hours to a full label
	 *
	 * @param WC_Shipping_Rate $rate Shipping rate.
	 *
	 * @throws Exception
	 */
	public static function add_description( WC_Shipping_Rate $rate ): void {
		$meta_data = $rate->get_meta_data();
		$description = $meta_data['bring_description'] ?? null;

		printf(
			'<div class="bring-fraktguiden-description"> %s </div>',
			$description
		);
	}
}
