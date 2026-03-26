<?php
/**
 * BFG Subscription Item with Calendar Icon
 *
 * Subscription item with calendar icon for displaying validity dates.
 *
 * @param string $label The label text (displayed uppercase)
 * @param string $value The value text
 * @param string $detail Optional detail text below value
 *
 * Usage:
 * <bfg-subscription-item.calendar label="VALID UNTIL" value="March 13, 2027" detail="365 days remaining"></bfg-subscription-item.calendar>
 */
?>

<div class="bfg-pro-status-card__item">
	<div class="bfg-pro-status-card__label">
		<svg class="bfg-pro-status-card__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
			<line x1="16" y1="2" x2="16" y2="6"></line>
			<line x1="8" y1="2" x2="8" y2="6"></line>
			<line x1="3" y1="10" x2="21" y2="10"></line>
		</svg>
		<t>label</t>
	</div>
	<span class="bfg-pro-status-card__value"><t>value</t></span>
	<span class="bfg-pro-status-card__detail"><t>detail</t></span>
</div>
