<?php

use BringFraktguiden\Admin\FieldRenderer;

// Feature card state — active (blue, linked) when Pro is fully enabled
$bfg_cards_active   = ($license_active && $pro_enabled) || ($is_test_site && $pro_enabled);
$bfg_booking_url    = $bfg_cards_active ? esc_url(admin_url('admin.php?page=bring_fraktguiden_booking')) : '';
$bfg_shipping_url   = $bfg_cards_active ? esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')) : '';
$bfg_fallback_url   = $bfg_cards_active ? esc_url(admin_url('admin.php?page=bring_fraktguiden_fallback')) : '';
$bfg_settings_url   = $bfg_cards_active ? esc_url(admin_url('admin.php?page=bring_fraktguiden_settings')) : '';

if ($license_active && $pro_enabled) {
	$bfg_features_subtitle = __('Click to configure each feature', 'bring-fraktguiden-for-woocommerce');
} elseif ($is_trial) {
	$bfg_features_subtitle = __('All features active during your trial', 'bring-fraktguiden-for-woocommerce');
} elseif ($is_expired) {
	$bfg_features_subtitle = __('Purchase a license to regain access', 'bring-fraktguiden-for-woocommerce');
} elseif ($is_test_site && $pro_enabled) {
	$bfg_features_subtitle = __('PRO features enabled for testing', 'bring-fraktguiden-for-woocommerce');
} else {
	$bfg_features_subtitle = __('Available with Pro', 'bring-fraktguiden-for-woocommerce');
}

