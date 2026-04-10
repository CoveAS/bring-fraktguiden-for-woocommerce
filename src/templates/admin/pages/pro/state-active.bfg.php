<?php
/**
 * Pro Page — Active State
 *
 * Rendered when the license is active and Pro is enabled.
 *
 * @var bool   $license_days_remaining
 * @var string $valid_to_formatted
 * @var string $license_key
 */
?>

<!-- License Active Card -->
<div class="bfg-section bfg-license-info-section">
	<div class="bfg-free-card bfg-license-active-card">
		<div class="bfg-pro-upsell-card__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/>
				<path d="M5 21h14"/>
			</svg>
		</div>
		<div class="bfg-pro-upsell-card__body">
			<h2 class="bfg-complete-card__title"><t>Pro License Active</t></h2>
			<p class="bfg-pro-upsell-card__desc"><t>All features are available. Your configurations are active and running.</t></p>
			<bfg-subscription-info class="bfg-subscription--active bfg-subscription--grid3">
				<bfg-subscription-item label="LICENSE KEY" :value="$license_key ? esc_html( $license_key ) : ''"></bfg-subscription-item>
				<bfg-subscription-item.status label="STATUS" value="Active" color="green"></bfg-subscription-item.status>
				<?php if ($valid_to_formatted): ?>
				<bfg-subscription-item.calendar label="VALID UNTIL" :value="esc_html( $valid_to_formatted )" :detail="$license_days_remaining > 0 ? sprintf( '%d days left', $license_days_remaining ) : ''"></bfg-subscription-item.calendar>
				<?php endif; ?>
			</bfg-subscription-info>
			<div class="bfg-pro-upsell-card__ctas">
				<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--text">
					<t>Manage license</t>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round">
						<line x1="7" y1="17" x2="17" y2="7"></line>
						<polyline points="7 7 17 7 17 17"></polyline>
					</svg>
				</a>
			</div>
		</div>
	</div>
</div>
