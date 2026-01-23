<?php

use BringFraktguiden\Admin\Component;
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
<div class="wrap bfg-admin-page bfg-admin-page__fallback">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><?php esc_html_e('Fallback Options', 'bring-fraktguiden-for-woocommerce'); ?></h1>
		</div>
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php if (defined('BRING_ENVIRONMENT') && BRING_ENVIRONMENT === 'local'): ?>
			<?php echo Component::noticeBanner(
				__('This site is running in a local environment and production settings has been deactivated.', 'bring-fraktguiden-for-woocommerce'),
				'warning'
			); ?>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields('bring_fraktguiden_fallback'); ?>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('No connection', 'bring-fraktguiden-for-woocommerce'),
					__('In some rare cases the plugin will be unable to make connection with the API. This could be caused by a myriad of reasons, but the most common is that either the bring API has some temporary down time or that there is some problem with the network connection. We recommend configuring a fallback shipping option that your customers can use when the API is unavailable.', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<?php echo $fields->no_connection_rate_id->label(); ?>
						<?php echo $fields->no_connection_rate_id; ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->no_connection_flat_rate->label(); ?>
						<?php echo Component::inputWithSuffix($fields->no_connection_flat_rate, $currency, 'lg'); ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->no_connection_flat_rate_label->label(); ?>
						<?php echo $fields->no_connection_flat_rate_label; ?>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>

				<?php echo Component::boxHeader(
					__('Heavy and oversized items', 'bring-fraktguiden-for-woocommerce'),
					__('A heavily loaded cart may exceed your selected service\'s size or weight limits. Set up a fallback option here to handle instances where shipping rates are not available due to excess weight or size.', 'bring-fraktguiden-for-woocommerce'),
					true
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<?php echo $fields->exception_rate_id->label(); ?>
						<?php echo $fields->exception_rate_id; ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->exception_flat_rate->label(); ?>
						<?php echo Component::inputWithSuffix($fields->exception_flat_rate, $currency, 'lg'); ?>
						<?php echo $fields->exception_flat_rate->description(); ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->exception_flat_rate_label->label(); ?>
						<?php echo $fields->exception_flat_rate_label; ?>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Dimension packing for cart items', 'bring-fraktguiden-for-woocommerce'),
					__('Packing options for cart items. The plugin uses an algorithm to pack items in the cart into an imaginary box. The dimension is then sent to the bring.com API and services and prices are returned.', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<?php echo Component::checkboxBox($fields->enable_multipack); ?>

					<div class="bfg-field">
						<label class="bfg-field-group-title"><?php esc_html_e('Maximum box dimensions', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<div class="bfgu-flex bfgu-flex-row bfgu-gap-4 bfgu-mb-5">
							<div class="bfgu-flex-1">
								<?php echo $fields->dimension_packing_side->label(); ?>
								<?php echo Component::inputWithSuffix($fields->dimension_packing_side, 'cm'); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->dimension_packing_circumference->label(); ?>
								<?php echo Component::inputWithSuffix($fields->dimension_packing_circumference, 'cm'); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->dimension_packing_weight->label(); ?>
								<?php echo Component::inputWithSuffix($fields->dimension_packing_weight, 'kg'); ?>
							</div>
						</div>
					</div>

					<?php echo Component::checkboxBox($fields->calculate_by_weight); ?>

					<div class="bfg-field">
						<?php echo $fields->max_products->label(); ?>
						<div class="bfg-input bfg-input--number">
							<?php echo $fields->max_products->field(); ?>
						</div>
					</div>

					<div class="bfg-field">
						<?php echo $fields->alt_flat_rate_id->label(); ?>
						<?php echo $fields->alt_flat_rate_id; ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->alt_flat_rate->label(); ?>
						<?php echo Component::inputWithSuffix($fields->alt_flat_rate, $currency, 'lg'); ?>
						<?php echo $fields->alt_flat_rate->description(); ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->alt_flat_rate_label->label(); ?>
						<?php echo $fields->alt_flat_rate_label; ?>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>
		</form>
	</div>
</div>
