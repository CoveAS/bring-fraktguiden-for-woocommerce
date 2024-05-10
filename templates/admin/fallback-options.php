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
			<h2><?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?> </h2>
			<p>@TODO </p>

			<form method="post" action="options.php">
				<?php settings_fields('bring_fraktguiden_fallback'); ?>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('No connection', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('In some rare cases the plugin will be unable to make connection with the API. This could be caused by a myriad of reasons, but the most common is that either the bring API has some temporary down time or that there is some problem with the network connection. We recommend configuring a fallback shipping option that your customers can use when the API is unavailable.', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<div class="bfg-box__section">

						<div class="bfg-field">
							<?php echo $fields->no_connection_rate_id->label(); ?>
							<?php echo $fields->no_connection_rate_id; ?>
						</div>

						<div class="bfg-field">
						<?php echo $fields->no_connection_flat_rate->label(); ?>
						<div class="bfg-input bfg-input--number">
							<?php echo $fields->no_connection_flat_rate->field(); ?>
							<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
						</div>
						</div>

						<div class="bfg-field">
							<?php echo $fields->no_connection_flat_rate_label->label(); ?>
							<?php echo $fields->no_connection_flat_rate_label; ?>
						</div>

					</div>
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Heavy and oversized items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('A heavily loaded cart may exceed your selected service\'s size or weight limits. Set up a fallback option here to handle instances where shipping rates are not available due to excess weight or size.', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<div class="bfg-box__section">


						<div class="bfg-field">
						<?php echo $fields->exception_rate_id->label(); ?>
						<?php echo $fields->exception_rate_id; ?>

						</div>
						<div class="bfg-field">
						<?php echo $fields->exception_flat_rate->label(); ?>
						<div class="bfg-input bfg-input--number">
							<?php echo $fields->exception_flat_rate->field(); ?>
							<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
						</div>
						<?php echo $fields->exception_flat_rate->description(); ?>
						</div>
						<div class="bfg-field">

						<?php echo $fields->exception_flat_rate_label->label(); ?>
						<?php echo $fields->exception_flat_rate_label; ?>
						</div>

						<?php submit_button(); ?>
					</div>
				</div>
				<div class="bfg-box">
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Dimension packing for cart items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<p><?php esc_html_e('Packing options for cart items. The plugin uses an algorithm to pack items in the cart into an imaginary box. The dimension is then sent to the bring.com API and services and prices are returned.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						<p><?php esc_html_e(
							'
							', 'bring-fraktguiden-for-woocommerce'
						); ?></p>
					</div>


					<div class="bfg-box__section">

						<div class="bfg-field">
							<?php echo $fields->enable_multipack; ?>
						</div>

						<div class="bfg-field">
							<div class="bfg-flex bfg-gap-8">
								<div class="">
									<?php echo $fields->dimension_packing_side->label(); ?>
									<div class="bfg-input bfg-input--number">
										<?php echo $fields->dimension_packing_side->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="">
									<?php echo $fields->dimension_packing_circumference->label(); ?>
									<div class="bfg-input bfg-input--number">
										<?php echo $fields->dimension_packing_circumference->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->dimension_packing_weight->label(); ?>
									<div class="">
										<?php echo $fields->dimension_packing_weight->field(); ?>
										<span class="bfg-suffix">kg</span>
									</div>
								</div>
							</div>
						</div>

						<div class="bfg-field">
							<?php echo $fields->calculate_by_weight->label(); ?>
							<?php echo $fields->calculate_by_weight; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->max_products->label(); ?>
							<?php echo $fields->max_products; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->alt_flat_rate_id->label(); ?>
							<?php echo $fields->alt_flat_rate_id; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->alt_flat_rate->label(); ?>
							<div class="bfg-input bfg-input--number">
								<?php echo $fields->alt_flat_rate->field(); ?>
								<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
							</div>
							<?php echo $fields->alt_flat_rate->description(); ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->alt_flat_rate_label->label(); ?>
							<?php echo $fields->alt_flat_rate_label; ?>
						</div>


						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
