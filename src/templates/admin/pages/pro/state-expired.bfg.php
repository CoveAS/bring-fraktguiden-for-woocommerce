<?php
/**
 * Pro Page — Expired State
 *
 * Rendered when a license was previously active but has since expired.
 */

use BringFraktguiden\Admin\FieldRenderer;
?>

<!-- License Expired Card -->
<div class="bfg-section bfg-license-info-section">
	<div class="bfg-free-card bfg-license-expired-card">
		<div class="bfg-pro-upsell-card__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
				<path d="M21 3v5h-5"/>
				<path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
				<path d="M8 16H3v5"/>
			</svg>
		</div>
		<div class="bfg-pro-upsell-card__body">
			<h2 class="bfg-complete-card__title"><t>License Expired</t></h2>
			<p class="bfg-pro-upsell-card__desc"><t>Your configurations are preserved. Renew your license to reactivate all Pro features.</t></p>
			<div class="bfg-pro-upsell-card__ctas">
				<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--primary">
					<t>Renew License</t>
				</a>
			</div>
		</div>
	</div>
</div>

<bfg-pro-license-form title="Have a new license key?" description="Enter it below to activate your renewed license."></bfg-pro-license-form>
