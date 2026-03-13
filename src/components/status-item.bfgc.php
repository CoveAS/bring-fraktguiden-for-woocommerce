<?php
/**
 * BFG Status Item Component
 *
 * Displays a single status item with a label and value.
 * Used within a status-card component.
 *
 * @param string $label The label text
 * @param string $value The value text
 * @param string $type Optional type modifier for styling (e.g., "success", "trial", "default")
 *
 * Usage:
 * <bfg-status-item label="Status" value="Active" type="success" />
 */
?>

<div class="bfg-pro-status-card__item">
	<span class="bfg-pro-status-card__label"><t>:label</t></span>
	<span class="bfg-pro-status-card__value<if :type> bfg-pro-status-card__value--:type</if>"><t>:value</t></span>
</div>
