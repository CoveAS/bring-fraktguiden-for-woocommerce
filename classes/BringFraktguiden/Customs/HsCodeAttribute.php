<?php

namespace BringFraktguiden\Customs;

/**
 * The HS code lives in a WooCommerce product attribute.
 *
 * Posten Bring Checkout stores it the same way, so a shop that moves from that
 * plugin keeps its codes. See doc/posten-bring-checkout-nvit.md.
 */
class HsCodeAttribute
{
	/**
	 * Accepted attribute slugs, in the order they are tried.
	 */
	public const SLUGS = ['hscode', 'htscode', 'hs-code', 'hts-code'];

	/**
	 * The slug used when none of the accepted slugs exists.
	 */
	public const DEFAULT_SLUG = 'hscode';

	/**
	 * The admin_post action name.
	 */
	public const ACTION = 'bring_fraktguiden_create_hs_code_attribute';

	public static function init(): void
	{
		add_action('admin_post_' . self::ACTION, [self::class, 'handle_create']);
	}

	/**
	 * Create the attribute, then go back to the home page.
	 */
	public static function handle_create(): void
	{
		if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
			wp_die(esc_html__('This action needs a form post.', 'bring-fraktguiden-for-woocommerce'));
		}

		check_admin_referer(self::ACTION);

		if (!current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('You are not allowed to do this.', 'bring-fraktguiden-for-woocommerce'));
		}

		$result = self::create();

		if (is_wp_error($result)) {
			wp_die(esc_html($result->get_error_message()));
		}

		wp_safe_redirect(admin_url('admin.php?page=bring_fraktguiden_home'));
		exit;
	}

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
	 * Return the taxonomy name of the first accepted slug that exists, or null.
	 */
	public static function taxonomy(): ?string
	{
		$slug = self::find();

		return $slug ? wc_attribute_taxonomy_name($slug) : null;
	}

	/**
	 * Create the attribute when none of the accepted slugs exists.
	 *
	 * WooCommerce registers the taxonomy on the next request, so the attribute
	 * only appears on the product edit screen after a page load.
	 *
	 * @return string|\WP_Error The slug in use, or the error from WooCommerce.
	 */
	public static function create(): string|\WP_Error
	{
		$slug = self::find();

		if ($slug) {
			return $slug;
		}

		$result = wc_create_attribute([
			'name'         => __('HS Code', 'bring-fraktguiden-for-woocommerce'),
			'slug'         => self::DEFAULT_SLUG,
			'type'         => 'select',
			'order_by'     => 'name',
			'has_archives' => false,
		]);

		if (is_wp_error($result)) {
			return $result;
		}

		return self::DEFAULT_SLUG;
	}
}
