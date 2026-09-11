<?php

/**
 * Pro Page Template
 *
 * All variables are prepared in SettingsPage::pro_page().
 *
 * @var bool   $is_test_site
 * @var bool   $license_active
 * @var bool   $pro_enabled
 * @var bool   $pro_activated
 * @var int    $days_remaining
 * @var int    $license_days_remaining
 * @var int|false $pro_activated_on
 * @var bool   $is_trial
 * @var bool   $is_expired
 * @var string $valid_to_formatted
 * @var string $license_key
 * @var int    $shipmentsThisMonth
 * @var int    $activeShippingMethodsCount
 * @var bool   $bfg_cards_active
 * @var bool   $bfg_cards_expired
 * @var string $bfg_booking_url
 * @var string $bfg_shipping_url
 * @var string $bfg_fallback_url
 * @var string $bfg_settings_url
 * @var string $bfg_features_subtitle
 */
?>

<div class="wrap bfg bfg-admin-page bfg-admin-page__pro">
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
			<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/pro/state-active.php'; ?>
		<?php elseif ($is_expired): ?>
			<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/pro/state-expired.php'; ?>
		<?php elseif ($is_trial): ?>
			<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/pro/state-trial.php'; ?>
		<?php elseif ($is_test_site && $pro_enabled): ?>
			<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/pro/state-test-site.php'; ?>
		<?php else: ?>
			<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/pro/state-free.php'; ?>
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
				<bfg-feature-card :href="$bfg_booking_url" :active="$bfg_cards_active" :expired="$bfg_cards_expired">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
							<line x1="16" y1="2" x2="16" y2="6"></line>
							<line x1="8" y1="2" x2="8" y2="6"></line>
							<line x1="3" y1="10" x2="21" y2="10"></line>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>MyBring Booking</t><sup>1</sup></strong>
					<span class="bfg-feature-card__desc"><t>Book shipments and print labels directly from WooCommerce orders</t></span>
					<bfg-feature-card.benefits>
						<li><t>Book directly from order view</t></li>
						<li><t>Print Bring shipping labels</t></li>
						<li><t>Automatic tracking number</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- Pickup Points -->
				<bfg-feature-card :href="$bfg_shipping_url" :active="$bfg_cards_active" :expired="$bfg_cards_expired">
					<bfg-feature-card.icon>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
							<circle cx="12" cy="10" r="3"></circle>
						</svg>
					</bfg-feature-card.icon>
					<strong class="bfg-feature-card__title"><t>Pickup Points</t><sup>2</sup></strong>
					<span class="bfg-feature-card__desc"><t>Let customers choose their preferred Bring pickup location at checkout</t></span>
					<bfg-feature-card.benefits>
						<li><t>Choose pickup location at checkout</t></li>
						<li><t>Lockers and post offices</t></li>
						<li><t>Location lookup by postal code</t></li>
					</bfg-feature-card.benefits>
				</bfg-feature-card>

				<!-- Fixed Pricing -->
				<bfg-feature-card :href="$bfg_shipping_url" :active="$bfg_cards_active" :expired="$bfg_cards_expired">
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
				<bfg-feature-card :href="$bfg_shipping_url" :active="$bfg_cards_active" :expired="$bfg_cards_expired">
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
				<bfg-feature-card :href="$bfg_fallback_url" :active="$bfg_cards_active" :expired="$bfg_cards_expired">
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
				<bfg-feature-card :href="$bfg_settings_url" :active="$bfg_cards_active" :expired="$bfg_cards_expired">
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

		<div class="bfg-page__footer-notes" style="border-top: 1px solid var(--bfg-border); display: flex; flex-direction: column; gap: 0.375rem;">
			<small style="color: var(--bfg-text-muted);"><sup>1</sup>
				<t>Domestic shipments only. We're working on building support for international shipping.</t>
			</small>
			<small style="color: var(--bfg-text-muted);"><sup>2</sup>
				<t>List of currently supported services for using pickup point: Pickup parcel (5800), Pakke til
					Pakkeboks (5801), Express next day (4850), Business parcel (5000), Norgespakke (3067), PICKUP_PARCEL
					and PICKUP_PARCEL_BULK</t>
			</small>
		</div>

	</div>
</div>
