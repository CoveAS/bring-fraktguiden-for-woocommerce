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

<!-- License Active Card -->
<div class="bfg-section bfg-license-info-section">
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
