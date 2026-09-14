<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * The orders of a bulk booking selection that need customs data.
 *
 * The bulk booking modal shows one HS code table and one customs warning for
 * the whole selection. Both read the same orders, and both need the Bring
 * service of each order.
 */
class BulkOrders
{
	/**
	 * Return the orders of the selection that need customs data.
	 *
	 * An order whose route asks customs for nothing is left out, the same rule
	 * the booking box follows.
	 *
	 * @param int[] $order_ids
	 *
	 * @return WC_Order[]
	 */
	public static function with_customs(array $order_ids): array
	{
		$orders = [];

		foreach ($order_ids as $order_id) {
			$order = wc_get_order((int) $order_id);

			if (!$order instanceof WC_Order) {
				continue;
			}

			if (!CustomsRoute::for_order($order, self::service($order))) {
				continue;
			}

			$orders[] = $order;
		}

		return $orders;
	}

	/**
	 * Return the Bring service the order ships with.
	 *
	 * The shipping line carries the service in the `bring_product` meta, the
	 * same key the booking box reads.
	 */
	public static function service(WC_Order $order): string
	{
		foreach ($order->get_shipping_methods() as $item) {
			$service = (string) $item->get_meta('bring_product');

			if ($service) {
				return $service;
			}
		}

		return '';
	}
}
