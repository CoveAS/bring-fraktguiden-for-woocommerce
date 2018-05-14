<style type="text/css">
  .mailbox-waybills,
  .mailbox-labels {
    width: 100%;
    border-collapse: collapse;
  }
  .mailbox-waybills p,
  .mailbox-labels p {
    margin: 0;
  }
  .mailbox-waybills th,
  .mailbox-waybills td,
  .mailbox-labels th,
  .mailbox-labels td {
    padding: 0.25rem;
  }
  .mailbox-waybills th,
  .mailbox-labels th {
    text-align: left;
    background-color: #eee;
  }
  .mailbox-waybills tr:nth-of-type(even),
  .mailbox-labels tr:nth-of-type(even) {
    background-color: #eee;
  }

  .inactive strong {
    color: #999;
  }
</style>
<?php foreach ( $consignments as $customer_number => $customer_consignments ): ?>
  <h3><?php echo $customer_number; ?></h3>
  <table class="mailbox-labels">
    <thead>
      <tr>
        <th><?php _e( 'Customer number', 'bring-fraktguiden' ); ?></th>
        <th><?php _e( 'Consignment number', 'bring-fraktguiden' ); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $customer_consignments as $mailbox_label_id => $consignment ): ?>
        <?php
        $active = ! in_array(
          $consignment->get_consignment_number(),
          $inactive_consignment_numbers
        );
        ?>
      <tr class="<?php echo $active ? 'active' : 'inactive'; ?>">
        <td>
          <?php if ( $new || $errors ): ?>
          <label>
            <input type="checkbox" <?php echo ( $new ? '' : 'checked="checked"' ); ?> value="<?php echo $consignment->get_consignment_number(); ?>" name="consignment_numbers[<?php echo $consignment->get_customer_number(); ?>][<?php echo $mailbox_label_id; ?>]">
            <strong><?php echo $consignment->get_consignment_number(); ?></strong>
          </label>
          <?php else: ?>
            <strong><?php echo $consignment->get_consignment_number(); ?></strong>
          <?php endif; ?>
          <br>
          <small><?php _e( 'Date' ); ?>: <?php echo $consignment->get_date_time(); ?></small>
        </td>
        <td>
          <p><a href="<?php echo $consignment->get_label_file()->get_download_url(); ?>" target="_blank">
            <?php _e( 'Download label', 'bring-fraktguiden' ) ?>
          </a></p>
          <small><?php _e( 'Order ID', 'bring-fraktguiden' ); ?>: <a href="<?php admin_url('edit.php?post_id='.$consignment->order_id );?>">
             <?php echo $consignment->order_id; ?>
          </a></small>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endforeach; ?>
<div style="text-align: right;">
  <?php if ( ! empty( $consignments ) && $new ): ?>
    <a class="wp-core-ui button button-large bring-select-all" href="#"><?php _e( 'Select all', 'bring-fraktguiden' ); ?></a>
    <script type="text/javascript">
      jQuery( '.bring-select-all' ).click( function( e ) {
        e.preventDefault();
        jQuery( '.mailbox-labels input' ).prop( 'checked', 'checked' );
      })
    </script>
  <?php endif; ?>
  <?php if ( ! empty( $errors ) ): ?>
      <input type="submit" class="wp-core-ui button button-large button-primary" value="<?php _e( 'Retry booking', 'bring-fraktguiden' ); ?>" name="retry_request">
  <?php endif; ?>
</div>
<?php if ( empty( $consignments ) ): ?>
  <h3><?php _e( 'No labels available', 'bring-fraktguiden' ); ?></h3>
<?php endif; ?>
