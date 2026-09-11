<?php
/**
 * BFG Step - In Progress
 *
 * An in-progress step row component with number badge.
 *
 * @usage <bfg-step.in-progress number="2">Step Title<bfg-step-desc>Description</bfg-step-desc></bfg-step.in-progress>
 *
 * @param string $number Step number
 */
?>

<div class="bfg-step bfg-step--in-progress">
	<div class="bfg-step__icon bfg-step__icon--number"><t>number</t></div>
	<div class="bfg-step__content"><slot></slot></div>
</div>