/**
 * Pro Page Template
 *
 * @var bool $is_test_site
 * @var bool $license_active
 * @var bool $pro_enabled
 * @var bool $pro_activated
 * @var int $days_remaining
 * @var int $license_days_remaining
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
				<div class="bfg-free-card bfg-complete-card bfg-license-active-card">
					<div class="bfg-complete-card__icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="7.5" cy="15.5" r="5.5"/>
							<path d="m21 2-9.6 9.6"/>
							<path d="m15.5 7.5 3 3L22 7l-3-3"/>
						</svg>
					</div>
					<div class="bfg-complete-card__body">
						<h3 class="bfg-complete-card__title"><t>License Details</t></h3>
						<p class="bfg-complete-card__desc"><t>Your active Pro license</t></p>
						<bfg-subscription-info class="bfg-subscription--active" style="margin-top: 12px;">
							<bfg-subscription-item.lock label="LICENSE STATUS" value="Active" :detail="$license_key ? 'License: ' . esc_html( $license_key ) : ''"></bfg-subscription-item.lock>
							<?php if ($valid_to_formatted): ?>
							<bfg-subscription-item.calendar label="VALID UNTIL" :value="esc_html( $valid_to_formatted )" :detail="$license_days_remaining > 0 ? sprintf( '%d days remaining', $license_days_remaining ) : ''"></bfg-subscription-item.calendar>
							<?php endif; ?>
						</bfg-subscription-info>

						<div class="bfg-license-active-card__actions">
							<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--text">
								<?php esc_html_e('Manage license', 'bring-fraktguiden-for-woocommerce'); ?>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<line x1="5" y1="12" x2="19" y2="12"></line>
									<polyline points="12 5 19 12 12 19"></polyline>
								</svg>
							</a>
						</div>
					</div>
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
							<div class="bfg-pro-license-form__row" style="display:flex; gap: 0.5rem; align-items: center; width: 100%;">
								<div style="flex: 1; min-width: 0;"><?php FieldRenderer::test_url(); ?></div>
								<button type="submit" class="bfg-btn bfg-btn--primary" style="flex-shrink:0;">
									<t>Activate</t>
								</button>
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
							<div class="bfg-pro-license-form__row" style="display:flex; gap: 0.5rem; align-items: center; width: 100%;">
								<div style="flex: 1; min-width: 0;"><?php FieldRenderer::test_url(); ?></div>
								<button type="submit" class="bfg-btn bfg-btn--primary" style="flex-shrink:0;">
									<t>Activate</t>
								</button>
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
						<a href="#" class="bfg-btn bfg-btn--text" id="bfg-activate-license-toggle">
							<t>Activate License</t>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<line x1="5" y1="12" x2="19" y2="12"></line>
								<polyline points="12 5 19 12 12 19"></polyline>
							</svg>
						</a>

						<!-- License Form (inline, revealed on click) -->
						<div id="bfg-license-form-section" style="display:none; margin-top: 1.25rem;">
							<form method="post" action="options.php" id="bfg-license-form-pro">
								<?php settings_fields('bring_fraktguiden_pro'); ?>
								<div style="display:none">
									<?php FieldRenderer::pro_enabled(); ?>
								</div>
								<div class="bfg-field">
									<label class="bfg-field__label">
										<t>Enter Your License Key</t>
									</label>
									<div class="bfg-pro-license-form__row" style="display:flex; gap: 0.5rem; align-items: center; width: 100%;">
										<div style="flex: 1; min-width: 0;"><?php FieldRenderer::test_url(); ?></div>
										<button type="submit" class="bfg-btn bfg-btn--primary" style="flex-shrink:0;">
											<t>Activate</t>
										</button>
									</div>
									<p class="bfg-description">
										<t>Your license key is a 16-character code you received after purchase</t>
									</p>
								</div>
							</form>
						</div>
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
							toggle.style.display = 'none';
							licenseSection.style.display = '';
							licenseSection.querySelector('input').focus();
						});
					}

				});
			</script>
		<?php endif; ?>

		<!-- Shared license key validation — applies to whichever state renders #bfg-license-form-pro -->
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const licenseForm = document.getElementById('bfg-license-form-pro');
				const licenseInput = licenseForm ? licenseForm.querySelector('input[type="text"], input:not([type])') : null;

				if (!licenseForm || !licenseInput) return;

				licenseInput.addEventListener('input', function () {
					const raw = licenseInput.value;
					if (!raw) { bfgField.clearError(licenseInput); return; }
					if (!/^[A-Za-z0-9-]*$/.test(raw)) {
						bfgField.showError(licenseInput, 'Only letters and numbers are allowed.');
						return;
					}
					if (raw.replace(/-/g, '').length > 16) {
						bfgField.showError(licenseInput, 'License key must be 16 characters.');
						return;
					}
					bfgField.clearError(licenseInput);
				});

				licenseForm.addEventListener('submit', function (e) {
					const raw = licenseInput.value.trim();
					if (!raw) {
						e.preventDefault();
						bfgField.showError(licenseInput, 'Please enter your license key.');
						return;
					}
					const normalized = raw.replace(/-/g, '');
					if (!/^[A-Za-z0-9]+$/.test(normalized)) {
						e.preventDefault();
						bfgField.showError(licenseInput, 'Only letters and numbers are allowed.');
						return;
					}
					if (normalized.length !== 16) {
						e.preventDefault();
						bfgField.showError(licenseInput, 'License key must be 16 characters.');
						return;
					}
				});
			});
		</script>

		<!-- Features Grid — shared across all states -->
		<div class="bfg-pro-features-section">
			<div class="bfg-pro-features-section__header">
				<h3 class="bfg-pro-features-section__title"><t>Features</t></h3>
				<p class="bfg-pro-features-section__subtitle"><?php echo esc_html($bfg_features_subtitle); ?></p>
			</div>
			<div class="bfg-pro-feature-card-grid">

				<!-- MyBring Booking -->
				<bfg-feature-card :href="$bfg_booking_url" :active="$bfg_cards_active">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
							<line x1="16" y1="2" x2="16" y2="6"></line>
							<line x1="8" y1="2" x2="8" y2="6"></line>
							<line x1="3" y1="10" x2="21" y2="10"></line>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>MyBring Booking</t></strong>
					<span class="bfg-feature-card__desc"><t>Book shipments and print labels directly from WooCommerce orders</t></span>
					<bfg-feature-card.benefits>
						<li><t>Book directly from order view</t></li>
						<li><t>Print Bring shipping labels</t></li>
						<li><t>Automatic tracking number</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- Pickup Points -->
				<bfg-feature-card :href="$bfg_shipping_url" :active="$bfg_cards_active">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
							<circle cx="12" cy="10" r="3"></circle>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>Pickup Points</t></strong>
					<span class="bfg-feature-card__desc"><t>Let customers choose their preferred Bring pickup location at checkout</t></span>
					<bfg-feature-card.benefits>
						<li><t>Choose pickup location at checkout</t></li>
						<li><t>Lockers and post offices</t></li>
						<li><t>Location lookup by postal code</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- Fixed Pricing -->
				<bfg-feature-card :href="$bfg_shipping_url" :active="$bfg_cards_active">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<line x1="12" y1="1" x2="12" y2="23"></line>
							<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>Fixed Pricing</t></strong>
					<span class="bfg-feature-card__desc"><t>Override Bring API rates with your own fixed prices per shipping service</t></span>
					<bfg-feature-card.benefits>
						<li><t>Set price per shipping method</t></li>
						<li><t>Per-zone price rules</t></li>
						<li><t>Cart weight-based pricing</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- Free Shipping -->
				<bfg-feature-card :href="$bfg_shipping_url" :active="$bfg_cards_active">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M20 12V22H4V12"></path>
							<path d="M22 7H2v5h20V7z"></path>
							<path d="M12 22V7"></path>
							<path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
							<path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>Free Shipping</t></strong>
					<span class="bfg-feature-card__desc"><t>Offer free shipping when cart meets a minimum order value</t></span>
					<bfg-feature-card.benefits>
						<li><t>Set order value threshold</t></li>
						<li><t>Per-service configuration</t></li>
						<li><t>Combine with fixed pricing</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- Fallback Pricing -->
				<bfg-feature-card :href="$bfg_fallback_url" :active="$bfg_cards_active">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
							<polyline points="2 17 12 22 22 17"></polyline>
							<polyline points="2 12 12 17 22 12"></polyline>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>Fallback Pricing</t></strong>
					<span class="bfg-feature-card__desc"><t>Keep checkout working with fixed fallback rates when the Bring API is unavailable</t></span>
					<bfg-feature-card.benefits>
						<li><t>Prevent checkout errors</t></li>
						<li><t>Fallback rate per service</t></li>
						<li><t>Always show shipping options</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- PRO Settings -->
				<bfg-feature-card :href="$bfg_settings_url" :active="$bfg_cards_active">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="3"></circle>
							<path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>PRO Settings</t></strong>
					<span class="bfg-feature-card__desc"><t>Advanced configuration for customer numbers, service names and display options</t></span>
					<bfg-feature-card.benefits>
						<li><t>Multiple customer numbers</t></li>
						<li><t>Custom service display names</t></li>
						<li><t>Estimated delivery display</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

			</div>
		</div>

	</div>
</div>
