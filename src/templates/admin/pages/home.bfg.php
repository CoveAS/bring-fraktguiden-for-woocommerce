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
			<h1>
				<t>Home</t>
			</h1>
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

			<!-- Setup Header -->
			<div class="bfg-setup-header">
				<h2 class="bfg-setup-header__title">
					<t>Get Started with Bring shipping</t>
				</h2>
				<p class="bfg-setup-header__subtitle">
					<t>Complete these steps to configure Bring Shipping</t>
				</p>

				<div class="bfg-setup-header__stats">
					<div class="bfg-setup-header__stat">
						<span class="bfg-setup-header__stat-label">
							<t>Current Step</t>
						</span>
						<span class="bfg-setup-header__stat-value">
							<?php printf(__('Step %d of %d', 'bring-fraktguiden-for-woocommerce'), $currentStepNumber, $stepCount); ?>
						</span>
					</div>
					<div class="bfg-setup-header__stat bfg-setup-header__stat--right">
						<span class="bfg-setup-header__stat-label">
							<t>Progress</t>
						</span>
						<span class="bfg-setup-header__stat-value">
							<?php printf(__('%d of %d completed', 'bring-fraktguiden-for-woocommerce'), $stepsCompleted, $stepCount); ?>
						</span>
					</div>
				</div>

				<div class="bfg-progress-container">
					<div class="bfg-progress-bar-new">
						<div class="bfg-progress-bar-fill"
							style="width: <?php echo ($stepsCompleted / $stepCount) * 100; ?>%;"></div>
					</div>
				</div>
			</div>

			<div class="bfg-steps-list">
				<?php foreach ($steps as $i => $step): ?>
					<?php
					// Only mark as "in progress" if we're showing the next step (i.e., at least one step completed)
					$isNext = $showNextStep && $nextStep === $step;
					?>
					<?php if ($step->completed): ?>
						<bfg-step.completed :href="$step->action">
							<?php echo esc_html($step->label); ?>
							<bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
							<bfg-badge.completed>
								<t>Done</t>
							</bfg-badge.completed>
						</bfg-step.completed>
					<?php elseif ($isNext): ?>
						<bfg-step.in-progress :number="$i + 1">
							<?php echo esc_html($step->label); ?>
							<bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
							<a class="bfg-btn bfg-btn--primary bfg-btn--sm" href="<?php echo esc_attr($step->action); ?>">
								<?php echo esc_html($step->actionText); ?>
							</a>
							<bfg-badge.in-progress>
								<t>In Progress</t>
							</bfg-badge.in-progress>
						</bfg-step.in-progress>
					<?php else: ?>
						<bfg-step.pending :href="$step->action" :number="$i + 1">
							<?php echo esc_html($step->label); ?>
							<bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
						</bfg-step.pending>
					<?php endif; ?>
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
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round">
						<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
				</div>

				<h2 class="bfg-pro-teaser__title">
					<t>PRO License Active</t>
				</h2>
				<p class="bfg-pro-teaser__subtitle">
					<t>You have full access to all PRO features. Thank you for your support!</t>
				</p>

				<bfg-subscription-info class="bfg-subscription--success">
					<bfg-subscription-item label="Status" value="Active"></bfg-subscription-item>
					<bfg-subscription-item label="License Type" value="PRO License"></bfg-subscription-item>
				</bfg-subscription-info>

				<ul class="bfg-pro-features-grid bfg-pro-features-grid--compact">
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>MyBring Booking</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Free shipping threshold</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Fixed price per service</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Pick-up points</t>
					</li>
				</ul>
			</div>

		<?php elseif ($is_expired): ?>
			<!-- Expired State -->
			<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--expired">
				<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--expired">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="12" y1="8" x2="12" y2="12"></line>
						<line x1="12" y1="16" x2="12.01" y2="16"></line>
					</svg>
				</div>

				<h2 class="bfg-pro-teaser__title">
					<t>Trial Expired</t>
				</h2>
				<p class="bfg-pro-teaser__subtitle">
					<t>Your trial has ended. Purchase a license to continue using PRO features.</t>
				</p>

				<bfg-subscription-info class="bfg-subscription--expired">
					<bfg-subscription-item label="Status" value="Expired"></bfg-subscription-item>
					<bfg-subscription-item label="PRO Features" value="Disabled"></bfg-subscription-item>
				</bfg-subscription-info>

				<div class="bfg-pro-footer">
					<a href="https://bringfraktguiden.no/" target="_blank"
						class="bfg-btn bfg-btn--primary bfg-btn--lg bfg-btn--full-width">
						<t>Purchase PRO License</t>
					</a>
				</div>
			</div>

		<?php elseif ($is_trial): ?>
			<!-- Trial Active State -->
			<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--trial">
				<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--trial">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round">
						<circle cx="12" cy="12" r="10"></circle>
						<polyline points="12 6 12 12 16 14"></polyline>
					</svg>
				</div>

				<h2 class="bfg-pro-teaser__title">
					<t>Trial Active</t>
				</h2>
				<p class="bfg-pro-teaser__subtitle">
					<?php printf(
						esc_html__('You have %d days remaining in your trial. Upgrade now to keep your PRO features!', 'bring-fraktguiden-for-woocommerce'),
						max(0, $days_remaining)
					); ?>
				</p>

				<bfg-subscription-info class="bfg-subscription--trial">
					<bfg-subscription-item label="Status" value="Trial"></bfg-subscription-item>
					<bfg-subscription-item label="Days Remaining" :value="max(0, $days_remaining)"></bfg-subscription-item>
				</bfg-subscription-info>

				<div class="bfg-pro-footer">
					<a href="https://bringfraktguiden.no/" target="_blank"
						class="bfg-btn bfg-btn--primary bfg-btn--lg bfg-btn--full-width">
						<t>Upgrade to PRO License</t>
					</a>
				</div>
			</div>

		<?php elseif ($is_test_site && $pro_enabled): ?>
			<!-- Test Site State -->
			<div class="bfg-box bfg-pro-teaser-v2 bfg-pro-teaser--test">
				<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--test">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round">
						<path
							d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
						</path>
					</svg>
				</div>

				<h2 class="bfg-pro-teaser__title">
					<t>Test Environment Active</t>
				</h2>
				<p class="bfg-pro-teaser__subtitle">
					<t>PRO features are enabled for testing. A license is required for production use.</t>
				</p>

				<bfg-subscription-info class="bfg-subscription--test">
					<bfg-subscription-item label="Environment" value="Test Site"></bfg-subscription-item>
					<bfg-subscription-item label="PRO Features" value="Enabled"></bfg-subscription-item>
				</bfg-subscription-info>

				<div class="bfg-pro-footer">
					<h4 class="bfg-pro-footer__title">
						<t>Ready to go live?</t>
					</h4>
					<a href="https://bringfraktguiden.no/" target="_blank"
						class="bfg-btn bfg-btn--secondary bfg-btn--lg bfg-btn--full-width">
						<t>Purchase PRO License</t>
					</a>
				</div>
			</div>

		<?php else: ?>
			<!-- Fresh/Default State - Show Trial/License Options -->
			<div class="bfg-box bfg-pro-teaser-v2">
				<p class="bfg-pro-teaser__caption">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round">
						<path
							d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z" />
						<path d="M5 3v4" />
						<path d="M19 17v4" />
						<path d="M3 5h4" />
						<path d="M17 19h4" />
					</svg>
					<t>Try Pro Free for 7 Days</t>
				</p>
				<h2 class="bfg-section-card-title">
					<t>Experience Pro Features</t>
				</h2>
				<p class="bfg-pro-teaser__subtitle">
					<t>Get instant access to all premium features on your live site. Start your free trial or activate your
						license.</t>
				</p>

				<ul class="bfg-pro-teaser-features">
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>MyBring Booking</t><sup>1</sup>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Fixed shipping prices</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Free shipping threshold</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Pick-up points</t><sup>2</sup>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Multiple customer numbers</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Custom service names</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>Service fallback pricing</t>
					</li>
					<li>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
						<t>PRO support</t>
					</li>
				</ul>

				<form method="post" action="options.php" id="bfg-pro-activation-form">
					<?php settings_fields('bring_fraktguiden_home'); ?>

					<!-- Hidden pro_enabled checkbox that we toggle via JS -->
					<div style="display:none">
						<?php BringFraktguiden\Admin\FieldRenderer::pro_enabled(); ?>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary bfg-btn--lg bfg-btn--full-width">
						<t>Start Your Free 7-Day Trial</t>
					</button>
					<p class="bfg-pro-disclaimer">
						<t>Trial starts immediately and lasts for 7 days from activation.</t>
					</p>
				</form>

				<div class="bfg-pro-divider">
					<span>
						<t>Already have a license?</t>
					</span>
				</div>

				<div class="bfg-pro-license-links">
					<a href="#" class="bfg-pro-license-activate-link" id="bfg-show-license-form">
						<t>Click here to activate your license</t>
					</a>
					<p class="bfg-pro-license-buy">
						<t>Don't have a license?</t> <a href="https://bringfraktguiden.no/" target="_blank">
							<t>Buy one here</t>
						</a>
					</p>
				</div>

				<form method="post" action="options.php" id="bfg-license-form" class="bfg-pro-license-form"
					style="display: none;">
					<?php settings_fields('bring_fraktguiden_home'); ?>

					<div style="display:none">
						<?php BringFraktguiden\Admin\FieldRenderer::pro_enabled(); ?>
					</div>

					<div class="bfg-pro-license-form__field">
						<label class="bfg-pro-license-form__label">
							<t>Enter Your License Key</t>
						</label>
						<div class="bfg-pro-license-form__row">
							<?php BringFraktguiden\Admin\FieldRenderer::test_url(); ?>
							<span class="bfg-license-feedback" id="bfg-license-feedback"></span>
						</div>
						<p class="bfg-description">
							<t>Your license key is a 16-character code you received after purchase</t>
						</p>
					</div>

					<p class="bfg-pro-license-buy" style="text-align: center;">
						<t>Need a license?</t> <a href="https://bringfraktguiden.no/" target="_blank">
							<t>Purchase here</t>
						</a>
					</p>
				</form>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function () {
					const proCheckbox = document.querySelector('#bfg-pro-activation-form input[name="pro_enabled"]');
					const trialForm = document.getElementById('bfg-pro-activation-form');
					const licenseForm = document.getElementById('bfg-license-form');
					const showLicenseLink = document.getElementById('bfg-show-license-form');
					const licenseLinks = document.querySelector('.bfg-pro-license-links');
					const licenseInput = licenseForm ? licenseForm.querySelector('input[name="test_url"]') : null;
					const licenseFeedback = document.getElementById('bfg-license-feedback');

					// Show license form when clicking the link
					if (showLicenseLink && licenseForm && licenseLinks) {
						showLicenseLink.addEventListener('click', function (e) {
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

						licenseInput.addEventListener('input', function () {
							showFeedback('');
							clearTimeout(saveTimeout);

							if (isValidLicenseFormat(licenseInput.value)) {
								saveTimeout = setTimeout(async function () {
									showFeedback('saving');
									const success = await saveLicense(licenseInput.value);
									if (success) {
										showFeedback('saved');
										setTimeout(function () {
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
						trialForm.addEventListener('submit', function () {
							if (proCheckbox) {
								proCheckbox.checked = true;
							}
						});
					}
				});
			</script>

			<div class="bfg-page__footer-notes" id="bfg-pro-footnotes">
				<small><sup>1</sup>
					<t>Domestic shipments only. We're working on building support for international shipping.</t>
				</small>
				<small><sup>2</sup>
					<t>List of currently supported services for using pickup point: Pickup parcel (5800), Pakke til
						Pakkeboks (5801), Express next day (4850), Business parcel (5000), Norgespakke (3067), PICKUP_PARCEL
						and PICKUP_PARCEL_BULK</t>
				</small>
			</div>
		<?php endif; ?>

	</div>
</div>