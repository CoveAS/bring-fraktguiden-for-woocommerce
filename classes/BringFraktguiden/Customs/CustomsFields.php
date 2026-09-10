<?php

namespace BringFraktguiden\Customs;

use WC_Product;

/**
 * The customs fields on the product edit screen.
 *
 * A product holds an HS code, a goods description and a net weight. A variation
 * may hold its own set, behind an override checkbox, because one parent can
 * hold goods that differ, for example shampoo and conditioner.
 *
 * All three fields are optional. The HS code has no fallback. The goods
 * description falls back to the order line name. The net weight falls back to
 * the WooCommerce weight.
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
		add_action('admin_enqueue_scripts', [self::class, 'enqueue_script']);
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
			'custom_attributes' => self::hs_code_attributes(),
		]);

		woocommerce_wp_text_input([
			'id'          => GoodsDescription::META,
			'label'       => __('Customs goods description', 'bring-fraktguiden-for-woocommerce'),
			'placeholder' => $product_object ? $product_object->get_name() : '',
			'description' => __('Bring sends this text to customs. The product name is used when you leave it empty.', 'bring-fraktguiden-for-woocommerce'),
			'desc_tip'    => true,
		]);

		woocommerce_wp_text_input([
			'id'          => NetWeight::META,
			'label'       => self::net_weight_label(),
			'placeholder' => $product_object ? $product_object->get_weight() : '',
			'description' => __('The weight of the goods alone, without the packing. The weight above is used when you leave it empty.', 'bring-fraktguiden-for-woocommerce'),
			'desc_tip'    => true,
			'data_type'   => 'decimal',
		]);
	}

	/**
	 * Return the attributes that make the browser check an HS code.
	 *
	 * @return array<string, string>
	 */
	private static function hs_code_attributes(): array
	{
		return [
			'list'      => self::DATALIST_ID,
			'inputmode' => 'numeric',
			'pattern'   => '[0-9]{' . HsCode::MIN_DIGITS . ',' . HsCode::MAX_DIGITS . '}',
			'title'     => self::hs_code_rule(),
		];
	}

	/**
	 * Return the sentence that states the length of an HS code.
	 */
	private static function hs_code_rule(): string
	{
		return sprintf(
			/* translators: 1: the shortest code length, 2: the longest code length. */
			__('An HS code holds %1$d to %2$d digits, without dots.', 'bring-fraktguiden-for-woocommerce'),
			HsCode::MIN_DIGITS,
			HsCode::MAX_DIGITS
		);
	}

	/**
	 * Return the net weight label, with the weight unit of the shop.
	 */
	private static function net_weight_label(): string
	{
		return sprintf(
			/* translators: %s: the weight unit of the shop, for example kg. */
			__('Customs net weight (%s)', 'bring-fraktguiden-for-woocommerce'),
			get_option('woocommerce_weight_unit')
		);
	}

	public static function save_product(int $product_id): void
	{
		$product = wc_get_product($product_id);

		if (!$product) {
			return;
		}

		self::save_meta(
			$product,
			$_POST[HsCode::META] ?? null,
			$_POST[GoodsDescription::META] ?? null,
			$_POST[NetWeight::META] ?? null
		);
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
			'custom_attributes' => self::hs_code_attributes(),
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

		woocommerce_wp_text_input([
			'id'            => NetWeight::META . '[' . $loop . ']',
			'name'          => NetWeight::META . '[' . $loop . ']',
			'value'         => $product->get_meta(NetWeight::META),
			'label'         => self::net_weight_label(),
			'placeholder'   => $product->get_weight(),
			'data_type'     => 'decimal',
			'wrapper_class' => 'form-row form-row-full bring-customs-override' . $hidden,
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
			$_POST[GoodsDescription::META][$loop] ?? null,
			$_POST[NetWeight::META][$loop] ?? null
		);
	}

	/**
	 * Write the fields, then drop the list of used codes.
	 */
	private static function save_meta(WC_Product $product, $hs_code, $description, $net_weight): void
	{
		if (null !== $hs_code) {
			$product->update_meta_data(HsCode::META, HsCode::strip(self::clean($hs_code)));
		}

		if (null !== $description) {
			$product->update_meta_data(GoodsDescription::META, self::clean($description));
		}

		if (null !== $net_weight) {
			$product->update_meta_data(NetWeight::META, wc_format_decimal(self::clean($net_weight)));
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
	 * Load the script of the customs fields on the product screen.
	 */
	public static function enqueue_script(): void
	{
		$screen = get_current_screen();

		if (!$screen || 'product' !== $screen->id) {
			return;
		}

		wp_enqueue_script(
			'bring-customs-fields',
			plugin_dir_url(dirname(__DIR__, 2)) . 'resources/js/customs-fields.js',
			['jquery'],
			\Bring_Fraktguiden::VERSION,
			true
		);

		wp_localize_script('bring-customs-fields', 'bringCustomsFields', [
			'overrideName' => self::OVERRIDE_META,
			'codeName'     => HsCode::META,
			'netName'      => NetWeight::META,
			'minDigits'    => HsCode::MIN_DIGITS,
			'maxDigits'    => HsCode::MAX_DIGITS,
			'codeRule'     => self::hs_code_rule(),
			'tooHeavy'     => __('The net weight is above the weight of the product.', 'bring-fraktguiden-for-woocommerce'),
		]);
	}

	private static function clean($value): string
	{
		return sanitize_text_field(wp_unslash((string) $value));
	}
}
