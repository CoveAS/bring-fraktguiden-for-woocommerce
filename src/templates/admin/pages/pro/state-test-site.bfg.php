<?php
/**
 * Pro Page — Test Site State
 *
 * Rendered when the site is flagged as a test environment with Pro enabled.
 */
?>

<!-- Test Environment Active -->
<div class="bfg-section bfg-pro-free-state">

	<!-- Banner: test mode status -->
	<div class="bfg-free-card bfg-test-notice">
		<div class="bfg-test-notice__body">
			<strong class="bfg-test-notice__title"><t>Test Mode Active</t></strong>
			<p class="bfg-test-notice__desc"><t>Pro features are enabled for testing. A license is required for production use.</t></p>
		</div>
	</div>

	<!-- License Card -->
	<bfg-license-card
		title="Pro License"
		subtitle="PRO features are enabled for testing. A license is required for production use."
		status="Test Mode"
		status-color="gray"
		days=""
		manage-url="https://bringfraktguiden.no/"
		manage-label="Purchase License">
	</bfg-license-card>

</div>
