<?php
/**
 * BFG Field - Checkbox
 *
 * A checkbox field component with title and optional description.
 *
 * @usage <bfg-field.checkbox name="demo" value="1" title="Enable this" description="Help text" />
 * @usage <bfg-field.checkbox name="demo" value="1" title="Checked" checked />
 *
 * @param string $name Input name
 * @param string $value Input value
 * @param string $title Checkbox title
 * @param string $description Help text (optional)
 * @param bool $checked Whether checkbox is checked (optional)
 */
?>

<div class="bfg-field bfg-field--checkbox-box">
	<div class="bfg-input bfg-input--checkbox">
		<label>
			<input type="checkbox" name=":name" value=":value">
			<div class="bfg-checkbox-content">
				<span class="bfg-checkbox-title"><t>title</t></span>
				<if :description>
				<p class="bfg-checkbox-desc"><t>description</t></p>
				</if>
			</div>
		</label>
	</div>
</div>
