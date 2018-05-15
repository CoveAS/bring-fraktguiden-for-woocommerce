
<table class="wc_shipping widefat fraktguiden-services-table">
  <thead>
  <tr>
    <th class="fraktguiden-services-table-col-enabled">
      <?php _e( 'Active', 'bring-fraktguiden' ); ?>
    </th>
    <th class="fraktguiden-services-table-col-service">
      <?php _e( 'Service', 'bring-fraktguiden' ); ?>
    </th>
    <?php if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) : ?>
    <th class="fraktguiden-services-table-col-custom-price">
      <?php _e( 'Custom price', 'bring-fraktguiden' ); ?>
    </th>
    <th class="fraktguiden-services-table-col-free-shipping">
      <?php _e( 'Free shipping', 'bring-fraktguiden' ); ?>
    </th>
    <th class="fraktguiden-services-table-col-free-shipping-threshold">
      <?php _e( 'Free shipping limit', 'bring-fraktguiden' ); ?>
    </th>
    <?php endif; ?>
  </tr>
  </thead>
  <tbody>

  <?php
  foreach ( $service_group['services'] as $key => $service_data ):
    $service = new Fraktguiden_Service( $key, $service_data, $service_options );
    require __DIR__ .'/service-row.php';
  endforeach; ?>
  </tbody>
</table>
