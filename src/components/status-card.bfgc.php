<?php
/**
 * BFG Status Card Component
 *
 * A container for displaying status information in a card format.
 * Used to group related status items together. Supports nested status-item components.
 *
 * Usage:
 * <bfg-status-card class="bfg-pro-status-card--trial">
 *     <bfg-status-item label="Status" value="Trial"></bfg-status-item>
 *     <bfg-status-item label="Days Remaining" value="7"></bfg-status-item>
 * </bfg-status-card>
 *
 * Note: Use explicit closing tags for nested components (not self-closing />)
 * Note: Pass type modifiers via class attribute (e.g., class="bfg-pro-status-card--trial")
 */
?>

<div class="bfg-pro-status-card">
	<slot />
</div>
