<?php
/**
 * BFG Field - Number Input
 *
 * A number input field component with label and optional suffix.
 *
 * @usage <bfg-field.number id="demo" name="demo" label="Price" value="100" suffix="NOK" />
 * @usage <bfg-field.number id="demo" name="demo" label="Price" value="100" suffix-lg="NOK" />
 *
 * @param string $id Input ID
 * @param string $name Input name
 * @param string $label Field label
 * @param string $value Input value
 * @param string $suffix Small suffix text (optional)
 * @param string $suffix-lg Large suffix text (optional)
 */
?>

<div class="bfg-field">
	<label for=":id"><t>label</t></label>
	<div class="bfg-input bfg-input--number">
		<input type="number" id=":id" name=":name" value=":value">
		<if :suffix>
		<span class="bfg-suffix"><t>suffix</t></span>
		</if>
		<if :suffix-lg>
		<span class="bfg-suffix-lg"><t>suffix-lg</t></span>
		</if>
	</div>
</div>
