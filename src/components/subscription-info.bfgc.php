<?php
/**
 * BFG Subscription Info Component
 *
 * A container for displaying subscription status information.
 * Groups subscription items (status, validity) in a 2-column grid.
 *
 * Usage:
 * <bfg-subscription-info class="bfg-pro-status-card--trial">
 *     <bfg-subscription-item label="Status" value="Trial"></bfg-subscription-item>
 *     <bfg-subscription-item label="Days Remaining" value="7"></bfg-subscription-item>
 * </bfg-subscription-info>
 *
 * Note: Use explicit closing tags for nested components (not self-closing />)
 * Note: Pass type modifiers via class attribute (e.g., class="bfg-pro-status-card--trial")
 */
?>

<div class="bfg-pro-status-card">
	<slot />
</div>
