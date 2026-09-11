<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * The `customsInformation` object of a Bring booking.
 *
 * The object covers the whole order, because a WooCommerce order does not say
 * which goods travel on which shipping line. See the failed idea in CLAUDE.md.
 *
 * An NVIT booking carries the top level type. An export leaves it out, and
 * carries the exporter and the importer parties instead. CustomsParties builds
 * those. See doc/nvit.md and doc/export.md.
 */
class CustomsInformation
{
	/**
	 * Return the object of a booking, or null when the booking needs none.
	 *
	 * @param WC_Order           $order   The order the booking ships.
	 * @param string             $product The Bring product, for example 5800.
	 * @param NatureOfCargo|null $cargo   Why the goods move. A null reads the
	 *                                    posted booking form, which is what the
	 *                                    bulk booking does.
	 *
	 * @return array<string, mixed>|null
	 */
	public static function for_order(WC_Order $order, string $product, ?NatureOfCargo $cargo = null): ?array
	{
		$route = CustomsRoute::for_order($order, $product);

		if (!$route) {
			return null;
		}

		$declarations = CustomsDeclaration::for_order($order);

		if (!$declarations) {
			return null;
		}

		$information = [
			'customsDeclarations' => $declarations,
			'natureOfCargo'       => ['type' => ($cargo ?? NatureOfCargo::from_request())->value],
		];

		// The type marks the data as transit data. An export needs a normal
		// customs declaration, and the NVIT type does not work for it.
		if (CustomsRoute::NVIT === $route) {
			$information = ['type' => 'NVIT'] + $information;
		}

		// A shop that has not confirmed signs nothing. An absent field is not a
		// refusal, so leave it out rather than send false.
		if (CustomsConsent::given()) {
			$information['consent'] = true;
		}

		return $information;
	}
}
