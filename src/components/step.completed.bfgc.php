<?php
/**
 * BFG Step - Completed
 *
 * A completed step row component with checkmark icon.
 *
 * @usage <bfg-step.completed href="#">Step Title<bfg-step-desc>Description</bfg-step-desc><bfg-badge.completed>Completed</bfg-badge.completed></bfg-step.completed>
 *
 * @param string $href Link URL (optional)
 */
?>

<div class="bfg-step bfg-step--completed">
	<div class="bfg-step__icon bfg-step__icon--completed">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
			<polyline points="20 6 9 17 4 12"></polyline>
		</svg>
	</div>
	<div class="bfg-step__content">
		<if :href>
		<a href=":href" class="bfg-step__link"><slot/></a>
		</if>
		<else>
		<slot/>
		</else>
	</div>
</div>