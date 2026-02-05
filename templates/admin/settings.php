<?php

use BringFraktguiden\Admin\Component;
use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__settings">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?></h1>
		</div>
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields('bring_fraktguiden_settings'); ?>
			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Display Options', 'bring-fraktguiden-for-woocommerce'),
					__('Customize how shipping options appear to customers', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<?php echo $fields->language->label(); ?>
						<?php echo $fields->language; ?>
					</div>
					<div class="bfg-field">
						<?php echo $fields->service_sorting->label(); ?>
						<?php echo $fields->service_sorting; ?>
					</div>
					<?php echo Component::checkboxBox($fields->shipping_options_full_width); ?>
					<?php echo Component::checkboxBox($fields->display_desc); ?>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Pricing Options', 'bring-fraktguiden-for-woocommerce'),
					__('Settings that affect the shipping rates, price estimation and service availability', 'bring-fraktguiden-for-woocommerce')
				); ?>
				<div class="bfg-box__section">
					<div class="bfg-field">
						<label class="bfg-field-group-title"><?php esc_html_e('Shipping location', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<div class="bfgu-flex bfgu-flex-row bfgu-gap-4">
							<div class="bfgu-flex-1">
								<?php echo $fields->from_zip->label() ?>
								<?php echo $fields->from_zip->field(); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->from_country->label() ?>
								<?php echo $fields->from_country->field(); ?>
							</div>
						</div>
						<p class="bfg-description">
							<?php echo strtr(
								esc_html__('Required if you are sending from a different address than the {{a}}store address{{/a}}. Eg. you\'re shipping from a warehouse.', 'bring-fraktguiden-for-woocommerce'),
								[
									'{{a}}' => sprintf('<a href="%s">', admin_url('admin.php?page=wc-settings')),
									'{{/a}}' => '</a>',
								],
							); ?>
						</p>
					</div>

					<div class="bfg-field">
						<?php echo $fields->price_to_use->label(); ?>
						<?php echo $fields->price_to_use; ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->handling_fee->label(); ?>
						<?php echo Component::inputWithSuffix($fields->handling_fee, $currency, 'lg'); ?>
						<?php echo $fields->handling_fee->description(); ?>
					</div>

					<?php echo Component::checkboxBox($fields->post_office); ?>
					<?php echo Component::checkboxBox($fields->use_customer_number_to_get_prices); ?>
					<?php echo Component::checkboxBox($fields->calculate_by_weight); ?>

					<div class="bfg-field bfgu-mt-8">
						<label class="bfg-field-group-title"><?php esc_html_e('Minimum package dimensions', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<div class="bfgu-flex bfgu-flex-row bfgu-gap-4 bfgu-mb-5">
							<div class="bfgu-flex-1">
								<?php echo $fields->minimum_length->label(); ?>
								<?php echo Component::inputWithSuffix($fields->minimum_length, 'cm'); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->minimum_width->label(); ?>
								<?php echo Component::inputWithSuffix($fields->minimum_width, 'cm'); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->minimum_height->label(); ?>
								<?php echo Component::inputWithSuffix($fields->minimum_height, 'cm'); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->minimum_weight->label(); ?>
								<?php echo Component::inputWithSuffix($fields->minimum_weight, 'kg'); ?>
							</div>
						</div>
						<p class="bfg-description"><?php esc_html_e('Some services add an extra fee for small items. Here you can customize the smallest dimension of packages you send. If the packaged size of the items in the cart is below this threshold then the plugin will round up the dimensions to avoid this fee.', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Lead Time', 'bring-fraktguiden-for-woocommerce'),
					__('Configure lead time and cutoff settings', 'bring-fraktguiden-for-woocommerce')
				); ?>
				<div class="bfg-box__section">
					<div class="bfg-field">
						<div class="bfgu-flex bfgu-flex-row bfgu-gap-4">
							<div class="bfgu-flex-1">
								<?php echo $fields->lead_time->label(); ?>
								<?php echo Component::inputWithSuffix($fields->lead_time, __('days', 'bring-fraktguiden-for-woocommerce')); ?>
								<?php echo $fields->lead_time->description(); ?>
							</div>
							<div class="bfgu-flex-1">
								<?php echo $fields->lead_time_cutoff->label(); ?>
								<?php echo $fields->lead_time_cutoff->field(); ?>
								<?php echo $fields->lead_time_cutoff->description(); ?>
							</div>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Advanced Settings', 'bring-fraktguiden-for-woocommerce')
				); ?>
				<div class="bfg-box__section">
					<?php echo Component::checkboxBox($fields->debug); ?>
					<?php echo Component::checkboxBox($fields->disable_stylesheet); ?>
					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>
		</form>
	</div>
</div>
