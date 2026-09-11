<?php

use BringFraktguiden\Customs\CustomsField;
use BringFraktguiden\Customs\HsCode;
use BringFraktguiden\Customs\HsCodeDatalist;

/**
 * @var array<int, array{name: string, image: string, code: string}> $hs_rows
 */

$hs_missing = count(array_filter($hs_rows, static fn (array $row): bool => '' === $row['code']));
?>

<details class="bfg-customs-products<?php echo $hs_missing ? '' : ' bfg-customs-products--ok'; ?>" data-bfg-hs-panel <?php echo $hs_missing ? 'open' : ''; ?>>
	<summary class="bfg-customs-products__summary">
		<span class="bfg-customs-products__icon bfg-customs-products__icon--warn" aria-hidden="true">
			<svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 7.5V10.8333M10 14.1667H10.0083M8.5747 2.68333L1.51637 14.1667C1.34889 14.4566 1.26025 14.7854 1.25932 15.1202C1.25838 15.4551 1.34518 15.7843 1.51103 16.0752C1.67688 16.366 1.91598 16.6083 2.20453 16.7781C2.49308 16.9479 2.82106 17.0392 3.15587 17.0429H16.8442C17.179 17.0392 17.507 16.9479 17.7955 16.7781C18.0841 16.6083 18.3232 16.366 18.489 16.0752C18.6549 15.7843 18.7417 15.4551 18.7407 15.1202C18.7398 14.7854 18.6512 14.4566 18.4837 14.1667L11.4254 2.68333C11.2544 2.40158 11.0136 2.16867 10.7263 2.00711C10.439 1.84555 10.1149 1.76068 9.78504 1.76068C9.45518 1.76068 9.13108 1.84555 8.84379 2.00711C8.5565 2.16867 8.31569 2.40158 8.1447 2.68333H8.5747Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</span>
		<span class="bfg-customs-products__icon bfg-customs-products__icon--ok" aria-hidden="true">
			<svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.167 10.417L8.333 14.583L15.833 5.417" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</span>
		<strong><t>HS codes of the products</t></strong>
		<span
			class="bfg-customs-products__count"
			data-bfg-hs-count
			data-missing="<?php esc_attr_e('without a code: %d', 'bring-fraktguiden-for-woocommerce'); ?>"
			data-done="<?php esc_attr_e('every product has a code', 'bring-fraktguiden-for-woocommerce'); ?>"
		><?php echo $hs_missing
			? esc_html(sprintf(__('without a code: %d', 'bring-fraktguiden-for-woocommerce'), $hs_missing))
			: esc_html__('every product has a code', 'bring-fraktguiden-for-woocommerce'); ?></span>
	</summary>

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
		<span class="bfg-customs-products__bulk-group" data-bfg-hs-bulk-group hidden>
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
		</span>
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
</details>
