<?php
/**
 * BFG Status Item Component
 *
 * Displays a single status item with a label and value.
 * Used within a status-card component.
 *
 * @param string $label The label text
 * @param string $value The value text
 *
 * Usage (standalone):
 * <bfg-status-item label="Status" value="Active"></bfg-status-item>
 *
 * Usage (nested in status-card):
 * <bfg-status-card>
 *     <bfg-status-item label="Status" value="Active"></bfg-status-item>
 * </bfg-status-card>
 *
 * Note: Use explicit closing tags when nesting components (not self-closing />)
 */
?>

<div class="bfg-pro-status-card__item">
	<span class="bfg-pro-status-card__label"><t>label</t></span>
	<span class="bfg-pro-status-card__value"><t>value</t></span>
</div>
