<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<script>
</script>

<?php
?>
<div class="wrap">
	<div class="bfg-page__header">
		<h1><?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?></h1>
	</div>
	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-page__content">
			<h2> <?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?> </h2>
			<p>@TODO </p>

			<form method="post" action="options.php">
				<?php settings_fields('bring_fraktguiden_plugin_page'); ?>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Bring API offline / No connection', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					</div>

					<div class="bfg-box__section">

						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Heavy and oversized items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					</div>

					<div class="bfg-box__section">
						<?php echo $fields->exception_handling->label(); ?>
						<?php echo $fields->exception_handling; ?>

						<?php echo $fields->exception_flat_rate_label->label(); ?>
						<?php echo $fields->exception_flat_rate_label; ?>

						<?php echo $fields->exception_flat_rate->label(); ?>
						<?php echo $fields->exception_flat_rate; ?>

						<?php echo $fields->exception_rate_id->label(); ?>
						<?php echo $fields->exception_rate_id; ?>
						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Dimension packing product quantity threshold', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('Processing options for large quantity of products in the cart. The plugin uses an algorithm to pack items in the cart into an imaginary box and the dimension of this box is used for getting services and prices from the API.', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<div class="bfg-box__section">
						<?php echo $fields->max_products->label(); ?>
						<?php echo $fields->max_products; ?>


						<?php echo $fields->alt_handling->label(); ?>
						<?php echo $fields->alt_handling; ?>

						<?php echo $fields->alt_flat_rate_id->label(); ?>
						<?php echo $fields->alt_flat_rate_id; ?>

						<?php echo $fields->alt_flat_rate->label(); ?>
						<?php echo $fields->alt_flat_rate; ?>

						<?php echo $fields->alt_flat_rate_label->label(); ?>
						<?php echo $fields->alt_flat_rate_label; ?>


						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Pricing options', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('Settings that affect the shipping rates, price estimation and service availability', 'bring-fraktguiden-for-woocommerce'); ?></p>
						<?php do_settings_sections('bring_fraktguiden_plugin_page'); ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
