<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * The HS codes of the products of several orders.
 *
 * The bulk booking modal on the orders list shows one table for the whole
 * selection, so a shop worker fills in the missing codes before the send.
 *
 * A code is a trait of the product, so the same product in two orders makes one
 * row. See OrderHsCodes.
 */
class BulkHsCodes
{
	/**
	 * Return one row per product of the orders that need customs data.
	 *
	 * An order whose route asks customs for nothing adds no row, the same rule
	 * the booking box follows.
	 *
	 * @param int[] $order_ids
	 *
	 * @return array<int, array{name: string, image: string, code: string}>
	 */
	public static function rows(array $order_ids): array
	{
		$rows = [];

		foreach (BulkOrders::with_customs($order_ids) as $order) {
			$rows += OrderHsCodes::rows($order);
		}

		return $rows;
	}

	/**
	 * Write the codes a shop worker typed.
	 *
	 * The caller passes what the browser sent, so every value is untrusted.
	 * OrderHsCodes::save() skips a product that is not part of the order.
	 *
	 * @param int[]                    $order_ids
	 * @param array<int|string, mixed> $codes The code of each product, keyed by product id.
	 */
	public static function save(array $order_ids, array $codes): void
	{
		foreach (BulkOrders::with_customs($order_ids) as $order) {
			OrderHsCodes::save($order, $codes);
		}
	}
}
