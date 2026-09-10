<?php

namespace BringFraktguiden\Customs;

use WC_Order_Item_Product;
use WC_Product;

/**
 * The short description of the goods that Bring customs data needs.
 *
 * A shop may set the text per product, and per variation when the variations
 * hold different goods. The order line name is the fallback, so the text is
 * always optional.
 *
 * The Booking API sets no length limit on this field.
 */
class GoodsDescription
{
	/**
	 * The text, on a product and on a variation.
	 */
	public const META = '_bring_goods_description';

	/**
	 * Return the goods description of an order line.
	 */
	public static function for_order_item(WC_Order_Item_Product $item): string
	{
		$product = $item->get_product();
		$text    = $product ? self::for_product($product) : '';

		return $text ?: $item->get_name();
	}

	/**
	 * Return the stored goods description of a product, or an empty string.
	 *
	 * A variation uses its own text only when the shop turns the override on.
	 */
	public static function for_product(WC_Product $product): string
	{
		if ($product->is_type('variation')) {
			$own = self::own_text($product);

			if ($own) {
				return $own;
			}

			$parent = wc_get_product($product->get_parent_id());

			return $parent ? trim((string) $parent->get_meta(self::META)) : '';
		}

		return trim((string) $product->get_meta(self::META));
	}

	/**
	 * Return the text of a variation when its override is on.
	 */
	private static function own_text(WC_Product $variation): string
	{
		if ('yes' !== $variation->get_meta(CustomsFields::OVERRIDE_META)) {
			return '';
		}

		return trim((string) $variation->get_meta(self::META));
	}
}
