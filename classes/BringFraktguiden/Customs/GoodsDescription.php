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
	 * Whether a variation uses its own customs data. Holds 'yes' or 'no'.
	 */
	public const OVERRIDE_META = '_bring_customs_override';

	public static function init(): void
	{
		add_action('woocommerce_product_options_shipping_product_data', [self::class, 'product_field']);
		add_action('woocommerce_process_product_meta', [self::class, 'save_product']);
		add_action('woocommerce_product_after_variable_attributes', [self::class, 'variation_fields'], 10, 3);
		add_action('woocommerce_save_product_variation', [self::class, 'save_variation'], 10, 2);
		add_action('admin_footer', [self::class, 'print_toggle_script']);
	}

	/**
	 * Let the override checkbox show and hide the fields under it.
	 *
	 * WooCommerce loads the variation form over ajax, so the listener sits on
	 * the document.
	 */
	public static function print_toggle_script(): void
	{
		$screen = get_current_screen();

		if (!$screen || 'product' !== $screen->id) {
			return;
		}

		$name = esc_js(self::OVERRIDE_META);

		echo <<<HTML
			<script>
			document.addEventListener('change', function (event) {
				var box = event.target;

				if (!box.name || box.name.indexOf('{$name}') !== 0) {
					return;
				}

				var panel = box.closest('.woocommerce_variable_attributes');

				if (!panel) {
					return;
				}

				panel.querySelectorAll('.bring-customs-override').forEach(function (row) {
					row.classList.toggle('hidden', !box.checked);
				});
			});
			</script>
			HTML;
	}

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
		if ('yes' !== $variation->get_meta(self::OVERRIDE_META)) {
			return '';
		}

		return trim((string) $variation->get_meta(self::META));
	}

	/**
	 * Show the text input on the Shipping tab of a product.
	 */
	public static function product_field(): void
	{
		global $product_object;

		woocommerce_wp_text_input([
			'id'          => self::META,
			'label'       => __('Customs goods description', 'bring-fraktguiden-for-woocommerce'),
			'placeholder' => $product_object ? $product_object->get_name() : '',
			'description' => __('Bring sends this text to customs. The product name is used when you leave it empty.', 'bring-fraktguiden-for-woocommerce'),
			'desc_tip'    => true,
		]);
	}

	public static function save_product(int $product_id): void
	{
		if (!isset($_POST[self::META])) {
			return;
		}

		$product = wc_get_product($product_id);

		if (!$product) {
			return;
		}

		$product->update_meta_data(self::META, self::clean($_POST[self::META]));
		$product->save();
	}

	/**
	 * Show the override checkbox and the text input on a variation.
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

		$override = 'yes' === $product->get_meta(self::OVERRIDE_META);
		$parent   = wc_get_product($product->get_parent_id());

		woocommerce_wp_checkbox([
			'id'            => self::OVERRIDE_META . '[' . $loop . ']',
			'name'          => self::OVERRIDE_META . '[' . $loop . ']',
			'value'         => $override ? 'yes' : 'no',
			'label'         => __('Override the customs data for this variation', 'bring-fraktguiden-for-woocommerce'),
			'wrapper_class' => 'form-row form-row-full',
		]);

		woocommerce_wp_text_input([
			'id'            => self::META . '[' . $loop . ']',
			'name'          => self::META . '[' . $loop . ']',
			'value'         => $product->get_meta(self::META),
			'label'         => __('Customs goods description', 'bring-fraktguiden-for-woocommerce'),
			'placeholder'   => self::variation_placeholder($product, $parent),
			'wrapper_class' => 'form-row form-row-full bring-customs-override' . ($override ? '' : ' hidden'),
		]);
	}

	/**
	 * Return the text that applies to a variation when it has none of its own.
	 */
	private static function variation_placeholder(WC_Product $variation, ?WC_Product $parent): string
	{
		$inherited = $parent ? trim((string) $parent->get_meta(self::META)) : '';

		return $inherited ?: $variation->get_name();
	}

	/**
	 * Save a variation, and keep its text when the override is off.
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

		$override = isset($_POST[self::OVERRIDE_META][$loop]) ? 'yes' : 'no';
		$variation->update_meta_data(self::OVERRIDE_META, $override);

		if (isset($_POST[self::META][$loop])) {
			$variation->update_meta_data(self::META, self::clean($_POST[self::META][$loop]));
		}

		$variation->save();
	}

	private static function clean($value): string
	{
		return sanitize_text_field(wp_unslash((string) $value));
	}
}
