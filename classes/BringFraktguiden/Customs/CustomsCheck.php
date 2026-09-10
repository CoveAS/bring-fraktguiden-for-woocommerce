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
 * The check says nothing about the route. NvitRule and the export rule decide
 * whether an order needs customs data at all. A caller asks a rule first, then
 * asks the check.
 */
class CustomsCheck
{
	/**
	 * Return the problems of an order, keyed by item id.
	 *
	 * An order with no problems returns an empty array.
	 *
	 * @return array<int, array<int, CustomsProblem>>
	 */
	public static function problems(WC_Order $order): array
	{
		$problems = [];

		foreach (CustomsDeclaration::items($order) as $id => $item) {
			$found = self::for_order_item($item, $order);

			if ($found) {
				$problems[$id] = $found;
			}
		}

		return $problems;
	}

	/**
	 * Return whether an order can carry a customs declaration.
	 */
	public static function passes(WC_Order $order): bool
	{
		return [] === self::problems($order);
	}

	/**
	 * Return the problems of one item line.
	 *
	 * @return array<int, CustomsProblem>
	 */
	public static function for_order_item(WC_Order_Item_Product $item, WC_Order $order): array
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

		return $problems;
	}
}
