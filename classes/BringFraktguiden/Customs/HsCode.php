<?php

namespace BringFraktguiden\Customs;

use WC_Order_Item_Product;
use WC_Product;

/**
 * Read the HS code of a product.
 *
 * The code is the term name, for example 330510, never the term slug.
 */
class HsCode
{
	/**
	 * Return the HS code of an order line, or an empty string.
	 */
	public static function for_order_item(WC_Order_Item_Product $item): string
	{
		$product = $item->get_product();

		return $product ? self::for_product($product) : '';
	}

	/**
	 * Return the HS code of a product, or an empty string.
	 *
	 * A variation holds the term slug in post meta. It falls back to the value
	 * of its parent when it holds none.
	 */
	public static function for_product(WC_Product $product): string
	{
		$taxonomy = HsCodeAttribute::taxonomy();

		if (!$taxonomy) {
			return '';
		}

		if ($product->is_type('variation')) {
			$code = self::from_variation($product, $taxonomy);

			if ($code) {
				return $code;
			}
		}

		return trim((string) $product->get_attribute($taxonomy));
	}

	/**
	 * Resolve the term slug stored on a variation to the term name.
	 */
	private static function from_variation(WC_Product $variation, string $taxonomy): string
	{
		$slug = $variation->get_meta('attribute_' . $taxonomy);

		if (!$slug) {
			return '';
		}

		$term = get_term_by('slug', $slug, $taxonomy);

		return $term && !is_wp_error($term) ? $term->name : '';
	}
}
