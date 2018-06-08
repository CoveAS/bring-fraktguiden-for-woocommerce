<?php
//$correlation_id     = $consignment->correlationId;
// $errors             = $consignment->errors;
$consignment_number = $consignment->get_consignment_number();
$links              = $consignment->get_links();
$tracking           = $links['tracking'];
$date_and_times     = $consignment->get_dates();
$earliest_pickup    = $date_and_times['earliestPickup'] ? date_i18n( wc_date_format(), $date_and_times['earliestPickup'] / 1000 ) : 'N/A';
$expected_delivery  = $date_and_times['expectedDelivery'] ? date_i18n( wc_date_format(), $date_and_times['expectedDelivery'] / 1000 ) : 'N/A';
$packages           = $consignment->get_packages();
$labels_url         = Bring_Booking_Labels::create_download_url( $order->order->get_id() );
?>
<div>
  <table>
    <tr>
      <th colspan="2"><?php printf( 'NO: %s', $consignment_number ) ?></th>
    </tr>
    <tr>
      <td><?php _e( 'Earliest Pickup', 'bring-fraktguiden' ); ?>:</td>
      <td><?php echo $earliest_pickup; ?></td>
    </tr>
    <tr>
      <td><?php _e( 'Expected delivery', 'bring-fraktguiden' ); ?>:</td>
      <td><?php echo $expected_delivery; ?></td>
    </tr>
    <tr>
      <td><?php _e( 'Labels', 'bring-fraktguiden' ); ?>:</td>
      <td>
        <a class="button button-small button-primary" href="<?php echo $labels_url; ?>" target="_blank"><?php _e( 'Download', 'bring-fraktguiden' ); ?> &darr;</a>
      </td>
    </tr>
    <tr>
      <td><?php _e( 'Tracking', 'bring-fraktguiden' ); ?>:</td>
      <td>
        <a class="button button-small" href="<?php echo $tracking; ?>" target="_blank"><?php _e( 'View', 'bring-fraktguiden' ); ?> &rarr;</a>
      </td>
    </tr>
    <tr>
      <td>
        <?php _e( 'Packages', 'bring-fraktguiden' ); ?>:
      </td>
      <td valign="center">
        <ul class="bring-list-tracking-numbers">
          <?php
          foreach ( $packages as $package ) {
            //$correlation_id = property_exists( $package, 'correlationId' ) ? $package->correlationId : 'N/A';
            ?>
            <li><?php printf( 'NO: %s', $package['packageNumber'] ); ?></li>
          <?php } ?>
        </ul>
      </td>
    </tr>
  </table>
</div>