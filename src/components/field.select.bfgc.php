<?php
/**
 * BFG Field - Select Input
 *
 * A select dropdown field component with label and optional description.
 *
 * @usage <bfg-field.select id="demo" name="demo" label="Select Input" description="Help text" />
 *
 * Note: Options must be added as slot content in the source template.
 * The component framework doesn't support complex data structures like arrays yet.
 *
 * @param string $id Input ID
 * @param string $name Input name
 * @param string $label Field label
 * @param string $value Selected value (optional)
 * @param string $placeholder Placeholder text (optional)
 * @param string $description Help text (optional)
 */
?>

<div class="bfg-field">
	<label for=":id">
		<t>label</t>
	</label>
	<div class="bfg-input bfg-input--select">
		<select id=":id" name=":name" class="bfg-custom-select">
			<if :placeholder>
				<option value="" disabled selected>
					<t>placeholder</t>
				</option>
			</if>
			<slot></slot>
		</select>
	</div>
	<if :description>
		<p class="bfg-description">
			<t>description</t>
		</p>
	</if>
</div>