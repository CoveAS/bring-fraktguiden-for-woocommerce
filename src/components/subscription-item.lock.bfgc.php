<?php
/**
 * BFG Subscription Item with Lock Icon
 *
 * Subscription item with lock icon for displaying license status.
 *
 * @param string $label The label text (displayed uppercase)
 * @param string $value The value text
 * @param string $detail Optional detail text below value
 *
 * Usage:
 * <bfg-subscription-item.lock label="LICENSE STATUS" value="Active" detail="License: PRO-2026-XXXX"></bfg-subscription-item.lock>
 */
?>

<div class="bfg-subscription__item <t>class</t>">
	<div class="bfg-subscription__label">
		<svg class="bfg-subscription__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
			<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
		</svg>
		<t>label</t>
	</div>
	<span class="bfg-subscription__value"><t>value</t></span>
	<span class="bfg-subscription__detail"><t>detail</t></span>
</div>
