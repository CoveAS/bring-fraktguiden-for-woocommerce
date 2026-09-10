<?php

namespace BringFraktguiden\Customs;

use BringFraktguiden\Order\ShippedLine;
use WC_Order_Item_Product;
use WC_Product;

/**
 * The net weight that Bring customs data needs, in kilograms.
 *
 * Customs asks for two weights per item line. The gross weight is the goods
 * with their packing. The net weight is the goods alone.
 *
 * WooCommerce holds one weight per product, and a shop enters what the parcel
 * scale shows. That weight is the gross weight. A shop that knows the weight of
 * the goods alone sets the net weight in a field of its own.
 *
 * The net weight falls back to the WooCommerce weight, so the two weights are
 * equal until a shop fills the field.
 */
class NetWeight
{
	/**
	 * The net weight of one unit, on a product and on a variation.
	 *
	 * The value is in the weight unit of the shop, like the WooCommerce weight.
	 */
	public const META = '_bring_net_weight';

	/**
	 * Return the gross weight of an order line in kilograms.
	 */
	public static function gross_for_order_item(WC_Order_Item_Product $item): float
	{
		return self::line_total($item, self::unit_gross($item->get_product()));
	}

	/**
	 * Return the net weight of an order line in kilograms.
	 */
	public static function net_for_order_item(WC_Order_Item_Product $item): float
	{
		$product = $item->get_product();
		$gross   = self::unit_gross($product);
		$net     = $product ? self::for_product($product) : 0.0;

		if (!$net || $net > $gross) {
			$net = $gross;
		}

		return self::line_total($item, $net);
	}

	/**
	 * Return the stored net weight of one unit, in kilograms, or 0.
	 *
	 * A variation uses its own weight only when the shop turns the override on.
	 */
	public static function for_product(WC_Product $product): float
	{
		if ($product->is_type('variation')) {
			$own = self::own_weight($product);

			if ($own) {
				return $own;
			}

			$parent = wc_get_product($product->get_parent_id());

			return $parent ? self::for_product($parent) : 0.0;
		}

		return self::in_kg($product->get_meta(self::META));
	}

	/**
	 * Convert a weight in the unit of the shop to kilograms.
	 *
	 * @param mixed $weight A weight, or an empty value.
	 */
	public static function in_kg($weight): float
	{
		$weight = trim((string) $weight);

		if ('' === $weight) {
			return 0.0;
		}

		return (float) wc_get_weight(wc_format_decimal($weight), 'kg');
	}

	/**
	 * Return the WooCommerce weight of one unit, in kilograms, or 0.
	 */
	private static function unit_gross(?WC_Product $product): float
	{
		return $product ? self::in_kg($product->get_weight()) : 0.0;
	}

	/**
	 * Multiply a unit weight by the number of units the shop ships.
	 *
	 * A refund lowers that number, so the weight follows the value of the line.
	 */
	private static function line_total(WC_Order_Item_Product $item, float $unit): float
	{
		$order  = $item->get_order();
		$pieces = $order
			? ShippedLine::for_order_item($item, $order)->pieces
			: (int) $item->get_quantity();

		return round($unit * max(1, $pieces), 3);
	}

	/**
	 * Return the net weight of a variation when its override is on.
	 */
	private static function own_weight(WC_Product $variation): float
	{
		if (!Override::is_on($variation)) {
			return 0.0;
		}

		return self::in_kg($variation->get_meta(self::META));
	}
}
