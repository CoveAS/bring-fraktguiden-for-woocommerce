<?php

use BringFraktguiden\Admin\ShippingTest;

/**
 * The shipping test block. The setup page and the product screen both show it.
 *
 * A product id of 0 tests a sample parcel instead of a saved product.
 *
 * @var int $product_id
 */

$bfg_countries = WC()->countries->get_shipping_countries();
$bfg_country = WC()->countries->get_base_country();
$bfg_postcode = WC()->countries->get_base_postcode();
?>

<div class="bfg bfg-shipping-test"
	data-product="<?php echo (int) $product_id; ?>"
	data-nonce="<?php echo esc_attr(wp_create_nonce(ShippingTest::ACTION)); ?>"
	data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
	data-busy="<?php esc_attr_e('Asking Bring for a price.', 'bring-fraktguiden-for-woocommerce'); ?>"
	data-failed="<?php esc_attr_e('The test could not run. Reload the page and try again.', 'bring-fraktguiden-for-woocommerce'); ?>">

	<div class="bfg-shipping-test__form">
		<div class="bfg-shipping-test__field">
			<label class="bfg-step-form__label" for="bfg-shipping-test-postcode"><t>Post code</t></label>
			<input class="bfg-step-form__input bfg-shipping-test__postcode" type="text"
				id="bfg-shipping-test-postcode" value="<?php echo esc_attr($bfg_postcode); ?>">
		</div>

		<div class="bfg-shipping-test__field">
			<label class="bfg-step-form__label" for="bfg-shipping-test-country"><t>Country</t></label>
			<select class="bfg-shipping-test__country" id="bfg-shipping-test-country">
				<?php foreach ($bfg_countries as $bfg_code => $bfg_name): ?>
					<option value="<?php echo esc_attr($bfg_code); ?>" <?php selected($bfg_country, $bfg_code); ?>>
						<?php echo esc_html($bfg_name); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<button type="button" class="bfg-btn bfg-btn--primary bfg-shipping-test__run">
			<t>Test shipping</t>
		</button>
	</div>

	<div class="bfg-shipping-test__result" role="status" aria-live="polite"></div>
</div>
