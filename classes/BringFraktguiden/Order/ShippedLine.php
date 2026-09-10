<?php

namespace BringFraktguiden\Order;

use WC_Order;
use WC_Order_Item_Product;

/**
 * What an order line ships, and what it is worth, after any refund.
 *
 * WooCommerce never changes an item line when a shop refunds part of it. The
 * order holds the refunded pieces and the refunded money apart from the line,
 * so a reader that wants the shipped part must subtract them.
 *
 * A shop that refunds a whole line ships nothing on it. The pieces are then 0.
 */
final class ShippedLine
{
	private function __construct(
		public readonly int $pieces,
		public readonly float $value,
	) {
	}

	/**
	 * Return the shipped part of an item line.
	 */
	public static function for_order_item(WC_Order_Item_Product $item, WC_Order $order): self
	{
		return new self(
			self::pieces($item, $order),
			self::value($item, $order),
		);
	}

	/**
	 * Return the number of units the shop ships.
	 */
	private static function pieces(WC_Order_Item_Product $item, WC_Order $order): int
	{
		$refunded = (int) $order->get_qty_refunded_for_item($item->get_id());

		return max(0, (int) $item->get_quantity() - abs($refunded));
	}

	/**
	 * Return the value of the shipped units, with VAT.
	 *
	 * WooCommerce holds the line value without VAT, and the tax of the line
	 * next to it, so the two add up. Both refund methods return a positive
	 * amount.
	 */
	private static function value(WC_Order_Item_Product $item, WC_Order $order): float
	{
		$value = (float) $item->get_total() + (float) $item->get_total_tax();
		$value -= (float) $order->get_total_refunded_for_item($item->get_id());
		$value -= self::refunded_tax($item, $order);

		return round(max(0.0, $value), 2);
	}

	/**
	 * Return the refunded tax of an item line.
	 *
	 * WooCommerce reports a refunded tax per tax rate, so the rates of the line
	 * add up.
	 */
	private static function refunded_tax(WC_Order_Item_Product $item, WC_Order $order): float
	{
		$taxes = $item->get_taxes();
		$total = 0.0;

		foreach (array_keys($taxes['total'] ?? []) as $rate_id) {
			$total += (float) $order->get_tax_refunded_for_item($item->get_id(), (int) $rate_id);
		}

		return $total;
	}
}
