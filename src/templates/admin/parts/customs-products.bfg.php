<?php

use BringFraktguiden\Customs\CustomsField;
use BringFraktguiden\Customs\HsCode;
use BringFraktguiden\Customs\HsCodeDatalist;

/**
 * @var array<int, array{name: string, image: string, code: string}> $hs_rows
 */
?>

<div class="bfg-customs-products">
	<p class="bfg-customs-products__title"><strong><t>HS codes of the products</t></strong></p>
	<p class="bfg-customs-products__rule"><?php echo esc_html(CustomsField::hs_code_rule()); ?></p>

	<?php HsCodeDatalist::render(); ?>

	<div class="bfg-customs-products__tools">
		<button
			type="button"
			class="bfg-btn bfg-btn--secondary bfg-btn--sm"
			data-bfg-hs-toggle
			data-select="<?php esc_attr_e('Select all', 'bring-fraktguiden-for-woocommerce'); ?>"
			data-deselect="<?php esc_attr_e('Deselect all', 'bring-fraktguiden-for-woocommerce'); ?>"
		><t>Select all</t></button>
		<input
			type="text"
			class="bfg-customs-products__bulk"
			data-bfg-hs-bulk
			list="<?php echo esc_attr(HsCodeDatalist::ID); ?>"
			inputmode="numeric"
			placeholder="<?php esc_attr_e('Code for the marked products', 'bring-fraktguiden-for-woocommerce'); ?>"
			aria-label="<?php esc_attr_e('Code for the marked products', 'bring-fraktguiden-for-woocommerce'); ?>"
		>
		<button type="button" class="bfg-btn bfg-btn--secondary bfg-btn--sm" data-bfg-hs-set><t>Set the code</t></button>
	</div>

	<?php foreach ($hs_rows as $id => $row) : ?>
		<div class="bfg-customs-products__row">
			<input
				type="checkbox"
				class="bfg-customs-products__mark"
				data-bfg-hs-mark="<?php echo esc_attr($id); ?>"
				aria-label="<?php printf(esc_attr__('Mark %s', 'bring-fraktguiden-for-woocommerce'), esc_attr($row['name'])); ?>"
			>
			<div class="bfg-customs-products__image"><?php echo wp_kses_post($row['image']); ?></div>
			<div class="bfg-customs-products__field">
				<label for="bfg-hs-<?php echo esc_attr($id); ?>"><?php echo esc_html($row['name']); ?></label>
				<input
					type="text"
					id="bfg-hs-<?php echo esc_attr($id); ?>"
					value="<?php echo esc_attr($row['code']); ?>"
					data-hs-product="<?php echo esc_attr($id); ?>"
					list="<?php echo esc_attr(HsCodeDatalist::ID); ?>"
					inputmode="numeric"
					pattern="[0-9]{<?php echo esc_attr(HsCode::MIN_DIGITS); ?>,<?php echo esc_attr(HsCode::MAX_DIGITS); ?>}"
					placeholder="<?php esc_attr_e('Add a 6 to 10 digit HS code', 'bring-fraktguiden-for-woocommerce'); ?>"
					title="<?php echo esc_attr(CustomsField::hs_code_rule()); ?>"
				>
			</div>
		</div>
	<?php endforeach; ?>
</div>
