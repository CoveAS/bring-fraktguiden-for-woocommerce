<?php
/**
 * Pro Page — Trial State
 *
 * Rendered when Pro is enabled in trial mode (activated but no paid license).
 *
 * @var int $days_remaining
 */

use BringFraktguiden\Admin\FieldRenderer;
?>

<!-- Trial Active -->
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

<bfg-pro-license-form title="Activate Your License" description="Already have a license? Enter it below to activate."></bfg-pro-license-form>
