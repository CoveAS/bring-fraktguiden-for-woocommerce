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
			<p>

			</p>


			<form method="post" action="options.php">
				<?php settings_fields('bring_fraktguiden_settings'); ?>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Display options', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					</div>


					<div class="bfg-box__section">
						<div class="bfg-field">
							<?php echo $fields->language->label(); ?>
							<?php echo $fields->language; ?>
						</div>
						<div class="bfg-field">
							<?php echo $fields->service_sorting->label(); ?>
							<?php echo $fields->service_sorting; ?>
						</div>
						<div class="bfg-field">
							<?php echo $fields->shipping_options_full_width; ?>
						</div>
						<div class="bfg-field">
							<?php echo $fields->display_desc; ?>
						</div>


						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Pricing options', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('Settings that affect the shipping rates, price estimation and service availability', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>
					<div class="bfg-box__section">
						<div class="bfg-field">
							<?php echo $fields->price_to_use->label(); ?>
							<?php echo $fields->price_to_use; ?>
						</div>
						<div class="bfg-field">
							<label
								for="from_zip"><?php esc_html_e('Shipping location', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<div class="bfg-flex bfg-gap-8">
								<div>
									<?php echo $fields->from_zip->label() ?>
									<?php echo $fields->from_zip->field(); ?>
								</div>
								<div>
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
							<?php echo $fields->handling_fee->label(); ?>
							<div class="bfg-input bfg-input--number">
								<?php echo $fields->handling_fee->field(); ?>
								<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
							</div>
							<?php echo $fields->handling_fee->description(); ?>

						</div>
						<div class="bfg-field">
							<?php echo $fields->post_office; ?>
						</div>

						<div class="bfg-field">
						<?php echo $fields->use_customer_number_to_get_prices; ?>
						</div>
						<div class="bfg-field">
							<?php echo $fields->calculate_by_weight; ?>
						</div>

						<div class="bfg-field">
							<label for="minimum_length"><?php esc_html_e('Minimum package dimensions', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<div class="bfg-flex bfg-gap-8">
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_length->label(); ?>
									<div>
										<?php echo $fields->minimum_length->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_width->label(); ?>
									<div>
										<?php echo $fields->minimum_width->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_height->label(); ?>
									<div>
										<?php echo $fields->minimum_height->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->minimum_weight->label(); ?>
									<div>
										<?php echo $fields->minimum_weight->field(); ?>
										<span class="bfg-suffix">kg</span>
									</div>
								</div>
							</div>
							<p class="bfg-description"><?php esc_html_e('Some services add an extra fee for small items. Here you can customize the smallest dimension of packages you send. If the packaged size of the items in the cart is below this threshold then the plugin will round up the dimensions to avoid this fee.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>

						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Lead time', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('TODO: Lead time description', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>
					<div class="bfg-box__section">

						<div class="bfg-field">
							<?php echo $fields->lead_time->label(); ?>
							<?php echo $fields->lead_time; ?>
						</div>
						<div class="bfg-field">
							<?php echo $fields->lead_time_cutoff->label(); ?>
							<?php echo $fields->lead_time_cutoff; ?>
						</div>

						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Advanced settings', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					</div>
					<div class="bfg-box__section">
						<div class="bfg-field">
							<?php echo $fields->debug; ?>
						</div>
						<div class="bfg-field">
						<?php echo $fields->disable_stylesheet; ?>
						</div>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

