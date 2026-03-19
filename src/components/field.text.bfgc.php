<?php
/**
 * BFG Field - Text Input
 *
 * A text input field component with label and optional description.
 * The input element is provided via slot for maximum flexibility.
 *
 * @usage
 *   <bfg-field.text id="demo" label="Text Input" description="Help text">
 *     <input type="text" id="demo" name="demo" placeholder="Enter text" />
 *   </bfg-field.text>
 *
 * @param string $id Input ID (used for label's for attribute and description's id)
 * @param string $label Field label
 * @param string $description Help text (optional)
 */
?>

<div class="bfg-field">
	<label for=":id"><t>label</t></label>
	<slot></slot>
	<if :description>
	<p class="bfg-description"><t>description</t></p>
	</if>
</div>