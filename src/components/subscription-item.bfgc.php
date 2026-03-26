<?php
/**
 * BFG Subscription Item Component
 *
 * Displays a single subscription info item with a label and value.
 * Used within a subscription-info component.
 *
 * For items with icons, use the variant components:
 * - <bfg-subscription-item.lock> for lock icon (license status)
 * - <bfg-subscription-item.calendar> for calendar icon (validity dates)
 *
 * @param string $label The label text
 * @param string $value The value text
 *
 * Usage (nested in subscription-info):
 * <bfg-subscription-info>
 *     <bfg-subscription-item label="Status" value="Active"></bfg-subscription-item>
 * </bfg-subscription-info>
 *
 * Note: Use explicit closing tags when nesting components (not self-closing />)
 */
?>

<div class="bfg-pro-status-card__item">
	<span class="bfg-pro-status-card__label"><t>label</t></span>
	<span class="bfg-pro-status-card__value"><t>value</t></span>
</div>
