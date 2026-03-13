<?php
/**
 * BFG Status Card Component
 *
 * A container for displaying status information in a card format.
 * Used to group related status items together.
 *
 * @param string $type Optional type modifier (e.g., "trial", "default")
 *
 * Usage:
 * <bfg-status-card type="trial">
 *     <bfg-status-item label="Status" value="Trial" type="trial" />
 *     <bfg-status-item label="Days Remaining" value="7" type="default" />
 * </bfg-status-card>
 */
?>

<div class="bfg-pro-status-card<if :type> bfg-pro-status-card--:type</if>">
	<slot />
</div>
