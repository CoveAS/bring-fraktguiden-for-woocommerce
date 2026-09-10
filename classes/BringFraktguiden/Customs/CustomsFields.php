<?php

namespace BringFraktguiden\Customs;

use WC_Product;

/**
 * The customs fields on the product edit screen.
 *
 * A product holds one HS code and one goods description. A variation may hold
 * its own pair, behind an override checkbox, because one parent can hold goods
 * that differ, for example shampoo and conditioner.
 *
 * Both fields are optional. The HS code has no fallback. The goods description
 * falls back to the order line name.
 */
class CustomsFields
{
	/**
	 * Whether a variation uses its own customs data. Holds 'yes' or 'no'.
	 */
	public const OVERRIDE_META = '_bring_customs_override';

	/**
	 * The id of the list of codes the shop already uses.
	 */
	private const DATALIST_ID = 'bring-used-hs-codes';

	public static function init(): void
	{
		add_action('woocommerce_product_options_shipping_product_data', [self::class, 'product_fields']);
		add_action('woocommerce_process_product_meta', [self::class, 'save_product']);
		add_action('woocommerce_product_after_variable_attributes', [self::class, 'variation_fields'], 10, 3);
		add_action('woocommerce_save_product_variation', [self::class, 'save_variation'], 10, 2);
		add_action('admin_footer', [self::class, 'print_toggle_script']);
	}

	/**
	 * Show the fields on the Shipping tab of a product.
	 */
	public static function product_fields(): void
	{
		global $product_object;

		self::print_datalist();

		woocommerce_wp_text_input([
			'id'                => HsCode::META,
			'label'             => __('HS code', 'bring-fraktguiden-for-woocommerce'),
			'description'       => __('The customs code of the goods. Bring needs it for goods in transit and for export.', 'bring-fraktguiden-for-woocommerce'),
			'desc_tip'          => true,
			'custom_attributes' => ['list' => self::DATALIST_ID],
		]);

		woocommerce_wp_text_input([
			'id'          => GoodsDescription::META,
			'label'       => __('Customs goods description', 'bring-fraktguiden-for-woocommerce'),
			'placeholder' => $product_object ? $product_object->get_name() : '',
			'description' => __('Bring sends this text to customs. The product name is used when you leave it empty.', 'bring-fraktguiden-for-woocommerce'),
			'desc_tip'    => true,
		]);
	}

	public static function save_product(int $product_id): void
	{
		$product = wc_get_product($product_id);

		if (!$product) {
			return;
		}

		self::save_meta($product, $_POST[HsCode::META] ?? null, $_POST[GoodsDescription::META] ?? null);
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

		$override = 'yes' === $product->get_meta(self::OVERRIDE_META);
		$parent   = wc_get_product($product->get_parent_id());
		$hidden   = $override ? '' : ' hidden';

		woocommerce_wp_checkbox([
			'id'            => self::OVERRIDE_META . '[' . $loop . ']',
			'name'          => self::OVERRIDE_META . '[' . $loop . ']',
			'value'         => $override ? 'yes' : 'no',
			'label'         => __('Override the customs data for this variation', 'bring-fraktguiden-for-woocommerce'),
			'wrapper_class' => 'form-row form-row-full',
		]);

		woocommerce_wp_text_input([
			'id'                => HsCode::META . '[' . $loop . ']',
			'name'              => HsCode::META . '[' . $loop . ']',
			'value'             => $product->get_meta(HsCode::META),
			'label'             => __('HS code', 'bring-fraktguiden-for-woocommerce'),
			'placeholder'       => $parent ? HsCode::for_product($parent) : '',
			'custom_attributes' => ['list' => self::DATALIST_ID],
			'wrapper_class'     => 'form-row form-row-first bring-customs-override' . $hidden,
		]);

		woocommerce_wp_text_input([
			'id'            => GoodsDescription::META . '[' . $loop . ']',
			'name'          => GoodsDescription::META . '[' . $loop . ']',
			'value'         => $product->get_meta(GoodsDescription::META),
			'label'         => __('Customs goods description', 'bring-fraktguiden-for-woocommerce'),
			'placeholder'   => self::description_placeholder($product, $parent),
			'wrapper_class' => 'form-row form-row-last bring-customs-override' . $hidden,
		]);
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

		$variation->update_meta_data(
			self::OVERRIDE_META,
			isset($_POST[self::OVERRIDE_META][$loop]) ? 'yes' : 'no'
		);

		self::save_meta(
			$variation,
			$_POST[HsCode::META][$loop] ?? null,
			$_POST[GoodsDescription::META][$loop] ?? null
		);
	}

	/**
	 * Write both fields, then drop the list of used codes.
	 */
	private static function save_meta(WC_Product $product, $hs_code, $description): void
	{
		if (null !== $hs_code) {
			$product->update_meta_data(HsCode::META, self::clean($hs_code));
		}

		if (null !== $description) {
			$product->update_meta_data(GoodsDescription::META, self::clean($description));
		}

		$product->save();
		HsCode::forget_used();
	}

	/**
	 * Return the description that applies when a variation has none of its own.
	 */
	private static function description_placeholder(WC_Product $variation, ?WC_Product $parent): string
	{
		$inherited = $parent ? trim((string) $parent->get_meta(GoodsDescription::META)) : '';

		return $inherited ?: $variation->get_name();
	}

	/**
	 * Print the codes the shop already uses, so every field can suggest them.
	 */
	private static function print_datalist(): void
	{
		$codes = HsCode::used();

		if (!$codes) {
			return;
		}

		echo '<datalist id="' . esc_attr(self::DATALIST_ID) . '">';

		foreach ($codes as $code) {
			echo '<option value="' . esc_attr($code) . '"></option>';
		}

		echo '</datalist>';
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

	private static function clean($value): string
	{
		return sanitize_text_field(wp_unslash((string) $value));
	}
}
