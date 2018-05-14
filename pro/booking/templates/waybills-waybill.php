
<?php foreach( $waybills as $customer_number => $waybill ): ?>
  <h3>
    <?php echo __( 'Bring order id:' ) .' '. $waybill['data']['id']; ?>,
    <?php echo $customer_number; ?>
  </h3>
  <table class="mailbox-waybills">
    <thead>
      <th><?php _e( 'Package number', 'bring-fraktguiden' ); ?></th>
      <th><?php _e( 'Recipient', 'bring-fraktguiden' ); ?></th>
      <th><?php _e( 'Information', 'bring-fraktguiden' ); ?></th>
      <th><?php _e( 'Contact', 'bring-fraktguiden' ); ?></th>
      <th><?php _e( 'Tracking code', 'bring-fraktguiden' ); ?></th>
    </thead>
    <tbody>
    <?php foreach( $waybill['data']['attributes']['packages'] as $package ): ?>
      <tr>
        <td>
          <?php echo $package['packageNumber']; ?>
        </td>
        <td>
          <?php echo $package['recipientName']; ?><br>
          <?php echo $package['streetAddress']; ?><br>
          <?php echo $package['postalPlace']; ?>
          <?php echo $package['postalCode']; ?>
        </td>
        <td>
          <?php _e( 'RFID:', 'bring-fraktguiden' ); ?> <?php echo $package['rfid'] ? __('Yes') : __('No'); ?><br>
          <?php _e( 'Weight:', 'bring-fraktguiden' ); ?> <?php echo $package['weight']; ?>
        </td>
        <td><?php echo $package['email']; ?><br><?php echo $package['phoneNumber']; ?></td>
        <td><?php echo $package['shipmentNumber']; ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>

  </table>
  <div style="text-align: right;">
    <a class="wp-core-ui button button-large button-alt" href="<?php echo( $waybill['data']['attributes']['waybillUri'] ); ?>" target="_blank">
      <?php _e( 'Dowload waybill', 'bring-fraktguiden' ); ?>  &darr;
    </a>
  </div>
<?php endforeach; ?>