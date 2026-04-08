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
 * @var string $license_key
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

			<!-- Status Banner -->
			<div class="bfg-notice-banner" type="success">
				<span class="bfg-notice-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round">
						<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/>
						<path d="M5 21h14"/>
					</svg>
				</span>
				<p><t>Pro license active — All features available</t></p>
			</div>

			<!-- License Info Card -->
			<div class="bfg-section bfg-license-info-section">
				<div class="bfg-free-card bfg-complete-card bfg-license-card">
					<div class="bfg-complete-card__icon bfg-complete-card__icon--success">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="7.5" cy="15.5" r="5.5"/>
							<path d="m21 2-9.6 9.6"/>
							<path d="m15.5 7.5 3 3L22 7l-3-3"/>
						</svg>
					</div>
					<div class="bfg-complete-card__body">
						<h2 class="bfg-complete-card__title"><t>License Active</t></h2>
						<?php if ($license_key): ?>
							<span class="bfg-license-key-pill"><?php echo esc_html($license_key); ?></span>
						<?php endif; ?>
						<?php if ($valid_to_formatted): ?>
							<p class="bfg-license-expiry">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
									<line x1="16" y1="2" x2="16" y2="6"></line>
									<line x1="8" y1="2" x2="8" y2="6"></line>
									<line x1="3" y1="10" x2="21" y2="10"></line>
								</svg>
								<?php printf(
									esc_html__('Expires %s', 'bring-fraktguiden-for-woocommerce'),
									esc_html($valid_to_formatted)
								); ?>
							</p>
						<?php endif; ?>
					</div>
					<div class="bfg-license-card__actions">
						<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-link-green bfg-link-green--bold">
							<t>Manage</t>
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
								<polyline points="15 3 21 3 21 9"></polyline>
								<line x1="10" y1="14" x2="21" y2="3"></line>
							</svg>
						</a>
					</div>
				</div>
			</div>

			<!-- PRO Features Grid -->
			<div class="bfg-pro-features-section">
				<div class="bfg-pro-features-section__header">
					<h3 class="bfg-pro-features-section__title"><t>Features</t></h3>
					<p class="bfg-pro-features-section__subtitle"><t>Click to configure each feature</t></p>
				</div>
				<div class="bfg-pro-feature-card-grid">

					<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_booking')); ?>"
						class="bfg-pro-feature-card bfg-pro-feature-card--link">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
								<line x1="16" y1="2" x2="16" y2="6"></line>
								<line x1="8" y1="2" x2="8" y2="6"></line>
								<line x1="3" y1="10" x2="21" y2="10"></line>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>MyBring Booking</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Book shipments directly from WooCommerce</t></span>
					</a>

					<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')); ?>"
						class="bfg-pro-feature-card bfg-pro-feature-card--link">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
								<circle cx="12" cy="10" r="3"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Pickup Points</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Show pickup locations to customers</t></span>
					</a>

					<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')); ?>"
						class="bfg-pro-feature-card bfg-pro-feature-card--link">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<line x1="12" y1="1" x2="12" y2="23"></line>
								<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Fixed Pricing</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Set custom prices per shipping service</t></span>
					</a>

					<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')); ?>"
						class="bfg-pro-feature-card bfg-pro-feature-card--link">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M20 12V22H4V12"/>
								<path d="M22 7H2v5h20V7z"/>
								<path d="M12 22V7"/>
								<path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
								<path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Free Shipping</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Configure free shipping thresholds</t></span>
					</a>

					<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_fallback')); ?>"
						class="bfg-pro-feature-card bfg-pro-feature-card--link">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
								<polyline points="2 17 12 22 22 17"></polyline>
								<polyline points="2 12 12 17 22 12"></polyline>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Fallback Pricing</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Fallback rates when API is unavailable</t></span>
					</a>

					<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_settings')); ?>"
						class="bfg-pro-feature-card bfg-pro-feature-card--link">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<circle cx="12" cy="12" r="3"></circle>
								<path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>PRO Settings</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Customer numbers, service names and more</t></span>
					</a>

				</div>
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

				<!-- Card 1: Pro Features upsell -->
				<div class="bfg-free-card bfg-pro-upsell-card">
					<div class="bfg-pro-upsell-card__icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/>
							<path d="M5 21h14"/>
						</svg>
					</div>
					<div class="bfg-pro-upsell-card__body">
						<h2 class="bfg-complete-card__title"><t>Pro Features</t></h2>
						<p class="bfg-pro-upsell-card__desc"><t>Try everything free for 7 days, or purchase directly if you prefer.</t></p>
						<div class="bfg-pro-upsell-card__ctas">
							<form method="post" action="options.php" id="bfg-pro-activation-form-pro">
								<?php settings_fields('bring_fraktguiden_pro'); ?>
								<div style="display:none">
									<?php FieldRenderer::pro_enabled(); ?>
								</div>
								<button type="submit" class="bfg-btn bfg-btn--primary">
									<t>Start Free Trial</t>
								</button>
							</form>
							<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--secondary">
								<t>Purchase License</t>
							</a>
						</div>
					</div>
				</div>

				<!-- Card 3: Have a license key? -->
				<div class="bfg-free-card bfg-complete-card bfg-license-activate-card">
					<div class="bfg-complete-card__icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="7.5" cy="15.5" r="5.5"/>
							<path d="m21 2-9.6 9.6"/>
							<path d="m15.5 7.5 3 3L22 7l-3-3"/>
						</svg>
					</div>
					<div class="bfg-complete-card__body">
						<h3 class="bfg-complete-card__title"><t>Have a license key?</t></h3>
						<p class="bfg-complete-card__desc"><t>Activate your existing Pro license</t></p>
						<a href="#bfg-license-form-section" class="bfg-link-green bfg-link-green--bold" id="bfg-activate-license-toggle">
							<t>Activate License</t>
						</a>
					</div>
				</div>

			</div>

			<!-- License Form (hidden, revealed on click) -->
			<div class="bfg-section" id="bfg-license-form-section" style="display:none">
				<bfg-section.header title="Activate Your License" description="Enter your 16-character license key to activate Pro."></bfg-section.header>
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

			<!-- Features Section -->
			<div class="bfg-pro-features-section">
				<div class="bfg-pro-features-section__header">
					<h3 class="bfg-pro-features-section__title"><t>Features</t></h3>
					<p class="bfg-pro-features-section__subtitle"><t>Available with Pro</t></p>
				</div>
				<div class="bfg-pro-feature-card-grid">
					<div class="bfg-pro-feature-card">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>MyBring Booking</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Book shipments directly from WooCommerce</t></span>
					</div>
					<div class="bfg-pro-feature-card">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Fixed Pricing</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Set custom shipping prices</t></span>
					</div>
					<div class="bfg-pro-feature-card">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Pickup Points</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Show pickup locations to customers</t></span>
					</div>
					<div class="bfg-pro-feature-card">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Shipping Labels</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Print labels automatically</t></span>
					</div>
					<div class="bfg-pro-feature-card">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Advanced Tracking</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Real-time shipment tracking</t></span>
					</div>
					<div class="bfg-pro-feature-card">
						<div class="bfg-pro-feature-card__icon">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
								stroke-linecap="round" stroke-linejoin="round">
								<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</div>
						<strong class="bfg-pro-feature-card__title"><t>Return Management</t></strong>
						<span class="bfg-pro-feature-card__desc"><t>Handle returns efficiently</t></span>
					</div>
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

					const toggle = document.getElementById('bfg-activate-license-toggle');
					const licenseSection = document.getElementById('bfg-license-form-section');
					if (toggle && licenseSection) {
						toggle.addEventListener('click', function (e) {
							e.preventDefault();
							licenseSection.style.display = licenseSection.style.display === 'none' ? '' : 'none';
							licenseSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
						});
					}
				});
			</script>
		<?php endif; ?>

	</div>
</div>
