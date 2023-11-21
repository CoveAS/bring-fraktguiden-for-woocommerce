<?php

namespace Bring_Fraktguiden\Common;

use DateTime;
use Exception;
use WC_Shipping_Rate;

/**
 * Checkout Modifications
 */
class CarrierLogo {
	/**
	 * Add opening hours to a full label
	 *
	 * @param WC_Shipping_Rate $rate Shipping rate.
	 *
	 * @throws Exception
	 */
	public static function add_carrier_logo( WC_Shipping_Rate $rate ): void {
		$meta_data = $rate->get_meta_data();
		$alt = $meta_data['bring_logo_alt'] ?? null;
		$logo = $meta_data['bring_logo_url'] ?? null;
		if ( !$alt || !$logo ) {
			return;
		}

		printf(
			'<img class="bring-fraktguiden-logo" alt="%s" src="%s">',
			$alt,
			$logo
		);
	}
}
