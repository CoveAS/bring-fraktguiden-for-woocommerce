<?php

namespace BringFraktguiden\Customs;

/**
 * The HS code attribute that Posten Bring Checkout writes.
 *
 * This plugin stores its own codes in post meta. It reads the attribute so a
 * shop that moves from that plugin keeps its codes. It never writes one.
 * See doc/posten-bring-checkout-nvit.md.
 */
class HsCodeAttribute
{
	/**
	 * Accepted attribute slugs, in the order they are tried.
	 */
	public const SLUGS = ['hscode', 'htscode', 'hs-code', 'hts-code'];

	/**
	 * Return the first accepted slug that exists, or null.
	 */
	public static function find(): ?string
	{
		if (!function_exists('wc_get_attribute_taxonomies')) {
			return null;
		}

		$existing = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_name');

		foreach (self::SLUGS as $slug) {
			if (in_array($slug, $existing, true)) {
				return $slug;
			}
		}

		return null;
	}

	/**
	 * Return the taxonomy names of every accepted slug that exists.
	 *
	 * A shop may hold more than one, so a caller tries them in order.
	 *
	 * @return string[]
	 */
	public static function taxonomies(): array
	{
		if (!function_exists('wc_get_attribute_taxonomies')) {
			return [];
		}

		$existing = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_name');

		return array_map(
			'wc_attribute_taxonomy_name',
			array_values(array_intersect(self::SLUGS, $existing))
		);
	}
}
