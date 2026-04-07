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
 * @var int $shipmentsThisMonth
 * @var int $activeShippingMethodsCount
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
			<!-- Free Version State -->
			<div class="bfg-section bfg-pro-free-state">

				<!-- Card 1: Setup Complete -->
				<div class="bfg-free-card bfg-complete-card">
					<div class="bfg-complete-card__icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
					</div>
					<div class="bfg-complete-card__body">
						<h2 class="bfg-complete-card__title"><t>Setup Complete</t></h2>
						<p class="bfg-complete-card__desc"><t>Your shipping is configured and ready to use.</t></p>
						<p class="bfg-complete-card__hint">
							<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_pro')); ?>" class="bfg-link-green bfg-link-green--bold"><t>Explore Pro features</t></a>
							<t>if you want more capabilities</t>
						</p>
					</div>
				</div>

				<!-- Card 2: Next — Configure Bring Booking (PRO upsell) -->
				<div class="bfg-free-card bfg-booking-upsell">
					<div class="bfg-booking-upsell__icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
							<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
							<line x1="12" y1="22.08" x2="12" y2="12"></line>
						</svg>
					</div>
					<div class="bfg-booking-upsell__body">
						<div class="bfg-booking-upsell__title-row">
							<h3 class="bfg-booking-upsell__title"><t>Next: Configure Bring Booking</t></h3>
							<span class="bfg-badge bfg-badge--pro-green"><t>PRO</t></span>
						</div>
						<p class="bfg-booking-upsell__desc"><t>Automatically book shipments and print labels in one click</t></p>
						<ul class="bfg-check-list">
							<li>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
									<polyline points="22 4 12 14.01 9 11.01"></polyline>
								</svg>
								<t>Automatic booking when orders are placed</t>
							</li>
							<li>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
									<polyline points="22 4 12 14.01 9 11.01"></polyline>
								</svg>
								<t>Print shipping labels directly from WooCommerce</t>
							</li>
							<li>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
									<polyline points="22 4 12 14.01 9 11.01"></polyline>
								</svg>
								<t>Real-time tracking updates for customers</t>
							</li>
						</ul>
						<div class="bfg-booking-upsell__cta">
							<form method="post" action="options.php" id="bfg-pro-activation-form-pro">
								<?php settings_fields('bring_fraktguiden_pro'); ?>
								<div style="display:none">
									<?php FieldRenderer::pro_enabled(); ?>
								</div>
								<button type="submit" class="bfg-btn bfg-btn--primary">
									<t>Try it free for 7 days</t>
								</button>
							</form>
						</div>
					</div>
				</div>

				<!-- Stats Row -->
				<div class="bfg-stats-row">
					<div class="bfg-free-card bfg-stat-card">
						<div class="bfg-stat-card__icon">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
							</svg>
						</div>
						<div>
							<div class="bfg-stat-card__number"><?php echo esc_html($shipmentsThisMonth); ?></div>
							<div class="bfg-stat-card__label"><t>Shipments this month</t></div>
						</div>
					</div>
					<div class="bfg-free-card bfg-stat-card">
						<div class="bfg-stat-card__icon">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round">
								<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
								<polyline points="17 6 23 6 23 12"></polyline>
							</svg>
						</div>
						<div>
							<div class="bfg-stat-card__number"><?php echo esc_html($activeShippingMethodsCount); ?></div>
							<div class="bfg-stat-card__label"><t>Active shipping methods</t></div>
						</div>
					</div>
				</div>

				<!-- Card 4: Do more with Pro -->
				<div class="bfg-free-card bfg-do-more-card">
					<div class="bfg-do-more-card__header">
						<div class="bfg-do-more-card__icon">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z" />
							</svg>
						</div>
						<h3 class="bfg-do-more-card__title"><t>Do more with Pro</t></h3>
					</div>
					<div class="bfg-do-more-card__grid">
						<div class="bfg-do-more-feature">
							<strong><t>MyBring Booking</t></strong>
							<span><t>Book shipments in one click</t></span>
						</div>
						<div class="bfg-do-more-feature">
							<strong><t>Pickup Points</t></strong>
							<span><t>Show pickup locations</t></span>
						</div>
						<div class="bfg-do-more-feature">
							<strong><t>Shipping Labels</t></strong>
							<span><t>Print labels automatically</t></span>
						</div>
					</div>
					<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_pro')); ?>" class="bfg-link-green">
						<t>View all Pro features</t> &rarr;
					</a>
				</div>

			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function () {
					const proCheckbox = document.querySelector('#bfg-pro-activation-form-pro input[name="pro_enabled"]');
					const trialForm = document.getElementById('bfg-pro-activation-form-pro');
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
