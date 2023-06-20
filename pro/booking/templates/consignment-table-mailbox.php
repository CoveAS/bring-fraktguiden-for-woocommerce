<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use BringFraktguidenPro\Booking\Consignment\Bring_Booking_Consignment;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Labels;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

/** @var Bring_WC_Order_Adapter $order */

/** @var Bring_Booking_Consignment $consignment */
$consignment = reset( $consignments );
// $errors             = $consignment->errors;
// $confirmation       = $consignment->confirmation;
// $consignment_number = $confirmation->consignmentNumber;
// $links              = $confirmation->links;
$tracking_url = 'https://tracking.bring.com/tracking.html?q=';
// $date_and_times     = $confirmation->dateAndTimes;
// $earliest_pickup    = $date_and_times->earliestPickup ? date_i18n( wc_date_format(), $date_and_times->earliestPickup / 1000 ) : 'N/A';
// $expected_delivery  = $date_and_times->expectedDelivery ? date_i18n( wc_date_format(), $date_and_times->expectedDelivery / 1000 ) : 'N/A';
// $packages           = $confirmation->packages;

$labels_url = Bring_Booking_Labels::create_download_url( $order->order->get_id() );
$order_id   = $order->order->get_id();
$waybill    = get_attached_media( 'waybill', $order_id );

?>

<div>
	<table>
		<tr>
			<th colspan="2"><?php printf( 'NO: %s', esc_html( $consignment->get_consignment_number() ) ); ?></th>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Labels', 'bring-fraktguiden-for-woocommerce' ); ?>:</td>
			<td>
				<a class="button button-small button-alt" href="<?php echo esc_attr( $labels_url ); ?>" target="_blank"><?php esc_html_e( 'Download', 'bring-fraktguiden-for-woocommerce' ); ?> &darr;</a>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Waybill', 'bring-fraktguiden-for-woocommerce' ); ?>:</td>
			<td>
				<a class="button button-small button-primary" href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=mailbox_waybill' ) ); ?>" target="_blank"><?php esc_html_e( 'Create waybill', 'bring-fraktguiden-for-woocommerce' ); ?></a>
			</td>
		</tr>
		<tr>
			<td>
				<?php esc_html_e( 'Packages', 'bring-fraktguiden-for-woocommerce' ); ?>:
			</td>
			<td valign="center">
				<ul class="bring-list-tracking-numbers">
					<?php
					foreach ( $consignments as $_consignment ) {
						// $correlation_id = property_exists( $_consignment, 'correlationId' ) ? $_consignment->correlationId : 'N/A';
						?>
						<li><?php printf( '<a href="%s%s" target="_blank">NO: %2$s</a>', esc_attr( $tracking_url ), esc_html( $_consignment->get_tracking_code() ) ); ?></li>
					<?php } ?>
				</ul>
			</td>
		</tr>
	</table>
</div>
