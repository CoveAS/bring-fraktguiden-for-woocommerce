<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Admin\SettingsPage;
use BringFraktguiden\Admin\Step;

/**
 * @var array $steps
 * @var int $stepCount
 * @var int $stepsCompleted
 * @var ?Step $nextStep
 */
?>

<script>
</script>
<style>
.bfg-admin-page__home {
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
	color: #1d2327;
	margin-top: 20px;
}
.bfg-admin-page__home .bfg-page__main {
	max-width: 900px;
	margin: 0 auto;
	padding-bottom: 60px;
}
.bfg-notice-banner {
	background: #FFF7ED; /* orange-50 */
	border: 1px solid #FED7AA; /* orange-200 */
	border-radius: 8px;
	padding: 12px 16px;
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 24px;
}
.bfg-notice-banner p {
	margin: 0 !important;
	color: #9A3412; /* orange-800 */
	font-size: 14px;
	font-weight: 500;
}
.bfg-page__main-card {
	background: #fff;
	border: 1px solid #E5E7EB;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	padding: 40px;
}
.bfg-page__header-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 24px;
}
.bfg-page__title {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin: 0 !important;
}
.bfg-progress-badge {
	background: #F3F4F6;
	border-radius: 6px;
	padding: 4px 12px;
	font-size: 13px;
	font-weight: 600;
	color: #374151;
}
.bfg-progress-container {
	margin-bottom: 40px;
}
.bfg-progress-bar-new {
	height: 8px;
	background: #E5E7EB;
	border-radius: 4px;
	overflow: hidden;
}
.bfg-progress-bar-fill {
	height: 100%;
	background: #111827;
	border-radius: 4px;
	transition: width 0.3s ease;
}
.bfg-active-step-card {
	background: #EFF6FF;
	border: 1px solid #DBEAFE;
	border-radius: 12px;
	padding: 32px;
	display: flex;
	gap: 24px;
	margin-bottom: 40px;
}
.bfg-active-step__icon {
	flex-shrink: 0;
	width: 48px;
	height: 48px;
	background: #2563EB;
	color: #fff;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 20px;
	font-weight: 700;
}
.bfg-active-step__content h3 {
	font-size: 20px;
	font-weight: 700;
	margin: 0 0 8px !important;
	color: #111827;
}
.bfg-active-step__content p {
	font-size: 16px;
	margin: 0 0 20px !important;
	color: #4B5563;
	line-height: 1.5;
}
.bfg-button-primary {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	background: #2563EB;
	color: #fff !important;
	text-decoration: none;
	padding: 10px 20px;
	border-radius: 8px;
	font-weight: 600;
	font-size: 14px;
	transition: background 0.2s;
}
.bfg-button-primary:hover {
	background: #1D4ED8;
}
.bfg-steps-list {
	display: flex;
	flex-direction: column;
	gap: 12px;
	margin-bottom: 48px;
}
.bfg-step-row {
	display: flex;
	align-items: center;
	gap: 16px;
	padding: 16px 20px;
	background: #fff;
	border: 1px solid #E5E7EB;
	border-radius: 10px;
}
.bfg-step--in-progress {
	border-color: #BFDBFE;
	background: #F8FAFC;
}
.bfg-step-row__indicator {
	flex-shrink: 0;
	width: 32px;
	height: 32px;
}
.bfg-step-row__number {
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: #F3F4F6;
	color: #6B7280;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 14px;
	font-weight: 600;
}
.bfg-step--in-progress .bfg-step-row__number {
	background: #DBEAFE;
	color: #2563EB;
}
.bfg-step-row__content {
	flex-grow: 1;
}
.bfg-step-row__label {
	font-size: 15px;
	font-weight: 600;
	color: #111827;
}
.bfg-step-row__description {
	font-size: 13px;
	color: #6B7280;
}
.bfg-badge {
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	padding: 4px 8px;
	border-radius: 4px;
}
.bfg-badge--completed {
	color: #166534;
	border: 1px solid #166534;
}
.bfg-badge--in-progress {
	background: #2563EB;
	color: #fff;
}
.bfg-pro-features-teaser {
	background: #F9FAFB;
	border: 1px solid #E5E7EB;
	border-radius: 12px;
	padding: 32px;
}
.bfg-pro-list {
	list-style: disc !important;
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
	margin: 20px 0 !important;
	padding-left: 20px !important;
}
.bfg-field-group {
	margin-bottom: 24px;
	display: flex;
	flex-direction: column;
	gap: 16px;
}
.bfg-input label {
	display: flex;
	align-items: center;
	gap: 8px;
	font-weight: 600;
}
/* Hide old standard notices on this page */
.bfg-admin-page__home .notice,
.bfg-admin-page__home .updated,
.bfg-admin-page__home .error {
	display: none !important;
}
</style>

