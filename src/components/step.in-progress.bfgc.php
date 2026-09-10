<?php
/**
 * BFG Step - In Progress
 *
 * An in-progress step row component with number badge.
 *
 * @usage <bfg-step.in-progress href="#" number="2" label="Step Title"><bfg-step-desc>Description</bfg-step-desc></bfg-step.in-progress>
 *
 * @param string $href Link URL (optional)
 * @param string $number Step number
 * @param string $label Step title
 */
?>

<div class="bfg-step bfg-step--in-progress">
	<div class="bfg-step__icon bfg-step__icon--number"><t>number</t></div>
	<div class="bfg-step__content">
		<a href=":href" class="bfg-step__link"><t>label</t></a>
		<slot></slot>
	</div>
</div>
