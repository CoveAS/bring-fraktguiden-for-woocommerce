<?php
/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__settings">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><t>Settings</t></h1>
		</div>
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields('bring_fraktguiden_settings'); ?>
			<div class="bfg-section">
				<div class="bfg-section__header">
					<h2><t>Display Options</t></h2>
					<p><t>Customize how shipping options appear to customers</t></p>
				</div>

				<div class="bfg-section__section">
					<div class="bfg-field">
						<?php echo $fields->language->label(); ?>
						<?php echo $fields->language; ?>
					</div>
					<div class="bfg-field">
						<?php echo $fields->service_sorting->label(); ?>
						<?php echo $fields->service_sorting; ?>
					</div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->shipping_options_full_width; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->display_desc; ?></div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-section">
				<div class="bfg-section__header">
					<h2><t>Pricing Options</t></h2>
					<p><t>Settings that affect the shipping rates, price estimation and service availability</t></p>
				</div>
				<div class="bfg-section__section">
					<div class="bfg-field">
						<h3 class="bfg-field-group-title"><t>Shipping location</t></h3>
						<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
							<div class="bfgu:flex-1">
								<?php echo $fields->from_zip->label() ?>
								<?php echo $fields->from_zip->field(); ?>
							</div>
							<div class="bfgu:flex-1">
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
						<div class="bfg-input bfg-input--number">
							<?php echo $fields->handling_fee->field(); ?>
							<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
						</div>
						<?php echo $fields->handling_fee->description(); ?>
					</div>

					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->post_office; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->use_customer_number_to_get_prices; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->calculate_by_weight; ?></div>

					<div class="bfg-field bfgu:mt-8">
						<h3 class="bfg-field-group-title"><t>Minimum package dimensions</t></h3>
						<div class="bfgu:flex bfgu:flex-row bfgu:gap-4 bfgu:mb-5">
							<div class="bfgu:flex-1">
								<?php echo $fields->minimum_length->label(); ?>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_length->field(); ?>
									<span class="bfg-suffix">cm</span>
								</div>
							</div>
							<div class="bfgu:flex-1">
								<?php echo $fields->minimum_width->label(); ?>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_width->field(); ?>
									<span class="bfg-suffix">cm</span>
								</div>
							</div>
							<div class="bfgu:flex-1">
								<?php echo $fields->minimum_height->label(); ?>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_height->field(); ?>
									<span class="bfg-suffix">cm</span>
								</div>
							</div>
							<div class="bfgu:flex-1">
								<?php echo $fields->minimum_weight->label(); ?>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_weight->field(); ?>
									<span class="bfg-suffix">kg</span>
								</div>
							</div>
						</div>
						<p class="bfg-description"><t>Some Bring services charge extra for very small packages. Orders smaller than these dimensions are automatically rounded up to avoid those fees.</t></p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-section">
				<div class="bfg-section__header">
					<h2><t>Lead Time</t></h2>
					<p><t>Configure lead time and cutoff settings</t></p>
				</div>
				<div class="bfg-section__section">
					<div class="bfg-field">
						<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
							<div class="bfgu:flex-1">
								<?php echo $fields->lead_time->label(); ?>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->lead_time->field(); ?>
									<span class="bfg-suffix"><t>days</t></span>
								</div>
								<?php echo $fields->lead_time->description(); ?>
							</div>
							<div class="bfgu:flex-1">
								<?php echo $fields->lead_time_cutoff->label(); ?>
								<?php echo $fields->lead_time_cutoff->field(); ?>
								<?php echo $fields->lead_time_cutoff->description(); ?>
							</div>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-section">
				<div class="bfg-section__header">
					<h2><t>Advanced Settings</t></h2>
				</div>
				<div class="bfg-section__section">
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->debug; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->disable_stylesheet; ?></div>
					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>
		</form>
	</div>
</div>