<div class="wrap bfg-admin-page__home">
	<div class="bfg-page__header">
		<h1><?php esc_html_e('Home', 'bring-fraktguiden-for-woocommerce'); ?></h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php if (true): // Example notice, could be tied to a setting ?>
			<div class="bfg-notice-banner">
				<span class="bfg-notice-icon">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="#EA580C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</span>
				<p><?php esc_html_e('This site is running in a local environment and pretender settings has been deactivated.', 'bring-fraktguiden-for-woocommerce'); ?></p>
			</div>
		<?php endif; ?>

		<div class="bfg-page__main-card">
			<div class="bfg-page__header-row">
				<h2 class="bfg-page__title"><?php esc_html_e('Get started with Bring shipping', 'bring-fraktguiden-for-woocommerce'); ?></h2>
				<div class="bfg-progress-badge">
					<?php printf(__('Step %d of %d', 'bring-fraktguiden-for-woocommerce'), $stepsCompleted + 1, $stepCount); ?>
				</div>
			</div>

			<div class="bfg-progress-container">
				<div class="bfg-progress-bar-new">
					<div class="bfg-progress-bar-fill" style="width: <?php echo ($stepsCompleted / $stepCount) * 100; ?>%;"></div>
				</div>
			</div>

			<?php if ($nextStep): ?>
				<div class="bfg-active-step-card">
					<div class="bfg-active-step__icon">
						<?php echo $stepsCompleted + 1; ?>
					</div>
					<div class="bfg-active-step__content">
						<h3><?php echo esc_html($nextStep->label); ?></h3>
						<p><?php echo esc_html($nextStep->description); ?></p>
						<a class="bfg-button-primary" href="<?php echo esc_attr($nextStep->action); ?>">
							<?php echo esc_html($nextStep->actionText); ?>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M6 12L10 8L6 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
					</div>
				</div>
			<?php endif; ?>

			<div class="bfg-steps-list">
				<?php foreach ($steps as $i => $step): ?>
					<?php
					$isNext = $nextStep === $step;
					$statusClass = '';
					if ($step->completed) {
						$statusClass = 'bfg-step--completed';
					} elseif ($isNext) {
						$statusClass = 'bfg-step--in-progress';
					} else {
						$statusClass = 'bfg-step--pending';
					}
					?>
					<div class="bfg-step-row <?php echo $statusClass; ?>">
						<div class="bfg-step-row__indicator">
							<?php if ($step->completed): ?>
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect width="20" height="20" rx="10" fill="#DCFCE7"/>
									<path d="M6 10L9 13L14 7" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php else: ?>
								<div class="bfg-step-row__number"><?php echo $i + 1; ?></div>
							<?php endif; ?>
						</div>
						<div class="bfg-step-row__content">
							<div class="bfg-step-row__label"><?php echo esc_html($step->label) ?></div>
							<div class="bfg-step-row__description"><?php echo esc_html($step->description) ?></div>
						</div>
						<div class="bfg-step-row__status">
							<?php if ($step->completed): ?>
								<span class="bfg-badge bfg-badge--completed"><?php esc_html_e('Completed', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<?php elseif ($isNext) : ?>
								<span class="bfg-badge bfg-badge--in-progress"><?php esc_html_e('In Progress', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="bfg-pro-features-teaser">
				<form method="post" action="options.php">
					<?php settings_fields('bring_fraktguiden_home'); ?>
					<div class="bfg-pro-header">
						<h3><?php esc_html_e('Do more with our PRO features', 'bring-fraktguiden-for-woocommerce'); ?></h3>
					</div>
					<ul class="bfg-pro-list">
						<li><?php esc_html_e('Book orders directly with mybring.com from within WooCommerce', 'bring-fraktguiden-for-woocommerce'); ?><sup>1</sup></li>
						<li><?php esc_html_e('Set a free shipping threshold per service', 'bring-fraktguiden-for-woocommerce'); ?></li>
						<li><?php esc_html_e('Option to set a fixed price per service', 'bring-fraktguiden-for-woocommerce'); ?></li>
						<li><?php esc_html_e('Customize the name of the shipping rates', 'bring-fraktguiden-for-woocommerce'); ?></li>
						<li><?php esc_html_e('Enable pick-up-points for supported services', 'bring-fraktguiden-for-woocommerce'); ?><sup>2</sup></li>
						<li><?php esc_html_e('Get prioritised support', 'bring-fraktguiden-for-woocommerce'); ?></li>
					</ul>

					<div class="bfg-pro-actions">
						<div class="bfg-field-group">
							<?php BringFraktguiden\Admin\FieldRenderer::pro_enabled(); ?>
							<?php BringFraktguiden\Admin\FieldRenderer::test_url(); ?>
						</div>
						<p class="bfg-pro-note"><?php esc_html_e('Using our PRO features requires a license. For test and development sites you can activate a development mode where you can test the PRO features without requiring a license.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						<?php submit_button(__('Save changes', 'bring-fraktguiden-for-woocommerce')); ?>
					</div>
				</form>
			</div>

			<div class="bfg-page__footer-notes">
				<small><sup>1</sup> <?php esc_html_e('Domestic shipments only. We\'re working on building support for international shipping.', 'bring-fraktguiden-for-woocommerce'); ?></small>
				<small><sup>2</sup> <?php esc_html_e('List of currently supported services for using pickup point: Pickup parcel (5800), Pakke til Pakkeboks (5801), Express next day (4850), Business parcel (5000), Norgespakke (3067), PICKUP_PARCEL and PICKUP_PARCEL_BULK', 'bring-fraktguiden-for-woocommerce'); ?></small>
			</div>
		</div>
	</div>
</div>

