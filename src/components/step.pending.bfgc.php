<?php
/**
 * BFG Step - Pending
 *
 * A pending step row component with number badge.
 *
 * @usage <bfg-step.pending href="#" number="3">Step Title<bfg-step-desc>Description</bfg-step-desc></bfg-step.pending>
 *
 * @param string $href Link URL (optional)
 * @param string $number Step number
 */
?>

<div class="bfg-step bfg-step--pending">
	<div class="bfg-step__icon bfg-step__icon--number"><t>number</t></div>
	<div class="bfg-step__content">
		<if :href>
		<a href=":href" class="bfg-step__link"><slot/></a>
		</if>
		<else>
		<slot/>
		</else>
	</div>
</div>