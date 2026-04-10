<?php
/**
 * Pro Page — Test Site State
 *
 * Rendered when the site is flagged as a test environment with Pro enabled.
 */
?>

<!-- Test Environment Active -->
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
