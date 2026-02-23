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

<style>
.bfg-admin-page__home {
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
	color: #1d2327;
	margin-top: 20px;
}
.bfg-admin-page__home .bfg-page__main {
	max-width: 768px;
	margin: 0 auto;
	padding-bottom: 60px;
}
.bfg-page__main-card {
	background: #fff;
	border: 1px solid #E5E7EB;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	padding: 56px;
	margin-bottom: 40px;
}
.bfg-page__header-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 24px;
}
.bfg-progress-badge {
	background: #F3F4F6;
	border-radius: 6px;
	padding: 4px 12px;
	font-size: 14px;
	font-weight: 600;
	color: #374151;
	white-space: nowrap;
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
	font-size: 24px;
	font-weight: 500;
	margin: 0 0 8px !important;
	color: #111827;
	line-height: 32px;
	text-wrap: balance;
}
.bfg-active-step__content p {
	font-size: 16px;
	margin: 0 0 20px !important;
	color: #4B5563;
	line-height: 24px;
	font-weight: 400;
	text-wrap: pretty;
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
	white-space: nowrap;
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

.bfg-pro-features-teaser {
	background: #fff;
	border: 2px solid #E9D5FF;
	border-radius: 16px;
	padding: 40px;
	position: relative;
	overflow: hidden;
	margin-top: 40px;
}
.bfg-pro-features-teaser::after {
	content: "";
	position: absolute;
	top: 0;
	right: 0;
	width: 128px;
	height: 128px;
	background: linear-gradient(to bottom right, #c084fc, #60a5fa);
	opacity: 0.1;
	border-bottom-left-radius: 100%;
	z-index: 0;
}
.bfg-pro-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 32px;
	position: relative;
	z-index: 1;
}
.bfg-pro-header h3 {
	font-size: var(--bfg-text-level-1) !important;
	font-weight: 500;
	margin: 0 !important;
	color: #111827;
	line-height: 40px;
	letter-spacing: -0.02em;
	text-wrap: balance;
}
.bfg-pro-header .sparkle-icon {
	color: #9333EA;
	flex-shrink: 0;
}
.bfg-pro-list {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 16px 60px;
	margin: 0 0 40px 0 !important;
	margin-left: 0 !important;
	padding: 0 !important;
	padding-left: 0 !important;
	list-style: none !important;
	position: relative;
	z-index: 1;
}
.bfg-pro-list li {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	font-size: 14px;
	color: #374151;
	line-height: 1.4;
	list-style-type: none !important;
	padding: 0 !important;
	font-weight: 400;
	text-wrap: pretty;
}
.bfg-pro-list li::before {
	content: none !important;
}
.bfg-pro-list li svg {
	color: #9333EA;
	flex-shrink: 0;
	margin-top: 2px;
}
.bfg-pro-activation-card {
	background: #F9FAFB;
	border: 1px solid #E5E7EB;
	border-radius: 8px;
	padding: 24px;
	position: relative;
	z-index: 1;
}
.bfg-pro-activation-card .bfg-input--checkbox {
	margin-bottom: 32px;
}
.bfg-pro-activation-card .bfg-input--checkbox label {
	display: flex;
	align-items: center;
	gap: 16px;
	font-weight: 600;
	font-size: 16px;
	color: #111827;
	cursor: pointer;
	margin-bottom: 12px;
}
.bfg-pro-activation-card .bfg-input--checkbox input[type="checkbox"] {
	width: 24px !important;
	height: 24px !important;
	border-radius: 4px;
	border: 2px solid #D1D5DB;
	appearance: none;
	-webkit-appearance: none;
	position: relative;
	cursor: pointer;
	background: #fff;
	margin: 0;
	flex-shrink: 0;
}
.bfg-pro-activation-card .bfg-input--checkbox input[type="checkbox"]::before {
	content: none !important;
}
.bfg-pro-activation-card .bfg-input--checkbox input[type="checkbox"]:checked {
	background: #9333EA !important;
	border-color: #9333EA !important;
	background-image: none !important;
}
.bfg-pro-activation-card .bfg-input--checkbox input[type="checkbox"]:checked::after {
	content: "";
	position: absolute;
	top: 50%;
	left: 50%;
	width: 16px;
	height: 16px;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
	background-size: contain;
	background-repeat: no-repeat;
	transform: translate(-50%, -50%);
}
.bfg-pro-activation-card .bfg-description {
	font-size: 14px;
	color: #4B5563;
	line-height: 1.5;
	margin: 0 0 16px 0 !important;
	text-wrap: pretty;
}
.bfg-license-input-row {
	display: flex;
	flex-direction: column;
	gap: 32px;
}
.bfg-license-input-row .bfg-input {
	flex-grow: 1 !important;
	display: flex !important;
}
.bfg-license-input-row input[type="text"],
.bfg-license-input-row input[type="url"] {
	width: 100% !important;
	max-width: none !important;
	background: #ffffff !important;
	border: 1px solid #E5E7EB !important;
	border-radius: 8px;
	padding: 10px 16px;
	font-size: 14px;
	box-shadow: none !important;
	transition: border-color 0.2s;
	margin: 0 !important;
}
.bfg-license-input-row input:focus {
	border-color: #9333EA !important;
	outline: none !important;
	box-shadow: none !important;
}
body:not(.using-mouse) .bfg-license-input-row input:focus {
	box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.3) !important;
}
/* Test URL and license input focus - blue border */
.bfg-pro-content-block__test-url input:focus,
.bfg-license-input-wrapper input:focus,
#bfg-tab-trial input[name="test_url"]:focus,
#bfg-tab-license input[name="test_url"]:focus {
	border-color: #2563EB !important;
	outline: none !important;
	box-shadow: none !important;
}
/* Keyboard focus - add ring */
body:not(.using-mouse) .bfg-pro-content-block__test-url input:focus,
body:not(.using-mouse) .bfg-license-input-wrapper input:focus,
body:not(.using-mouse) #bfg-tab-trial input[name="test_url"]:focus,
body:not(.using-mouse) #bfg-tab-license input[name="test_url"]:focus {
	box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3) !important;
}
.bfg-pro-activation-card .submit-button {
	margin: 0 !important;
}
.bfg-pro-activation-card p.submit {
	padding: 0 !important;
	margin: 0 !important;
}
.bfg-pro-activation-card .button-primary {
	background: #9333EA !important;
	border: none !important;
	border-radius: 8px !important;
	padding: 0 24px !important;
	height: 44px !important;
	line-height: 44px !important;
	font-weight: 600 !important;
	font-size: 14px !important;
	box-shadow: none !important;
	text-shadow: none !important;
}
.bfg-pro-activation-card .button-primary:hover {
	background: #7E22CE !important;
}
.bfg-page__footer-notes {
	margin-top: 32px;
	display: flex;
	flex-direction: column;
	gap: 12px;
	padding-top: 24px;
	border-top: 1px solid #F3F4F6;
}
.bfg-page__footer-notes small {
	display: block;
	font-size: 14px;
	color: #6B7280;
	line-height: 1.5;
	font-weight: 400;
	text-wrap: pretty;
}
/* Hide WordPress notices */
.bfg-admin-page__home .notice,
.bfg-admin-page__home .updated,
.bfg-admin-page__home .error {
	display: none !important;
}
/* Scoped overrides for Home page elements */
.bfg-admin-page__home .bfg-step-row__number {
	background: #E5E7EB;
	color: #111827;
}
.bfg-admin-page__home .bfg-step--in-progress .bfg-step-row__number,
.bfg-admin-page__home .bfg-step--in-progress:hover .bfg-step-row__number {
	background: #2563EB;
	color: #fff;
}
.bfg-admin-page__home .bfg-pro-tabs {
	background: #EBEEF2;
}
.bfg-admin-page__home .bfg-pro-tab:hover:not(.is-active) {
	background: #DDE2E8;
	color: #111827;
}
.bfg-admin-page__home .bfg-pro-tab.is-active {
	box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}
