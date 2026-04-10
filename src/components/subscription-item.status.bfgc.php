<?php
/**
 * BFG Subscription Item with Status Dot
 *
 * Subscription item with a colored dot indicator for displaying active/inactive status.
 *
 * @param string $label The label text (displayed uppercase)
 * @param string $value The value text
 * @param string $color Dot color: "green" (default), "red", "gray"
 *
 * Usage:
 * <bfg-subscription-item.status label="STATUS" value="Active" color="green"></bfg-subscription-item.status>
 */
?>

<div class="bfg-subscription__item <t>class</t>">
	<span class="bfg-subscription__label bfg-subscription__label--status"><t>label</t></span>
	<span class="bfg-subscription__value bfg-subscription__value--status bfg-subscription__value--<t>color</t>">
		<span class="bfg-subscription__dot"></span>
		<t>value</t>
	</span>
</div>
