<table class="wc_shipping widefat fraktguiden-services-table">
	<thead>
		<tr>
			<th class="fraktguiden-services-table-col-enabled">
			<?php esc_html_e( 'Active', 'bring-fraktguiden' ); ?>
			</th>
			<th class="fraktguiden-services-table-col-service"><?php esc_html_e( 'Service', 'bring-fraktguiden' ); ?></th>
			<?php if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) : ?>
			<th class="fraktguiden-services-table-col-custom-price"><?php esc_html_e( 'Set fixed price', 'bring-fraktguiden' ); ?></th>
			<th class="fraktguiden-services-table-col-customer-number"><?php esc_html_e( 'Alternative Customer Number', 'bring-fraktguiden' ); ?></th>
			<th class="fraktguiden-services-table-col-free-shipping-threshold"><?php esc_html_e( 'Free shipping limit', 'bring-fraktguiden' ); ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $service_group['services'] as $key => $service_data ) :
			$service = new Fraktguiden_Service( $key, $service_data, $service_options );
			require __DIR__ . '/service-row.php';
		endforeach;
		?>
	</tbody>
</table>

<script>
	jQuery(document).ready( function($) {
		$(document).on('change', '.enable_customer_number_overwrite', function () {
		    $(this)
		    .parent().parent()
		    .find( 'input[type="text"]' )
		    .prop( 'disabled', ! this.checked )
		    .prop( 'required', this.checked );
		});

		$(document).on('change', '.enable_free_shipping_limit', function () {
		    $(this)
		    .parent().parent()
		    .find( 'input[type="text"]' )
		    .prop( 'disabled', ! this.checked )
		    .prop( 'required', this.checked );
		});
	});
</script>