.bfg-admin-page__home .bfg-pro-content-block {
	border: none !important;
}
</style>

<div class="wrap bfg-admin-page__home">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><?php esc_html_e('Home', 'bring-fraktguiden-for-woocommerce'); ?></h1>
		</div>

		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-page__main-card">
			<?php
			// Only show "next step" highlighting if at least one step is completed
			// For fresh state (nothing completed), don't highlight any step as "in progress"
			$showNextStep = $stepsCompleted > 0 && $nextStep;
			$nextStepIndex = $showNextStep ? array_search($nextStep, $steps, true) : false;
			$currentStepNumber = $nextStepIndex !== false ? $nextStepIndex + 1 : 1;
			?>
			<div class="bfg-page__header-row">
				<h2 class="bfg-section-card-title"><?php esc_html_e('Get started with Bring shipping', 'bring-fraktguiden-for-woocommerce'); ?></h2>
				<div class="bfg-progress-badge">
					<?php printf(__('%d of %d completed', 'bring-fraktguiden-for-woocommerce'), $stepsCompleted, $stepCount); ?>
				</div>
			</div>

			<div class="bfg-progress-container">
				<div class="bfg-progress-bar-new">
					<div class="bfg-progress-bar-fill" style="width: <?php echo ($stepsCompleted / $stepCount) * 100; ?>%;"></div>
				</div>
			</div>

			<?php if ($showNextStep): ?>
				<div class="bfg-active-step-card">
					<div class="bfg-active-step__icon">
						<?php echo $currentStepNumber; ?>
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
					// Only mark as "in progress" if we're showing the next step (i.e., at least one step completed)
					$isNext = $showNextStep && $nextStep === $step;
					$statusClass = '';
					if ($step->completed) {
						$statusClass = 'bfg-step--completed';
					} elseif ($isNext) {
						$statusClass = 'bfg-step--in-progress';
					} else {
						$statusClass = 'bfg-step--pending';
					}
					?>
					<a href="<?php echo esc_attr($step->action); ?>" class="bfg-step-row <?php echo $statusClass; ?>" style="text-decoration: none; color: inherit; display: flex;">
						<div class="bfg-step-row__indicator">
							<?php if ($step->completed): ?>
								<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="16" cy="16" r="16" fill="#dcfce7"/>
									<path d="M10 16L14 20L22 12" stroke="#15803d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
					</a>
				<?php endforeach; ?>
			</div>
		</div>

			<?php
			$is_test_site = Bring_Fraktguiden\Common\Fraktguiden_Helper::is_test_site();
			$pro_valid_to = get_option('bring_fraktguiden_pro_valid_to', false);
			$license_active = $pro_valid_to && intval($pro_valid_to) > time();
			$pro_enabled = Bring_Fraktguiden\Common\Fraktguiden_Helper::get_option('pro_enabled') === 'yes';
			$pro_activated = Bring_Fraktguiden\Common\Fraktguiden_Helper::pro_activated();
			$days_remaining = Bring_Fraktguiden\Common\Fraktguiden_Helper::get_pro_days_remaining();
			$pro_activated_on = Bring_Fraktguiden\Common\Fraktguiden_Helper::get_option('pro_activated_on');
			$is_trial = $pro_enabled && $pro_activated_on && !$license_active && $days_remaining >= 0;
			$is_expired = $pro_enabled && $pro_activated_on && $days_remaining < 0 && !$license_active;
			?>

			<?php if ($license_active && $pro_enabled): ?>
				<!-- PRO Active State -->
				<div class="bfg-pro-teaser-v2 bfg-pro-teaser--active">
					<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--success">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
							<polyline points="22 4 12 14.01 9 11.01"></polyline>
						</svg>
					</div>

					<h2 class="bfg-pro-teaser__title"><?php esc_html_e('PRO License Active', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p class="bfg-pro-teaser__subtitle">
						<?php esc_html_e('You have full access to all PRO features. Thank you for your support!', 'bring-fraktguiden-for-woocommerce'); ?>
					</p>

					<div class="bfg-pro-status-card">
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('Status', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value bfg-pro-status-card__value--success"><?php esc_html_e('Active', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('License Type', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value"><?php esc_html_e('PRO License', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</div>

					<ul class="bfg-pro-features-grid bfg-pro-features-grid--compact">
						<li>
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							<?php esc_html_e('MyBring Booking', 'bring-fraktguiden-for-woocommerce'); ?>
						</li>
						<li>
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							<?php esc_html_e('Free shipping threshold', 'bring-fraktguiden-for-woocommerce'); ?>
						</li>
						<li>
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							<?php esc_html_e('Fixed price per service', 'bring-fraktguiden-for-woocommerce'); ?>
						</li>
						<li>
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							<?php esc_html_e('Pick-up points', 'bring-fraktguiden-for-woocommerce'); ?>
						</li>
					</ul>
				</div>

			<?php elseif ($is_expired): ?>
				<!-- Expired State -->
				<div class="bfg-pro-teaser-v2 bfg-pro-teaser--expired">
					<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--expired">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<line x1="12" y1="8" x2="12" y2="12"></line>
							<line x1="12" y1="16" x2="12.01" y2="16"></line>
						</svg>
					</div>

					<h2 class="bfg-pro-teaser__title"><?php esc_html_e('Trial Expired', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p class="bfg-pro-teaser__subtitle">
						<?php esc_html_e('Your trial has ended. Purchase a license to continue using PRO features.', 'bring-fraktguiden-for-woocommerce'); ?>
					</p>

					<div class="bfg-pro-status-card bfg-pro-status-card--expired">
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('Status', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value bfg-pro-status-card__value--expired"><?php esc_html_e('Expired', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('PRO Features', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value"><?php esc_html_e('Disabled', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</div>

					<div class="bfg-pro-footer">
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-button-primary bfg-pro-btn-main">
							<?php esc_html_e('Purchase PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>

			<?php elseif ($is_trial): ?>
				<!-- Trial Active State -->
				<div class="bfg-pro-teaser-v2 bfg-pro-teaser--trial">
					<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--trial">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<polyline points="12 6 12 12 16 14"></polyline>
						</svg>
					</div>

					<h2 class="bfg-pro-teaser__title"><?php esc_html_e('Trial Active', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p class="bfg-pro-teaser__subtitle">
						<?php printf(
							esc_html__('You have %d days remaining in your trial. Upgrade now to keep your PRO features!', 'bring-fraktguiden-for-woocommerce'),
							max(0, $days_remaining)
						); ?>
					</p>

					<div class="bfg-pro-status-card bfg-pro-status-card--trial">
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('Status', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value bfg-pro-status-card__value--trial"><?php esc_html_e('Trial', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('Days Remaining', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value"><?php echo max(0, $days_remaining); ?></span>
						</div>
					</div>

					<div class="bfg-pro-footer">
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-button-primary bfg-pro-btn-main">
							<?php esc_html_e('Upgrade to PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>

			<?php elseif ($is_test_site && $pro_enabled): ?>
				<!-- Test Site State -->
				<div class="bfg-pro-teaser-v2 bfg-pro-teaser--test">
					<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--test">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
						</svg>
					</div>

					<h2 class="bfg-pro-teaser__title"><?php esc_html_e('Test Environment Active', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p class="bfg-pro-teaser__subtitle">
						<?php esc_html_e('PRO features are enabled for testing. A license is required for production use.', 'bring-fraktguiden-for-woocommerce'); ?>
					</p>

					<div class="bfg-pro-status-card bfg-pro-status-card--test">
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('Environment', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value bfg-pro-status-card__value--test"><?php esc_html_e('Test Site', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
						<div class="bfg-pro-status-card__item">
							<span class="bfg-pro-status-card__label"><?php esc_html_e('PRO Features', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<span class="bfg-pro-status-card__value"><?php esc_html_e('Enabled', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</div>

					<div class="bfg-pro-footer">
						<h4 class="bfg-pro-footer__title"><?php esc_html_e('Ready to go live?', 'bring-fraktguiden-for-woocommerce'); ?></h4>
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-pro-btn-outline">
							<?php esc_html_e('Purchase PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>

			<?php else: ?>
				<!-- Fresh/Default State - Show Trial/License Options -->
			<div class="bfg-pro-teaser-v2">
				<div class="bfg-pro-teaser__shield">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
					</svg>
				</div>

				<h2 class="bfg-section-card-title"><?php esc_html_e('Unlock PRO Features', 'bring-fraktguiden-for-woocommerce'); ?></h2>
				<p class="bfg-pro-teaser__subtitle">
					<?php esc_html_e('Activate your license or start a free trial to access all premium features on your live site.', 'bring-fraktguiden-for-woocommerce'); ?>
				</p>

				<div class="bfg-pro-tabs">
					<button type="button" class="bfg-pro-tab is-active" data-tab="trial">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
						<?php esc_html_e('Start Free Trial', 'bring-fraktguiden-for-woocommerce'); ?>
					</button>
					<button type="button" class="bfg-pro-tab" data-tab="license">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3L15.5 7.5z"></path></svg>
						<?php esc_html_e('Enter License', 'bring-fraktguiden-for-woocommerce'); ?>
					</button>
				</div>

				<form method="post" action="options.php" id="bfg-pro-activation-form">
					<?php settings_fields('bring_fraktguiden_home'); ?>
					
					<!-- Hidden pro_enabled checkbox that we toggle via JS -->
					<div style="display:none">
						<?php BringFraktguiden\Admin\FieldRenderer::pro_enabled(); ?>
					</div>

					<div id="bfg-tab-trial" class="bfg-pro-tab-content is-active">
						<div class="bfg-pro-content-block">
							<h3 class="bfg-pro-content-block__title"><?php esc_html_e('7-Day Free Trial', 'bring-fraktguiden-for-woocommerce'); ?></h3>
							<p class="bfg-pro-content-block__text"><?php esc_html_e('Get instant access to all PRO features for 7 days. No credit card required.', 'bring-fraktguiden-for-woocommerce'); ?></p>
							
							<ul class="bfg-pro-features-grid">
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Book orders with signaling cost', 'bring-fraktguiden-for-woocommerce'); ?><sup>1</sup>
								</li>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Free shipping threshold', 'bring-fraktguiden-for-woocommerce'); ?>
								</li>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Fixed price per service', 'bring-fraktguiden-for-woocommerce'); ?>
								</li>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Custom shipping zone names', 'bring-fraktguiden-for-woocommerce'); ?>
								</li>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Estimated delivery services', 'bring-fraktguiden-for-woocommerce'); ?>
								</li>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Prioritized support', 'bring-fraktguiden-for-woocommerce'); ?>
								</li>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
									<?php esc_html_e('Pick-up-points for services', 'bring-fraktguiden-for-woocommerce'); ?><sup>2</sup>
								</li>
							</ul>
						</div>

						<div class="bfg-pro-content-block__test-url">
							<h4 class="bfg-pro-content-block__subtitle"><?php esc_html_e('Test Site URL (Optional)', 'bring-fraktguiden-for-woocommerce'); ?></h4>
							<?php BringFraktguiden\Admin\FieldRenderer::test_url(); ?>
							<p class="bfg-description"><?php esc_html_e('Enter your staging or test site URL to activate the trial there first.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>
						<button type="submit" class="bfg-button-primary bfg-pro-btn-main">
							<?php esc_html_e('Start My Free Trial', 'bring-fraktguiden-for-woocommerce'); ?>
						</button>
						<p class="bfg-pro-disclaimer"><?php esc_html_e('Trial starts immediately and lasts for 7 days from activation.', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<div id="bfg-tab-license" class="bfg-pro-tab-content">
						<div class="bfg-license-input-wrapper">
							<label class="bfg-license-input-label" for="bfg_license_key"><?php esc_html_e('Enter Your License Key', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<?php BringFraktguiden\Admin\FieldRenderer::test_url(); ?>
							<p class="bfg-description"><?php esc_html_e('Your license key was sent to your email after purchase.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>
						<button type="submit" class="bfg-button-primary bfg-pro-btn-main" style="background: #BFDBFE !important; color: #3B82F6 !important; cursor: not-allowed;">
							<?php esc_html_e('Activate PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</button>
					</div>
				</form>

				<div class="bfg-pro-footer">
					<h4 class="bfg-pro-footer__title"><?php esc_html_e("Don't have a license yet?", 'bring-fraktguiden-for-woocommerce'); ?></h4>
					<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-pro-btn-outline">
						<?php esc_html_e('Purchase PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
					</a>
				</div>
			</div>

			<style>
				/* Inline styles for tab switching logic if needed, but mostly served by the main CSS */
			</style>
			
			<script>
			document.addEventListener('DOMContentLoaded', function() {
				const tabs = document.querySelectorAll('.bfg-pro-tab');
				const contents = document.querySelectorAll('.bfg-pro-tab-content');
				const footnotes = document.getElementById('bfg-pro-footnotes');
				const proCheckbox = document.querySelector('input[name="pro_enabled"]');
				const form = document.getElementById('bfg-pro-activation-form');
				const testUrlInputs = document.querySelectorAll('input[name="test_url"]');
				const licenseTabButton = document.querySelector('#bfg-tab-license .bfg-pro-btn-main');
				const licenseInput = document.querySelector('#bfg-tab-license input[name="test_url"]');

				// Function to update license button state
				function updateLicenseButtonState() {
					if (licenseInput && licenseTabButton) {
						if (licenseInput.value.trim().length > 0) {
							licenseTabButton.disabled = false;
							licenseTabButton.style.setProperty('background', '#2563EB', 'important');
							licenseTabButton.style.setProperty('color', '#fff', 'important');
							licenseTabButton.style.cursor = 'pointer';
						} else {
							licenseTabButton.disabled = true;
							licenseTabButton.style.setProperty('background', '#BFDBFE', 'important');
							licenseTabButton.style.setProperty('color', '#3B82F6', 'important');
							licenseTabButton.style.cursor = 'not-allowed';
						}
					}
				}

				// Initialize placeholders on page load and handle saved values
				const activeTab = document.querySelector('.bfg-pro-tab.is-active');
				if (activeTab) {
					const activeTabName = activeTab.getAttribute('data-tab');
					testUrlInputs.forEach(input => {
						// Always clear any saved value on page load
						const currentValue = input.value.trim();
						if (currentValue) {
							input.value = '';
						}

						// Set appropriate placeholder based on active tab
						if (activeTabName === 'trial') {
							input.setAttribute('placeholder', 'https://staging.yoursite.com');
						} else if (activeTabName === 'license') {
							input.setAttribute('placeholder', 'XXXX-XXXX-XXXX-XXXX');
						}
					});
				}

				// Initialize button state on page load
				if (licenseInput && licenseTabButton) {
					updateLicenseButtonState();
					licenseInput.addEventListener('input', updateLicenseButtonState);
				}

				tabs.forEach(tab => {
					tab.addEventListener('click', () => {
						const target = tab.getAttribute('data-tab');

						tabs.forEach(t => t.classList.remove('is-active'));
						contents.forEach(c => c.classList.remove('is-active'));

						tab.classList.add('is-active');
						document.getElementById('bfg-tab-' + target).classList.add('is-active');

						// Handle footnotes visibility
						if (footnotes) {
							footnotes.style.display = (target === 'trial') ? 'block' : 'none';
						}

						// Update placeholder based on active tab
						testUrlInputs.forEach(input => {
							if (target === 'trial') {
								input.setAttribute('placeholder', 'https://staging.yoursite.com');
							} else if (target === 'license') {
								input.setAttribute('placeholder', 'XXXX-XXXX-XXXX-XXXX');
							}
						});

						// Update button state when switching to license tab
						if (target === 'license') {
							updateLicenseButtonState();
						}
					});
				});

				if (form) {
					form.addEventListener('submit', function() {
						if (proCheckbox) {
							proCheckbox.checked = true;
						}
					});
				}
			});
			</script>

			<div class="bfg-page__footer-notes" id="bfg-pro-footnotes">
				<small><sup>1</sup> <?php esc_html_e('Domestic shipments only. We\'re working on building support for international shipping.', 'bring-fraktguiden-for-woocommerce'); ?></small>
				<small><sup>2</sup> <?php esc_html_e('List of currently supported services for using pickup point: Pickup parcel (5800), Pakke til Pakkeboks (5801), Express next day (4850), Business parcel (5000), Norgespakke (3067), PICKUP_PARCEL and PICKUP_PARCEL_BULK', 'bring-fraktguiden-for-woocommerce'); ?></small>
			</div>
			<?php endif; ?>

	</div>
</div>
