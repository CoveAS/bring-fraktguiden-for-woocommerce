<?php
/**
 * Pro Page — Active State
 *
 * Rendered when the license is active and Pro is enabled.
 *
 * @var int    $license_days_remaining
 * @var string $valid_to_formatted
 * @var string $license_key
 */
?>

<!-- License Active -->
<div class="bfg-section bfg-pro-free-state">

	<!-- Banner: active status -->
	<div class="bfg-free-card bfg-active-notice">
		<div class="bfg-active-notice__body">
			<strong class="bfg-active-notice__title"><t>Pro is active</t></strong>
			<p class="bfg-active-notice__desc"><t>All Pro features are enabled on your live site</t></p>
		</div>
	</div>

	<!-- License Card -->
	<bfg-license-card
		title="Pro License"
		:subtitle="$valid_to_formatted ? sprintf( __( 'Active until %s', 'bring-fraktguiden-for-woocommerce' ), esc_html( $valid_to_formatted ) ) : ''"
		status="Active"
		status-color="green"
		:license-key="$license_key ? esc_html( $license_key ) : ''"
		:days="$license_days_remaining > 0 ? sprintf( __( '%d days remaining', 'bring-fraktguiden-for-woocommerce' ), $license_days_remaining ) : ''"
		manage-url="https://bringfraktguiden.no/"
		manage-label="Manage License">
	</bfg-license-card>

</div>
