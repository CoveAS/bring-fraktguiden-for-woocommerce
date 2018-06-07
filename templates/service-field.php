<?php
foreach ( $services as $group => $service_group ):
?>
<tr valign="top">
  <th scope="row" class="titledesc">
    <label for="<?php echo $field_key ?>">
      <?php _e( $service_group['title'], 'bring-fraktguiden' ); ?>
    </label>
  </th>
  <td class="forminp">
    <?php if ( $service_group['description'] ): ?>
      <p><?php _e( $service_group['description'], 'bring-fraktguiden' ); ?></p>
    <?php endif; ?>
    <?php require __DIR__ .'/service-table.php'; ?>
  </td>
</tr>
<?php endforeach; ?>
<tr>
  <td colspan="2">
    <script>
      jQuery( document ).ready( function () {
        var $ = jQuery;
        $( '#woocommerce_bring_fraktguiden_service_name' ).change( function () {
          console.log( 'change', this.value );
          var val = this.value;
          $( '.fraktguiden-services-table' ).find( 'label.fraktguiden-service' ).each( function ( i, elem ) {
            var label = $( elem );
            label.text( label.attr( 'data-' + val ) );
          } );
        } );
      } );
    </script>
  </td>
</tr>