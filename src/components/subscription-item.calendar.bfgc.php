<?php
/*
 * BFG Subscription Item with Date
 *
 * Subscription item for displaying validity dates.
 *
 * Attributes:
 *   label  - The label text (displayed uppercase)
 *   value  - The date value text
 *   detail - Optional detail shown in parentheses after the value
 *
 * Usage:
 * <bfg-subscription-item.calendar label="VALID UNTIL" value="March 13, 2027" detail="365 days left"></bfg-subscription-item.calendar>
 */
?>

<div class="bfg-subscription__item <t>class</t>">
	<span class="bfg-subscription__label"><t>label</t></span>
	<span class="bfg-subscription__value"><t>value</t> <span class="bfg-subscription__detail bfgu:font-normal bfgu:opacity-60">(<t>detail</t>)</span></span>
</div>
