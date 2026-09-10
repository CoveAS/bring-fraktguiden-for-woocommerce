<?php

namespace BringFraktguiden\Customs;

use WC_Order_Item_Product;
use WC_Product;

/**
 * Read the HS code of a product.
 *
 * This plugin stores the code in post meta. A shop that comes from Posten
 * Bring Checkout keeps its codes in a product attribute, so the attribute is
 * the fallback. See doc/posten-bring-checkout-nvit.md.
 */
class HsCode
{
	/**
	 * The code, on a product and on a variation.
	 */
	public const META = '_bring_hs_code';

	/**
	 * The transient that holds the codes a shop already uses.
	 */
	private const USED_TRANSIENT = 'bring_fraktguiden_used_hs_codes';

	/**
	 * The shortest code customs accepts.
	 */
	public const MIN_DIGITS = 6;

	/**
	 * The longest code the Booking API accepts.
	 */
	public const MAX_DIGITS = 10;

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
	 * A variation uses its own code only when the shop turns the override on.
	 */
	public static function for_product(WC_Product $product): string
	{
		if ($product->is_type('variation')) {
			$own = self::own_code($product);

			if ($own) {
				return $own;
			}

			$parent = wc_get_product($product->get_parent_id());

			return $parent ? self::for_product($parent) : '';
		}

		$code = self::digits($product->get_meta(self::META));

		return $code ?: self::from_attribute($product);
	}

	/**
	 * Return the digits of a code, or an empty string when it cannot be used.
	 *
	 * Tolltariffen prints a code with dots, for example 3305.10.00. Customs
	 * takes the plain digits. A code shorter than six digits names no goods, so
	 * this method drops it.
	 *
	 * @param mixed $code A code in any notation, or an empty value.
	 */
	public static function digits($code): string
	{
		$digits = self::strip($code);
		$length = strlen($digits);

		if ($length < self::MIN_DIGITS || $length > self::MAX_DIGITS) {
			return '';
		}

		return $digits;
	}

	/**
	 * Remove every character that is not a digit.
	 *
	 * The product screen stores the result, so a shop keeps a code of the wrong
	 * length on the screen and can correct it.
	 *
	 * @param mixed $code A code in any notation, or an empty value.
	 */
	public static function strip($code): string
	{
		return preg_replace('/\D/', '', (string) $code);
	}

	/**
	 * Return the code of a variation when its override is on.
	 */
	private static function own_code(WC_Product $variation): string
	{
		if ('yes' !== $variation->get_meta(CustomsFields::OVERRIDE_META)) {
			return '';
		}

		$code = self::digits($variation->get_meta(self::META));

		return $code ?: self::from_attribute($variation);
	}

	/**
	 * Return the code that Posten Bring Checkout stored, or an empty string.
	 *
	 * The code is the term name, for example 330510, never the term slug. A
	 * variation holds the term slug in post meta.
	 */
	private static function from_attribute(WC_Product $product): string
	{
		foreach (HsCodeAttribute::taxonomies() as $taxonomy) {
			$code = $product->is_type('variation')
				? self::from_variation($product, $taxonomy)
				: trim((string) $product->get_attribute($taxonomy));

			// A product with several terms gives a comma separated list. Only
			// one code can go to customs, so the first one wins.
			$code = self::digits(explode(',', $code)[0]);

			if ($code) {
				return $code;
			}
		}

		return '';
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

	/**
	 * Return every code the shop already uses, so a field can suggest them.
	 *
	 * @return string[]
	 */
	public static function used(): array
	{
		$cached = get_transient(self::USED_TRANSIENT);

		if (is_array($cached)) {
			return $cached;
		}

		global $wpdb;

		$codes = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_value FROM {$wpdb->postmeta}
				 WHERE meta_key = %s AND meta_value <> '' ORDER BY meta_value",
				self::META
			)
		);

		$codes = array_values(array_unique(array_filter(array_map([self::class, 'digits'], $codes))));

		set_transient(self::USED_TRANSIENT, $codes, DAY_IN_SECONDS);

		return $codes;
	}

	/**
	 * Drop the list of used codes. A save calls this.
	 */
	public static function forget_used(): void
	{
		delete_transient(self::USED_TRANSIENT);
	}
}
