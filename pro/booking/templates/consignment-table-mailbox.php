<?php

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
$labels_url  = Bring_Booking_Labels::create_download_url( $order->order->get_id() );

$order_id = $order->order->get_id();

$waybill = get_attached_media( 'waybill', $order_id );


?>
<div>
  <table>
    <tr>
      <th colspan="2"><?php printf( 'NO: %s', $consignment->get_consignment_number() ) ?></th>
    </tr>
    <tr>
      <td><?php _e( 'Labels', 'bring-fraktguiden' ); ?>:</td>
      <td>
        <a class="button button-small button-alt" href="<?php echo $labels_url; ?>" target="_blank"><?php _e( 'Download', 'bring-fraktguiden' ); ?> &darr;</a>
      </td>
    </tr>
    <tr>
      <td><?php _e( 'Waybill', 'bring-fraktguiden' ); ?>:</td>
      <td>

        <a class="button button-small button-primary" href="<?php echo admin_url('post-new.php?post_type=mailbox_waybill'); ?>" target="_blank"><?php _e( 'Create waybill', 'bring-fraktguiden' ); ?></a>
      </td>
    </tr>
    <tr>
      <td>
        <?php _e( 'Packages', 'bring-fraktguiden' ); ?>:
      </td>
      <td valign="center">
        <ul class="bring-list-tracking-numbers">
          <?php
          foreach ( $consignments as $_consignment ) {
            //$correlation_id = property_exists( $_consignment, 'correlationId' ) ? $_consignment->correlationId : 'N/A';
            ?>
            <li><?php printf( '<a href="%s%s" target="_blank">NO: %2$s</a>', $tracking_url, $_consignment->get_tracking_code() ); ?></li>
          <?php } ?>
        </ul>
      </td>
    </tr>
  </table>
</div>