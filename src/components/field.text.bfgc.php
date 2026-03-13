<?php
/**
 * BFG Field - Text Input
 *
 * A text input field component with label and optional description.
 *
 * @usage <bfg-field.text id="demo" name="demo" label="Text Input" placeholder="Enter text" description="Help text" />
 *
 * @param string $id Input ID
 * @param string $name Input name
 * @param string $label Field label
 * @param string $value Input value (optional)
 * @param string $placeholder Placeholder text (optional)
 * @param string $description Help text (optional)
 */
?>

<div class="bfg-field">
	<label for=":id"><t>label</t></label>
	<input type="text" id=":id" name=":name" value=":value" placeholder=":placeholder">
	<if :description>
	<p class="bfg-description"><t>description</t></p>
	</if>
</div>
