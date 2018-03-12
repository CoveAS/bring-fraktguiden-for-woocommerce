<?php

// var_dump(  );die;

$consignment = $consignments;
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
$waybill_url = Bring_Booking_Labels::create_download_url( $order->order->get_id().'&type=waybill' );
?>
<div>
  <table>
    <tr>
      <th colspan="2"><?php printf( 'NO: %s', $consignment->id ) ?></th>
    </tr>
    <tr>
      <td><?php _e( 'Labels', 'bring-fraktguiden' ); ?>:</td>
      <td>
        <a class="button button-small button-primary" href="<?php echo $labels_url; ?>" target="_blank"><?php _e( 'Download', 'bring-fraktguiden' ); ?> &darr;</a>
      </td>
    </tr>
    <tr>
      <td><?php _e( 'Waybill', 'bring-fraktguiden' ); ?>:</td>
      <td>
        <a class="button button-small button-primary" href="<?php echo $waybill_url; ?>" target="_blank"><?php _e( 'Download', 'bring-fraktguiden' ); ?> &darr;</a>
      </td>
    </tr>
    <tr>
      <td>
        <?php _e( 'Packages', 'bring-fraktguiden' ); ?>:
      </td>
      <td valign="center">
        <ul class="bring-list-tracking-numbers">
          <?php
          foreach ( $consignment->attributes->packages as $package ) {
            //$correlation_id = property_exists( $package, 'correlationId' ) ? $package->correlationId : 'N/A';
            ?>
            <li><?php printf( '<a href="%s%s" target="_blank">NO: %2$s</a>', $tracking_url, $package->shipmentNumber ); ?></li>
          <?php } ?>
        </ul>
      </td>
    </tr>
  </table>
</div>