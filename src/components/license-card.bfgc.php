<?php
/**
 * BFG License Card Component
 *
 * A clean card displaying license details: title/subtitle, status badge,
 * license key pill, days remaining, and a manage link.
 *
 * @param string $title        Card heading (e.g. "Pro License")
 * @param string $subtitle     Sub-heading (e.g. "Active until April 21, 2026")
 * @param string $status       Status badge label (e.g. "Active")
 * @param string $status-color Dot/badge color: "green" (default), "red", "gray"
 * @param string $license-key  The license key string (e.g. "XXXX–XXXX–AB3F")
 * @param string $days         Footer left text (e.g. "11 days remaining")
 * @param string $manage-url   Href for the action link
 * @param string $manage-label Link label (e.g. "Manage License", "Renew")
 *
 * Usage:
 * <bfg-license-card
 *     title="Pro License"
 *     subtitle="Active until April 21, 2026"
 *     status="Active"
 *     status-color="green"
 *     license-key="XXXX–XXXX–AB3F"
 *     days="11 days remaining"
 *     manage-url="https://example.com">
 * </bfg-license-card>
 */
?>

<div class="bfg-license-card">
	<div class="bfg-license-card__body">
		<div class="bfg-license-card__header">
			<div>
				<div class="bfg-license-card__title"><t>title</t></div>
				<div class="bfg-license-card__subtitle"><t>subtitle</t></div>
			</div>
			<div class="bfg-license-card__status" data-color=":status-color">
				<span class="bfg-license-card__dot"></span>
				<t>status</t>
			</div>
		</div>
		<div class="bfg-license-card__key-section">
			<div class="bfg-license-card__key-label"><?php esc_html_e( 'License Key', 'bring-fraktguiden-for-woocommerce' ); ?></div>
			<if :license-key>
				<div class="bfg-license-card__key-value"><t>license-key</t></div>
			</if>
			<else>
				<div class="bfg-license-card__key-value bfg-license-card__key-value--placeholder">●●●● – ●●●● – ●●●●</div>
			</else>
		</div>
	</div>
	<div class="bfg-license-card__footer">
		<span class="bfg-license-card__days"><t>days</t></span>
		<a class="bfg-btn bfg-btn--text bfg-license-card__manage" href=":manage-url">
			<t>manage-label</t>
			<svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M2 6H10M10 6L6.5 2.5M10 6L6.5 9.5"/>
			</svg>
		</a>
	</div>
</div>
