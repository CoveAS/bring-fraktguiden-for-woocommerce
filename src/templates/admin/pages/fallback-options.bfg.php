<?php

use BringFraktguiden\Admin\Component;
use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var string $currency
 * @var Fields $fields
 */
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
				<div class="bfg-box__header">
					<h2><?php esc_html_e('No connection', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('When the Bring API is unavailable, no shipping options appear at checkout and customers can\'t complete their order. Add a fallback rate here to prevent lost sales.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<?php echo $fields->no_connection_rate_id->label(); ?>
						<?php echo $fields->no_connection_rate_id; ?>
					</div>

					<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
						<div class="bfgu:flex-1">
							<div class="bfg-field">
								<?php echo $fields->no_connection_flat_rate_label->label(); ?>
								<?php echo $fields->no_connection_flat_rate_label; ?>
							</div>
						</div>
						<div class="bfgu:flex-1">
							<div class="bfg-field">
								<?php echo $fields->no_connection_flat_rate->label(); ?>
								<?php echo Component::inputWithSuffix($fields->no_connection_flat_rate, $currency, 'lg'); ?>
							</div>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>

				<div class="bfg-box__header bfg-box__header--divider">
					<h2><?php esc_html_e('Heavy and oversized items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Orders that exceed Bring\'s weight or size limits won\'t get a shipping rate. Add a fallback option for these cases.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<?php echo $fields->exception_rate_id->label(); ?>
						<?php echo $fields->exception_rate_id; ?>
					</div>

					<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
						<div class="bfgu:flex-1">
							<div class="bfg-field">
								<?php echo $fields->exception_flat_rate_label->label(); ?>
								<?php echo $fields->exception_flat_rate_label; ?>
							</div>
						</div>
						<div class="bfgu:flex-1">
							<div class="bfg-field">
								<?php echo $fields->exception_flat_rate->label(); ?>
								<?php echo Component::inputWithSuffix($fields->exception_flat_rate, $currency, 'lg'); ?>
								<?php echo $fields->exception_flat_rate->description(); ?>
							</div>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<h2><?php esc_html_e('Dimension packing for cart items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Configure how cart items are packed into boxes before calculating shipping rates. Useful for stores with large or heavy products.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-box__section">
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->enable_multipack; ?></div>

					<?php
					ob_start();
					?>
					<div class="bfg-field">
						<h3 class="bfg-field-group-title"><?php esc_html_e('Maximum box dimensions', 'bring-fraktguiden-for-woocommerce'); ?></h3>
						<div class="bfgu:flex bfgu:flex-row bfgu:gap-4 bfgu:mb-5">
							<div class="bfgu:flex-1">
								<?php echo $fields->dimension_packing_side->label(); ?>
								<?php echo Component::inputWithSuffix($fields->dimension_packing_side, 'cm'); ?>
							</div>
							<div class="bfgu:flex-1">
								<?php echo $fields->dimension_packing_circumference->label(); ?>
								<?php echo Component::inputWithSuffix($fields->dimension_packing_circumference, 'cm'); ?>
							</div>
							<div class="bfgu:flex-1">
								<?php echo $fields->dimension_packing_weight->label(); ?>
								<?php echo Component::inputWithSuffix($fields->dimension_packing_weight, 'kg'); ?>
							</div>
						</div>
					</div>
					<?php
					$dimension_fields_content = ob_get_clean();
					echo Component::conditionalFieldGroup('dimension-fields', 'enable_multipack', $dimension_fields_content);
					?>

					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->calculate_by_weight; ?></div>

					<div class="bfg-field">
						<?php echo $fields->max_products->label(); ?>
						<div class="bfg-input bfg-input--number">
							<?php echo $fields->max_products->field(); ?>
						</div>
						<?php echo $fields->max_products->description(); ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->alt_flat_rate_id->label(); ?>
						<?php echo $fields->alt_flat_rate_id; ?>
					</div>

					<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
						<div class="bfgu:flex-1">
							<div class="bfg-field">
								<?php echo $fields->alt_flat_rate_label->label(); ?>
								<?php echo $fields->alt_flat_rate_label; ?>
							</div>
						</div>
						<div class="bfgu:flex-1">
							<div class="bfg-field">
								<?php echo $fields->alt_flat_rate->label(); ?>
								<?php echo Component::inputWithSuffix($fields->alt_flat_rate, $currency, 'lg'); ?>
								<?php echo $fields->alt_flat_rate->description(); ?>
							</div>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>
		</form>
	</div>
</div>
