<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * The `customsInformation` object of a Bring booking.
 *
 * The object covers the whole order, because a WooCommerce order does not say
 * which goods travel on which shipping line. See the failed idea in CLAUDE.md.
 *
 * Only NVIT is built. An export needs a consent flag, a cargo type and the
 * exporter and importer parties, and no settings screen holds those yet. See
 * doc/export.md.
 */
class CustomsInformation
{
	/**
	 * Return the object of a booking, or null when the booking needs none.
	 *
	 * @param WC_Order $order   The order the booking ships.
	 * @param string   $product The Bring product, for example 5800.
	 *
	 * @return array<string, mixed>|null
	 */
	public static function for_order(WC_Order $order, string $product): ?array
	{
		if (CustomsRoute::NVIT !== CustomsRoute::for_order($order, $product)) {
			return null;
		}

		$declarations = CustomsDeclaration::for_order($order);

		if (!$declarations) {
			return null;
		}

		return [
			'type'                => 'NVIT',
			'customsDeclarations' => $declarations,
		];
	}
}
