<?php
/**
 * BFG Step - Action
 *
 * A step row whose action changes data. The row is not a link, so the slot
 * holds a form with a submit button.
 *
 * @usage <bfg-step.action number="7" label="Step Title"><bfg-step-desc>Description</bfg-step-desc></bfg-step.action>
 *
 * @param string $number Step number
 * @param string $label Step title
 */
?>

<div class="bfg-step bfg-step--action">
	<div class="bfg-step__icon bfg-step__icon--number"><t>number</t></div>
	<div class="bfg-step__content">
		<span class="bfg-step__label"><t>label</t></span>
		<slot></slot>
	</div>
</div>
