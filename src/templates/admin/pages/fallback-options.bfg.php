<?php
/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__fallback">
	<div class="bfg-page__header">
		<?php if (!empty($_GET['ref']) && $_GET['ref'] === 'bring_fraktguiden_pro'): ?>
			<div class="bfg-page__header-with-back">
				<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_pro')); ?>" class="bfg-back-link">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="19" y1="12" x2="5" y2="12"></line>
						<polyline points="12 19 5 12 12 5"></polyline>
					</svg>
					<?php esc_html_e('Back to Pro', 'bring-fraktguiden-for-woocommerce'); ?>
				</a>
				<h1><?php esc_html_e('Fallback Options', 'bring-fraktguiden-for-woocommerce'); ?></h1>
			</div>
		<?php else: ?>
			<h1><?php esc_html_e('Fallback Options', 'bring-fraktguiden-for-woocommerce'); ?></h1>
		<?php endif; ?>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php if (defined('BRING_ENVIRONMENT') && BRING_ENVIRONMENT === 'local'): ?>
			<bfg-notice type="warning">
				<?php esc_html_e('This site is running in a local environment and production settings has been deactivated.', 'bring-fraktguiden-for-woocommerce'); ?>
			</bfg-notice>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields('bring_fraktguiden_fallback'); ?>

			<div class="bfg-section">
				<div class="bfg-section__header">
					<h2><?php esc_html_e('No connection', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('When the Bring API is unavailable, no shipping options appear at checkout and customers can\'t complete their order. Add a fallback rate here to prevent lost sales.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-section__section">
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
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->no_connection_flat_rate->field(); ?>
									<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
								</div>
							</div>
						</div>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</div>

				<div class="bfg-section__header bfg-section__header--divider">
					<h2><?php esc_html_e('Heavy and oversized items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Orders that exceed Bring\'s weight or size limits won\'t get a shipping rate. Add a fallback option for these cases.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-section__section">
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
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->exception_flat_rate->field(); ?>
									<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
								</div>
								<?php echo $fields->exception_flat_rate->description(); ?>
							</div>
						</div>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</div>
			</div>

			<div class="bfg-section">
				<div class="bfg-section__header">
					<h2><?php esc_html_e('Dimension packing for cart items', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Configure how cart items are packed into boxes before calculating shipping rates. Useful for stores with large or heavy products.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-section__section">
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->enable_multipack; ?></div>

					<bfg-conditional-field-group id="dimension-fields" trigger="enable_multipack">
						<div class="bfg-field">
							<h3 class="bfg-field-group-title"><?php esc_html_e('Maximum box dimensions', 'bring-fraktguiden-for-woocommerce'); ?></h3>
							<div class="bfgu:flex bfgu:flex-row bfgu:gap-4 bfgu:mb-5">
								<div class="bfgu:flex-1">
									<?php echo $fields->dimension_packing_side->label(); ?>
									<div class="bfg-input bfg-input--number">
										<?php echo $fields->dimension_packing_side->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="bfgu:flex-1">
									<?php echo $fields->dimension_packing_circumference->label(); ?>
									<div class="bfg-input bfg-input--number">
										<?php echo $fields->dimension_packing_circumference->field(); ?>
										<span class="bfg-suffix">cm</span>
									</div>
								</div>
								<div class="bfgu:flex-1">
									<?php echo $fields->dimension_packing_weight->label(); ?>
									<div class="bfg-input bfg-input--number">
										<?php echo $fields->dimension_packing_weight->field(); ?>
										<span class="bfg-suffix">kg</span>
									</div>
								</div>
							</div>
						</div>
					</bfg-conditional-field-group>

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
								<div class="bfg-input bfg-input--number">
									<?php echo $fields->alt_flat_rate->field(); ?>
									<span class="bfg-suffix-lg"><?php echo esc_html($currency); ?></span>
								</div>
								<?php echo $fields->alt_flat_rate->description(); ?>
							</div>
						</div>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
