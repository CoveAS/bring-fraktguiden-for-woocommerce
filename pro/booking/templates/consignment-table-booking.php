<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 * @var Bring_Consignment $consignment
 * @var Bring_WC_Order_Adapter $order
 */

use BringFraktguidenPro\Booking\Views\Bring_Booking_Labels;
use BringFraktguidenPro\Booking\Consignment\Bring_Consignment;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

$consignment_number = $consignment->get_consignment_number();
$tracking           = $consignment->get_tracking_link();
$date_and_times     = $consignment->get_dates();
$earliest_pickup    = $date_and_times['earliestPickup'] ? date_i18n( wc_date_format(), $date_and_times['earliestPickup'] / 1000 ) : 'N/A';
$expected_delivery  = $date_and_times['expectedDelivery'] ? date_i18n( wc_date_format(), $date_and_times['expectedDelivery'] / 1000 ) : 'N/A';
$packages           = $consignment->get_packages();
$labels_url         = Bring_Booking_Labels::create_download_url( $order->order->get_id() );
?>

<div>
	<table>
		<tr>
			<th colspan="2"><?php printf( 'NO: %s', esc_html( $consignment_number ) ); ?></th>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Earliest Pickup', 'bring-fraktguiden-for-woocommerce' ); ?>:</td>
			<td><?php echo esc_html( $earliest_pickup ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Expected delivery', 'bring-fraktguiden-for-woocommerce' ); ?>:</td>
			<td><?php echo esc_html( $expected_delivery ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Labels', 'bring-fraktguiden-for-woocommerce' ); ?>:</td>
			<td>
				<a class="button button-small button-primary" href="<?php echo esc_attr( $labels_url ); ?>" target="_blank"><?php esc_html_e( 'Download', 'bring-fraktguiden-for-woocommerce' ); ?> &darr;</a>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Tracking', 'bring-fraktguiden-for-woocommerce' ); ?>:</td>
			<td>
				<a class="button button-small" href="<?php echo esc_attr( $tracking ); ?>" target="_blank"><?php esc_html_e( 'View', 'bring-fraktguiden-for-woocommerce' ); ?> &rarr;</a>
			</td>
		</tr>
		<tr>
			<td>
				<?php esc_html_e( 'Packages', 'bring-fraktguiden-for-woocommerce' ); ?>:
			</td>
			<td valign="center">
				<ul class="bring-list-tracking-numbers">
					<?php
					foreach ( $packages as $package ) {
						// $correlation_id = property_exists( $package, 'correlationId' ) ? $package->correlationId : 'N/A';
						?>
						<li><?php printf( 'NO: %s', esc_html( $package['packageNumber'] ) ); ?></li>
					<?php } ?>
				</ul>
			</td>
		</tr>
	</table>
</div>
