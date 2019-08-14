<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

foreach ( $services as $group => $service_group ) :
	?>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $field_key ); ?>">
				<?php esc_html_e( $service_group['title'], 'bring-fraktguiden-for-woocommerce' ); // phpcs:ignore ?>
			</label>
		</th>
		<td class="forminp">
			<?php if ( $service_group['description'] ) : ?>
				<?php // Service description may contain HTML. ?>
				<p style="padding-bottom: 1rem;"><?php _e( $service_group['description'], 'bring-fraktguiden-for-woocommerce' ); // phpcs:ignore ?></p>
			<?php endif; ?>
			<?php require __DIR__ . '/service-table.php'; ?>
		</td>
	</tr>
<?php endforeach; ?>
<tr>
	<td colspan="2">
		<script>
			jQuery( function ( $ ) {
				<?php if ( ! Fraktguiden_Helper::pro_activated() ) : ?>
					var elem = $('#woocommerce_bring_fraktguiden_service_name [value="CustomName"]');
					var label = elem.text();
					label += " <?php esc_html_e( '(PRO only)' ); ?>";
					elem
						.text( label )
						.attr( 'disabled', 'disabled' );
				<?php endif; ?>
				function service_name_handler() {
					var val = this.value;
					if ('CustomName' == val) {
						// Show the input field for custom name.
						$( '.fraktguiden-service-custom-name' ).show();
						$( '.fraktguiden-service' ).hide();
					} else {
						// Show the label and change it to the selected type.
						$( '.fraktguiden-service-custom-name' ).hide();
						$( '.fraktguiden-service' ).show();
						$( '.fraktguiden-services-table' ).find( 'label.fraktguiden-service' ).each( function ( i, elem ) {
							var label = $( elem );
							label.text( label.attr( 'data-' + val ) );
						} );
					}
				}
				$( '#woocommerce_bring_fraktguiden_service_name' )
					.each( service_name_handler )
					.change( service_name_handler );
			} );
		</script>
	</td>
</tr>
