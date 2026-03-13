<?php
/**
 * BFG Progress Component
 *
 * A progress bar component with label badge and visual progress indicator.
 *
 * @usage <bfg-progress label="3 of 5 completed" percentage="60" />
 *
 * @param string $label Progress label text
 * @param string $percentage Progress percentage (0-100)
 */
?>

<div class="bfg-progress-container">
	<span class="bfg-progress-badge"><t>label</t></span>
	<div class="bfg-progress-bar-new">
		<div class="bfg-progress-bar-fill" style=":width-style"></div>
	</div>
</div>
