<?php

namespace BringFraktguiden\Customs;

use WC_Product;

/**
 * The customs fields on the product edit screen.
 *
 * A product carries the customs data for every variation. A variation carries
 * its own only when the shop turns the override on. See Override.
 *
 * The fields themselves are described in CustomsField::all(). This class only
 * places them on the two screens and saves what the shop posts.
 */
class CustomsFields
{
	public static function init(): void
	{
		add_action('woocommerce_product_options_shipping_product_data', [self::class, 'product_fields']);
		add_action('woocommerce_process_product_meta', [self::class, 'save_product']);
		add_action('woocommerce_product_after_variable_attributes', [self::class, 'variation_fields'], 10, 3);
		add_action('woocommerce_save_product_variation', [self::class, 'save_variation'], 10, 2);
		add_action('admin_enqueue_scripts', [self::class, 'enqueue_script']);
	}

	/**
	 * Show the fields on the Shipping tab of a product.
	 */
	public static function product_fields(): void
	{
		global $product_object;

		foreach (CustomsField::all() as $field) {
			$field->render_for_product($product_object instanceof WC_Product ? $product_object : null);
		}
	}

	public static function save_product(int $product_id): void
	{
		$product = wc_get_product($product_id);

		if (!$product) {
			return;
		}

		foreach (CustomsField::all() as $field) {
			$field->save($product);
		}

		$product->save();
	}

	/**
	 * Show the override checkbox and the fields on a variation.
	 *
	 * @param int      $loop      The index of the variation in the form.
	 * @param array    $data      The variation data. Unused.
	 * @param \WP_Post $variation The variation post.
	 */
	public static function variation_fields(int $loop, array $data, \WP_Post $variation): void
	{
		$product = wc_get_product($variation->ID);

		if (!$product) {
			return;
		}

		$override = Override::is_on($product);
		$parent   = wc_get_product($product->get_parent_id()) ?: null;

		woocommerce_wp_checkbox([
			'id'            => Override::META . '[' . $loop . ']',
			'name'          => Override::META . '[' . $loop . ']',
			'value'         => $override ? 'yes' : 'no',
			'label'         => __('Override the customs data for this variation', 'bring-fraktguiden-for-woocommerce'),
			'wrapper_class' => 'form-row form-row-full',
		]);

		foreach (CustomsField::all() as $field) {
			$field->render_for_variation($product, $parent, $loop, $override);
		}
	}

	/**
	 * Save a variation, and keep its values when the override is off.
	 *
	 * @param int $variation_id The variation.
	 * @param int $loop         The index of the variation in the form.
	 */
	public static function save_variation(int $variation_id, int $loop): void
	{
		$variation = wc_get_product($variation_id);

		if (!$variation) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce checks the nonce before it fires the save hooks.
		$override = isset($_POST[Override::META][$loop]) ? 'yes' : 'no';

		$variation->update_meta_data(Override::META, $override);

		foreach (CustomsField::all() as $field) {
			$field->save($variation, $loop);
		}

		$variation->save();
	}

	/**
	 * Load the script of the customs fields on the product screen.
	 */
	public static function enqueue_script(): void
	{
		$screen = get_current_screen();

		if (!$screen || 'product' !== $screen->id) {
			return;
		}

		HsCodePicker::enqueue();

		wp_enqueue_script(
			'bring-customs-fields',
			plugin_dir_url(dirname(__DIR__, 2)) . 'resources/js/customs-fields.js',
			['jquery'],
			\Bring_Fraktguiden::VERSION,
			true
		);

		wp_localize_script('bring-customs-fields', 'bringCustomsFields', [
			'overrideName' => Override::META,
			'netName'      => NetWeight::META,
			'tooHeavy'     => __('The net weight is above the weight of the product.', 'bring-fraktguiden-for-woocommerce'),
		]);
	}
}
