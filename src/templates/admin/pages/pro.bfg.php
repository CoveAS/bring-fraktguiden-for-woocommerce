<?php

use BringFraktguiden\Admin\FieldRenderer;

/**
 * Pro Page Template
 *
 * @var bool $is_test_site
 * @var bool $license_active
 * @var bool $pro_enabled
 * @var bool $pro_activated
 * @var int $days_remaining
 * @var int|false $pro_activated_on
 * @var bool $is_trial
 * @var bool $is_expired
 * @var string $valid_to_formatted
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__pro">
	<div class="bfg-page__header">
		<h1>
			<t>Bring Fraktguiden Pro</t>
		</h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php if ($license_active && $pro_enabled): ?>
			<!-- PRO Active State -->
			<div class="bfg-section bfg-pro-teaser-v2 bfg-pro-teaser--active">
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
					<?php if ($valid_to_formatted): ?>
						<bfg-subscription-item label="Valid Until" :value="$valid_to_formatted"></bfg-subscription-item>
					<?php endif; ?>
				</bfg-subscription-info>
			</div>

			<!-- PRO Features Overview -->
			<div class="bfg-section">
				<bfg-section.header>
					<t>Your PRO Features</t>
				</bfg-section.header>

				<bfg-section.section>
					<ul class="bfg-pro-features-grid">
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
				</bfg-section.section>
			</div>

		<?php elseif ($is_expired): ?>
			<!-- Expired State -->
			<div class="bfg-section bfg-pro-teaser-v2 bfg-pro-teaser--expired">
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

			<!-- License Activation Form -->
			<div class="bfg-section">
				<bfg-section.header>
					<t>Activate Your License</t>
				</bfg-section.header>

				<bfg-section.section>
					<form method="post" action="options.php" id="bfg-license-form-pro">
						<?php settings_fields('bring_fraktguiden_pro'); ?>

						<div style="display:none">
							<?php FieldRenderer::pro_enabled(); ?>
						</div>

						<div class="bfg-field">
							<label class="bfg-field__label">
								<t>Enter Your License Key</t>
							</label>
							<div class="bfg-pro-license-form__row">
								<?php FieldRenderer::test_url(); ?>
								<span class="bfg-license-feedback" id="bfg-license-feedback-pro"></span>
							</div>
							<p class="bfg-description">
								<t>Your license key is a 16-character code you received after purchase</t>
							</p>
						</div>
					</form>
				</bfg-section.section>
			</div>

		<?php elseif ($is_trial): ?>
			<!-- Trial Active State -->
			<div class="bfg-section bfg-pro-teaser-v2 bfg-pro-teaser--trial">
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

			<!-- PRO Features Overview -->
			<div class="bfg-section">
				<bfg-section.header>
					<t>PRO Features You're Enjoying</t>
				</bfg-section.header>

				<bfg-section.section>
					<ul class="bfg-pro-features-grid">
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
				</bfg-section.section>
			</div>

			<!-- License Activation Form -->
			<div class="bfg-section">
				<bfg-section.header title="Activate Your License" description="Already have a license? Enter it below to activate."></bfg-section.header>

				<bfg-section.section>
					<form method="post" action="options.php" id="bfg-license-form-pro">
						<?php settings_fields('bring_fraktguiden_pro'); ?>

						<div style="display:none">
							<?php FieldRenderer::pro_enabled(); ?>
						</div>

						<div class="bfg-field">
							<label class="bfg-field__label">
								<t>Enter Your License Key</t>
							</label>
							<div class="bfg-pro-license-form__row">
								<?php FieldRenderer::test_url(); ?>
								<span class="bfg-license-feedback" id="bfg-license-feedback-pro"></span>
							</div>
							<p class="bfg-description">
								<t>Your license key is a 16-character code you received after purchase</t>
							</p>
						</div>
					</form>
				</bfg-section.section>
			</div>

			<div class="bfg-page__footer-notes">
				<small><sup>1</sup>
					<t>Domestic shipments only. We're working on building support for international shipping.</t>
				</small>
				<small><sup>2</sup>
					<t>List of currently supported services for using pickup point: Pickup parcel (5800), Pakke til
						Pakkeboks (5801), Express next day (4850), Business parcel (5000), Norgespakke (3067), PICKUP_PARCEL
						and PICKUP_PARCEL_BULK</t>
				</small>
			</div>

		<?php elseif ($is_test_site && $pro_enabled): ?>
			<!-- Test Site State -->
			<div class="bfg-section bfg-pro-teaser-v2 bfg-pro-teaser--test">
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

			<!-- PRO Features Overview -->
			<div class="bfg-section">
				<bfg-section.header>
					<t>PRO Features Available</t>
				</bfg-section.header>

				<bfg-section.section>
					<ul class="bfg-pro-features-grid">
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
				</bfg-section.section>
			</div>

		<?php else: ?>
			<!-- Fresh/Default State - Show Trial/License Options -->
			<div class="bfg-section bfg-pro-teaser-v2">
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
					<t>Unlock PRO Features</t>
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

				<form method="post" action="options.php" id="bfg-pro-activation-form-pro">
					<?php settings_fields('bring_fraktguiden_pro'); ?>

					<!-- Hidden pro_enabled checkbox that we toggle via JS -->
					<div style="display:none">
						<?php FieldRenderer::pro_enabled(); ?>
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
					<a href="#" class="bfg-pro-license-activate-link" id="bfg-show-license-form-pro">
						<t>Click here to activate your license</t>
					</a>
					<p class="bfg-pro-license-buy">
						<t>Don't have a license?</t> <a href="https://bringfraktguiden.no/" target="_blank">
							<t>Buy one here</t>
						</a>
					</p>
				</div>

				<form method="post" action="options.php" id="bfg-license-form-pro" class="bfg-pro-license-form"
					style="display: none;">
					<?php settings_fields('bring_fraktguiden_pro'); ?>

					<div style="display:none">
						<?php FieldRenderer::pro_enabled(); ?>
					</div>

					<div class="bfg-pro-license-form__field">
						<label class="bfg-pro-license-form__label">
							<t>Enter Your License Key</t>
						</label>
						<div class="bfg-pro-license-form__row">
							<?php FieldRenderer::test_url(); ?>
							<span class="bfg-license-feedback" id="bfg-license-feedback-pro"></span>
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

			<script>
				document.addEventListener('DOMContentLoaded', function () {
					const proCheckbox = document.querySelector('#bfg-pro-activation-form-pro input[name="pro_enabled"]');
					const trialForm = document.getElementById('bfg-pro-activation-form-pro');
					const licenseForm = document.getElementById('bfg-license-form-pro');
					const showLicenseLink = document.getElementById('bfg-show-license-form-pro');
					const licenseLinks = document.querySelector('.bfg-pro-license-links');
					const licenseInput = licenseForm ? licenseForm.querySelector('input[name="test_url"]') : null;
					const licenseFeedback = document.getElementById('bfg-license-feedback-pro');

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
		<?php endif; ?>

	</div>
</div>
