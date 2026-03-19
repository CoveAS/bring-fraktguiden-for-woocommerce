<?php
/**
 * BFG Conditional Field Group Component
 *
 * Renders a group of fields that can be enabled/disabled based on a trigger checkbox.
 *
 * @usage
 *   <bfg-conditional-field-group id="my-fields" trigger="enable_feature">
 *     <div class="bfg-field">
 *       ... fields to conditionally show ...
 *     </div>
 *   </bfg-conditional-field-group>
 *
 * @param string $id Required. Unique identifier for the group.
 * @param string $trigger Required. Name attribute of the trigger checkbox.
 */
?>

<div class="bfg-conditional-group" id=":id" data-trigger=":trigger" data-bfg-init="conditionalGroup">
	<slot />
</div>
