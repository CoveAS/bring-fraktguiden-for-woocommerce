<?php

namespace BringFraktguiden\Customs;

/**
 * The script of the HS code picker, on any screen that sets an HS code.
 */
class HsCodePicker
{
	public const HANDLE = 'bfg-hs-code-picker';

	/**
	 * Load the picker. A screen calls this from admin_enqueue_scripts.
	 */
	public static function enqueue(): void
	{
		if (wp_script_is(self::HANDLE, 'enqueued')) {
			return;
		}

		$plugin_dir = dirname(__DIR__, 3);

		wp_enqueue_script(
			self::HANDLE,
			plugins_url(basename($plugin_dir) . '/build/js/hs-code-picker.js'),
			[],
			\Bring_Fraktguiden::VERSION,
			true
		);

		// The file is an ES module, and it imports a shared chunk.
		add_filter('script_loader_tag', [self::class, 'add_type_module'], 10, 2);

		self::add_config(self::HANDLE);
	}

	/**
	 * Give the picker its settings through a script that already imports it.
	 *
	 * A script that imports the picker must not enqueue the file as well. The
	 * enqueued file carries a version in its URL and the import does not, so
	 * the browser would load the module twice and build two modals.
	 *
	 * @param string $handle The script that imports the picker.
	 */
	public static function add_config(string $handle): void
	{
		wp_localize_script($handle, 'bringHsCodePicker', [
			'url'   => rest_url(HsCodeIndexRoute::ROUTE_NAMESPACE . HsCodeIndexRoute::ROUTE),
			'nonce' => wp_create_nonce('wp_rest'),
			'i18n'  => [
				'title'  => __('Choose an HS code', 'bring-fraktguiden-for-woocommerce'),
				'search' => __('Search for a code or for the goods', 'bring-fraktguiden-for-woocommerce'),
				'hint'   => __('Type a code, or type what the goods are.', 'bring-fraktguiden-for-woocommerce'),
				'loading' => __('Loading the customs tariff.', 'bring-fraktguiden-for-woocommerce'),
				'empty'  => __('No code matches.', 'bring-fraktguiden-for-woocommerce'),
				'choose' => __('Choose an HS code', 'bring-fraktguiden-for-woocommerce'),
				'close'  => __('Close', 'bring-fraktguiden-for-woocommerce'),
			],
		]);
	}

	/**
	 * Mark the picker script as a module.
	 *
	 * @param string $tag    The whole script tag.
	 * @param string $handle The script this tag loads.
	 */
	public static function add_type_module(string $tag, string $handle): string
	{
		return self::HANDLE === $handle
			? str_replace('<script ', '<script type="module" ', $tag)
			: $tag;
	}
}
