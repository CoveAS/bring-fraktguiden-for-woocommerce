<?php

namespace BringFraktguiden\Customs;

use BringFraktguiden\Order\ShippedLine;
use WC_Order;
use WC_Order_Item_Product;

/**
 * Whether an order holds the customs data that a booking needs.
 *
 * The check reads the same item lines that the declaration builds, so the two
 * never disagree. See CustomsDeclaration::items().
 *
 * A caller asks CustomsRoute first, then hands the answer to the check. The
 * route decides whether an order needs customs data at all, and an export asks
 * for more than a transit does.
 */
class CustomsCheck
{
	/**
	 * Return the problems of an order, keyed by item id.
	 *
	 * An order with no problems returns an empty array.
	 *
	 * @param WC_Order $order The order the booking ships.
	 * @param string   $route A CustomsRoute constant.
	 *
	 * @return array<int, array<int, CustomsProblem>>
	 */
	public static function problems(WC_Order $order, string $route): array
	{
		$problems = [];

		foreach (CustomsDeclaration::items($order) as $id => $item) {
			$found = self::for_order_item($item, $order, $route);

			if ($found) {
				$problems[$id] = $found;
			}
		}

		return $problems;
	}

	/**
	 * Return whether an order can carry a customs declaration.
	 */
	public static function passes(WC_Order $order, string $route): bool
	{
		return [] === self::problems($order, $route);
	}

	/**
	 * Return the problems of one item line.
	 *
	 * @param string $route A CustomsRoute constant.
	 *
	 * @return array<int, CustomsProblem>
	 */
	public static function for_order_item(WC_Order_Item_Product $item, WC_Order $order, string $route): array
	{
		$problems = [];

		if ('' === HsCode::for_order_item($item)) {
			$problems[] = CustomsProblem::MissingHsCode;
		}

		if (NetWeight::gross_for_order_item($item) <= 0) {
			$problems[] = CustomsProblem::MissingWeight;
		}

		if (ShippedLine::for_order_item($item, $order)->value <= 0) {
			$problems[] = CustomsProblem::NoValue;
		}

		// Only an export declares where the goods come from. Bring accepts a
		// transit declaration without the country.
		if (CustomsRoute::EXPORT === $route && '' === CountryOfOrigin::for_order_item($item)) {
			$problems[] = CustomsProblem::MissingCountryOfOrigin;
		}

		return $problems;
	}
}
