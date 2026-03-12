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

<?php /* Styles moved to assets/css/bring-fraktguiden-admin-home.css */ ?>

<div class="wrap bfg-admin-page bfg-admin-page__home">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><?php esc_html_e('Home', 'bring-fraktguiden-for-woocommerce'); ?></h1>
		</div>

		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-box">
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
						<a class="bfg-btn bfg-btn--primary" href="<?php echo esc_attr($nextStep->action); ?>">
							<?php echo esc_html($nextStep->actionText); ?>
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
				<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--active">
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
				<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--expired">
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
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--primary bfg-btn--lg bfg-btn--full-width">
							<?php esc_html_e('Purchase PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>

			<?php elseif ($is_trial): ?>
				<!-- Trial Active State -->
				<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--trial">
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
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--primary bfg-btn--lg bfg-btn--full-width">
							<?php esc_html_e('Upgrade to PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>

			<?php elseif ($is_test_site && $pro_enabled): ?>
				<!-- Test Site State -->
				<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--test">
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
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--secondary bfg-btn--lg bfg-btn--full-width">
							<?php esc_html_e('Purchase PRO License', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>

			<?php else: ?>
				<!-- Fresh/Default State - Show Trial/License Options -->
			<div class="bfg-box bfg-pro-teaser-v2">
				<p class="bfg-pro-teaser__caption">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
					<?php esc_html_e('Try Pro Free for 7 Days', 'bring-fraktguiden-for-woocommerce'); ?>
				</p>
				<h2 class="bfg-section-card-title"><?php esc_html_e('Experience Pro Features', 'bring-fraktguiden-for-woocommerce'); ?></h2>
				<p class="bfg-pro-teaser__subtitle">
					<?php esc_html_e('Get instant access to all premium features on your live site. Start your free trial or activate your license.', 'bring-fraktguiden-for-woocommerce'); ?>
				</p>

				<ul class="bfg-pro-features-grid bfg-pro-features-grid--two-col">
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('MyBring Booking', 'bring-fraktguiden-for-woocommerce'); ?><sup>1</sup>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('Fixed shipping prices', 'bring-fraktguiden-for-woocommerce'); ?>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('Free shipping threshold', 'bring-fraktguiden-for-woocommerce'); ?>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('Pick-up points', 'bring-fraktguiden-for-woocommerce'); ?><sup>2</sup>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('Multiple customer numbers', 'bring-fraktguiden-for-woocommerce'); ?>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('Custom service names', 'bring-fraktguiden-for-woocommerce'); ?>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('Service fallback pricing', 'bring-fraktguiden-for-woocommerce'); ?>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<?php esc_html_e('PRO support', 'bring-fraktguiden-for-woocommerce'); ?>
					</li>
				</ul>

				<form method="post" action="options.php" id="bfg-pro-activation-form">
					<?php settings_fields('bring_fraktguiden_home'); ?>

					<!-- Hidden pro_enabled checkbox that we toggle via JS -->
					<div style="display:none">
						<?php BringFraktguiden\Admin\FieldRenderer::pro_enabled(); ?>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary bfg-btn--lg bfg-btn--full-width">
						<?php esc_html_e('Start Your Free 7-Day Trial', 'bring-fraktguiden-for-woocommerce'); ?>
					</button>
					<p class="bfg-pro-disclaimer"><?php esc_html_e('Trial starts immediately and lasts for 7 days from activation.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</form>

				<div class="bfg-pro-divider">
					<span><?php esc_html_e('Already have a license?', 'bring-fraktguiden-for-woocommerce'); ?></span>
				</div>

				<div class="bfg-pro-license-links">
					<a href="#" class="bfg-pro-license-activate-link" id="bfg-show-license-form"><?php esc_html_e('Click here to activate your license', 'bring-fraktguiden-for-woocommerce'); ?></a>
					<p class="bfg-pro-license-buy"><?php esc_html_e("Don't have a license?", 'bring-fraktguiden-for-woocommerce'); ?> <a href="https://bringfraktguiden.no/" target="_blank"><?php esc_html_e('Buy one here', 'bring-fraktguiden-for-woocommerce'); ?></a></p>
				</div>

				<form method="post" action="options.php" id="bfg-license-form" class="bfg-pro-license-form" style="display: none;">
					<?php settings_fields('bring_fraktguiden_home'); ?>

					<div style="display:none">
						<?php BringFraktguiden\Admin\FieldRenderer::pro_enabled(); ?>
					</div>

					<div class="bfg-pro-license-form__field">
						<label class="bfg-pro-license-form__label"><?php esc_html_e('Enter Your License Key', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<div class="bfg-pro-license-form__row">
							<?php BringFraktguiden\Admin\FieldRenderer::test_url(); ?>
							<span class="bfg-license-feedback" id="bfg-license-feedback"></span>
						</div>
						<p class="bfg-description"><?php esc_html_e('Your license key is a 16-character code you received after purchase', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<p class="bfg-pro-license-buy" style="text-align: center;"><?php esc_html_e('Need a license?', 'bring-fraktguiden-for-woocommerce'); ?> <a href="https://bringfraktguiden.no/" target="_blank"><?php esc_html_e('Purchase here', 'bring-fraktguiden-for-woocommerce'); ?></a></p>
				</form>
			</div>

			<script>
			document.addEventListener('DOMContentLoaded', function() {
				const proCheckbox = document.querySelector('#bfg-pro-activation-form input[name="pro_enabled"]');
				const trialForm = document.getElementById('bfg-pro-activation-form');
				const licenseForm = document.getElementById('bfg-license-form');
				const showLicenseLink = document.getElementById('bfg-show-license-form');
				const licenseLinks = document.querySelector('.bfg-pro-license-links');
				const licenseInput = licenseForm ? licenseForm.querySelector('input[name="test_url"]') : null;
				const licenseFeedback = document.getElementById('bfg-license-feedback');

				// Show license form when clicking the link
				if (showLicenseLink && licenseForm && licenseLinks) {
					showLicenseLink.addEventListener('click', function(e) {
						e.preventDefault();
						licenseLinks.style.display = 'none';
						licenseForm.style.display = 'block';
						if (licenseInput) {
							licenseInput.focus();
						}
					});
				}

				// Check if license key format is valid (XXXX-XXXX-XXXX-XXXX or 16 chars without dashes)
				function isValidLicenseFormat(value) {
					const cleaned = value.replace(/-/g, '').trim();
					return cleaned.length === 16;
				}

				// Show feedback
				function showFeedback(type) {
					if (!licenseFeedback) return;
					if (type === 'saving') {
						licenseFeedback.innerHTML = '<span class="bfg-license-saving"></span>';
						licenseFeedback.className = 'bfg-license-feedback bfg-license-feedback--saving';
					} else if (type === 'saved') {
						licenseFeedback.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
						licenseFeedback.className = 'bfg-license-feedback bfg-license-feedback--saved';
					} else if (type === 'error') {
						licenseFeedback.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
						licenseFeedback.className = 'bfg-license-feedback bfg-license-feedback--error';
					} else {
						licenseFeedback.innerHTML = '';
						licenseFeedback.className = 'bfg-license-feedback';
					}
				}

				// Save license via AJAX
				async function saveLicense(licenseKey) {
					const formData = new FormData();
					formData.append('action', 'bring_save_license');
					formData.append('license_key', licenseKey);

					try {
						const response = await fetch(ajaxurl, {
							method: 'POST',
							body: formData
						});
						const data = await response.json();
						return data.status === 'success';
					} catch (error) {
						return false;
					}
				}

				// Auto-save license on valid input
				let saveTimeout = null;
				if (licenseInput) {
					licenseInput.setAttribute('placeholder', 'XXXX-XXXX-XXXX-XXXX');
					licenseInput.value = '';

					licenseInput.addEventListener('input', function() {
						showFeedback('');
						clearTimeout(saveTimeout);

						if (isValidLicenseFormat(licenseInput.value)) {
							saveTimeout = setTimeout(async function() {
								showFeedback('saving');
								const success = await saveLicense(licenseInput.value);
								if (success) {
									showFeedback('saved');
									setTimeout(function() {
										window.location.reload();
									}, 800);
								} else {
									showFeedback('error');
								}
							}, 500);
						}
					});
				}

				// Handle trial form submit
				if (trialForm) {
					trialForm.addEventListener('submit', function() {
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
