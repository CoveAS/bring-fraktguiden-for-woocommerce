<?php

namespace BringFraktguiden\Customs;

use WC_Order_Item_Product;
use WC_Product;

/**
 * The country the goods come from, which customs calls the country of origin.
 *
 * Bring names the field `countryCodeOrigin` and requires it on every entry of
 * `customsDeclarations` for an export. A booking without it fails with
 * BOOK-INPUT-028, "Invalid country code". See doc/export.md.
 *
 * The country is a trait of the goods, not of the shop, so a reseller sets a
 * different country per product. There is no fallback for that reason.
 */
class CountryOfOrigin
{
	/**
	 * The code, on a product and on a variation.
	 */
	public const META = '_bring_country_of_origin';

	/**
	 * Return the country code of an order line, or an empty string.
	 */
	public static function for_order_item(WC_Order_Item_Product $item): string
	{
		$product = $item->get_product();

		return $product ? self::for_product($product) : '';
	}

	/**
	 * Return the country code of a product, or an empty string.
	 *
	 * A variation uses its own country only when the shop turns the override on.
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

		return self::clean($product->get_meta(self::META));
	}

	/**
	 * Return the code when a country carries it, or an empty string.
	 *
	 * Customs takes a two letter ISO-3166-1 code. Bring refuses anything else,
	 * so a value outside the WooCommerce country list is dropped here.
	 *
	 * @param mixed $code A country code in any case, or an empty value.
	 */
	public static function clean($code): string
	{
		$code = strtoupper(trim((string) $code));

		return isset(self::countries()[$code]) ? $code : '';
	}

	/**
	 * Return the name of a country, or an empty string.
	 */
	public static function name(string $code): string
	{
		return self::countries()[$code] ?? '';
	}

	/**
	 * Return every country, keyed by code, as the field options.
	 *
	 * @return array<string, string>
	 */
	public static function countries(): array
	{
		return WC()->countries ? WC()->countries->get_countries() : [];
	}

	/**
	 * Return the country of a variation when its override is on.
	 */
	private static function own_code(WC_Product $variation): string
	{
		if (!Override::is_on($variation)) {
			return '';
		}

		return self::clean($variation->get_meta(self::META));
	}
}
