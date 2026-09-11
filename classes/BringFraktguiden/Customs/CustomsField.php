<?php

namespace BringFraktguiden\Customs;

use WC_Product;

/**
 * One customs field, described once.
 *
 * The product screen, the variation screen and the saver all read the same
 * description, so a field is added or changed in one place. See
 * CustomsField::all().
 */
class CustomsField
{
	/**
	 * Every field is built with named arguments. See CustomsField::all().
	 *
	 * @param string   $meta        The post meta key.
	 * @param string   $label       The field label.
	 * @param string   $description The help text. The product screen shows it.
	 * @param string   $row_class   The layout of the row on a variation.
	 * @param \Closure $clean       Turns a posted value into the stored value.
	 * @param \Closure $placeholder Returns the value that applies when the field is empty.
	 * @param array    $attributes  Extra input attributes.
	 * @param string   $data_type   The WooCommerce input type, or an empty string.
	 * @param array    $choices     The options of a select, keyed by stored value.
	 *                              An empty array makes the field a text input.
	 * @param bool     $picker      Show a button that opens the HS code picker.
	 */
	private function __construct(
		public readonly string $meta,
		private readonly string $label,
		private readonly string $description,
		private readonly string $row_class,
		private readonly \Closure $clean,
		private readonly \Closure $placeholder,
		private readonly array $attributes = [],
		private readonly string $data_type = '',
		private readonly array $choices = [],
		private readonly bool $picker = false
	) {
	}

	/**
	 * Return every customs field, in the order the screens show them.
	 *
	 * @return self[]
	 */
	public static function all(): array
	{
		return [
			new self(
				meta: HsCode::META,
				label: __('HS code', 'bring-fraktguiden-for-woocommerce'),
				description: __('The customs code of the goods. Bring needs it for goods in transit and for export.', 'bring-fraktguiden-for-woocommerce'),
				row_class: 'form-row-first',
				clean: HsCode::strip(...),
				placeholder: fn(WC_Product $product, ?WC_Product $parent): string
					=> $parent ? HsCode::for_product($parent) : '',
				picker: true
			),
			new self(
				meta: GoodsDescription::META,
				label: __('Customs goods description', 'bring-fraktguiden-for-woocommerce'),
				description: __('Bring sends this text to customs. The product name is used when you leave it empty.', 'bring-fraktguiden-for-woocommerce'),
				row_class: 'form-row-last',
				clean: static fn(string $value): string => $value,
				placeholder: fn(WC_Product $product, ?WC_Product $parent): string
					=> ($parent ? trim((string) $parent->get_meta(GoodsDescription::META)) : '') ?: $product->get_name()
			),
			new self(
				meta: NetWeight::META,
				label: self::net_weight_label(),
				description: __('The weight of the goods alone, without the packing. The weight above is used when you leave it empty.', 'bring-fraktguiden-for-woocommerce'),
				row_class: 'form-row-full',
				clean: wc_format_decimal(...),
				placeholder: fn(WC_Product $product, ?WC_Product $parent): string => (string) $product->get_weight(),
				data_type: 'decimal'
			),
			new self(
				meta: CountryOfOrigin::META,
				label: __('Country of origin', 'bring-fraktguiden-for-woocommerce'),
				description: __('The country the goods come from. Bring needs it for export, and refuses a booking without it.', 'bring-fraktguiden-for-woocommerce'),
				row_class: 'form-row-full',
				clean: CountryOfOrigin::clean(...),
				placeholder: fn(WC_Product $product, ?WC_Product $parent): string
					=> $parent ? CountryOfOrigin::name(CountryOfOrigin::for_product($parent)) : '',
				choices: CountryOfOrigin::countries()
			),
		];
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

	/**
	 * Show the field on the Shipping tab of a product.
	 */
	public function render_for_product(?WC_Product $product): void
	{
		$args = $this->args() + [
			'id'          => $this->meta,
			'value'       => $product ? (string) $product->get_meta($this->meta) : '',
			'placeholder' => $product ? ($this->placeholder)($product, null) : '',
			'description' => $this->description,
			'desc_tip'    => true,
		];

		$this->render($args, $args['placeholder']);
	}

	/**
	 * Show the field on one variation.
	 *
	 * @param int $loop The index of the variation in the form.
	 */
	public function render_for_variation(WC_Product $variation, ?WC_Product $parent, int $loop, bool $override): void
	{
		$name        = $this->meta . '[' . $loop . ']';
		$placeholder = ($this->placeholder)($variation, $parent);

		$args = $this->args() + [
			'id'            => $name,
			'name'          => $name,
			'value'         => $variation->get_meta($this->meta),
			'placeholder'   => $placeholder,
			'wrapper_class' => 'form-row ' . $this->row_class . ' bring-customs-override' . ($override ? '' : ' hidden'),
		];

		$this->render($args, $placeholder);
	}

	/**
	 * Print the field, as a select when it has choices and as a text input otherwise.
	 *
	 * A select carries an empty option, because every customs field is optional
	 * on the screen. The option names the value that applies when the shop
	 * leaves the field empty, the same answer the placeholder gives.
	 *
	 * @param array  $args        The WooCommerce field arguments.
	 * @param string $placeholder The value that applies when the field is empty.
	 */
	private function render(array $args, string $placeholder): void
	{
		if ($this->picker) {
			$this->render_button($args);

			return;
		}

		if (!$this->choices) {
			woocommerce_wp_text_input($args);

			return;
		}

		$empty = $placeholder ?: __('Not set', 'bring-fraktguiden-for-woocommerce');

		$args['options'] = ['' => $empty] + $this->choices;

		woocommerce_wp_select($args);
	}

	/**
	 * Print the button that opens the HS code picker.
	 *
	 * WooCommerce has no field of this shape, so the row is written out here.
	 * It carries the same wrapper class as a WooCommerce field, because the
	 * variation screen hides a row by that class.
	 *
	 * @param array $args The WooCommerce field arguments.
	 */
	private function render_button(array $args): void
	{
		printf(
			'<p class="form-field %s"><label>%s</label>%s<span class="description">%s</span></p>',
			esc_attr($args['wrapper_class'] ?? 'form-field-wide'),
			esc_html($this->label),
			HsCodeButton::html($args['name'] ?? $args['id'], (string) ($args['value'] ?? '')),
			esc_html($this->description)
		);
	}

	/**
	 * Write the posted value, and leave the stored value when none was posted.
	 *
	 * @param int|null $loop The index of the variation in the form, or null for a product.
	 */
	public function save(WC_Product $product, ?int $loop = null): void
	{
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce checks the nonce before it fires the save hooks.
		$value = null === $loop
			? ($_POST[$this->meta] ?? null)
			: ($_POST[$this->meta][$loop] ?? null);
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if (null === $value) {
			return;
		}

		$product->update_meta_data($this->meta, ($this->clean)(sanitize_text_field(wp_unslash((string) $value))));
	}

	/**
	 * Return the arguments both screens share.
	 *
	 * @return array<string, mixed>
	 */
	private function args(): array
	{
		$args = [
			'label'             => $this->label,
			'custom_attributes' => $this->attributes,
		];

		if ($this->data_type) {
			$args['data_type'] = $this->data_type;
		}

		return $args;
	}
}
