<table class="wc_shipping widefat fraktguiden-services-table">
	<thead>
		<tr>
			<th class="fraktguiden-services-table-col-enabled">
			<?php esc_html_e( 'Active', 'bring-fraktguiden-for-woocommerce' ); ?>
			</th>
			<th class="fraktguiden-services-table-col-service"><?php esc_html_e( 'Service', 'bring-fraktguiden-for-woocommerce' ); ?><span data-tip="<?php echo esc_attr( $service->service_data['HelpText'] ); ?>"class="woocommerce-help-tip"></span></th>
			<?php if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) : ?>
			<th class="fraktguiden-services-table-col-custom-price"><?php esc_html_e( 'Fixed price override', 'bring-fraktguiden-for-woocommerce' ); ?></th>
			<th class="fraktguiden-services-table-col-customer-number">
				<?php esc_html_e( 'Alternative customer number', 'bring-fraktguiden-for-woocommerce' ); ?>
				<span data-tip="<?php _e( 'Allows you to offer different shipping options from different shipping accounts. Useful for when allowing international and cargo shipping options', 'bring-fraktguiden-for-woocommerce' ); ?>" class="woocommerce-help-tip"></span>
			</th>
			<th class="fraktguiden-services-table-col-free-shipping-threshold">
				<?php esc_html_e( 'Free shipping threshold', 'bring-fraktguiden-for-woocommerce' ); ?>
				<span data-tip="<?php _e( 'Allows you to enable free shipping when the customers cart reached this value', 'bring-fraktguiden-for-woocommerce' ); ?>" class="woocommerce-help-tip"></span>
			</th>
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
	// @todo - hasty jQuery to disable text fields based on
	// if the previous checkbox is toggled on or off (checked)
	jQuery(document).ready( function($) {
		// Customer number toggle
		$(document).on('change', '.enable_customer_number_overwrite', function () {
		    $(this)
		    .parent().parent()
		    .find( 'input[type="text"]' )
		    .prop( 'disabled', ! this.checked )
		    .prop( 'required', this.checked );
		});

		// Free shipping toggle
		$(document).on('change', '.enable_free_shipping_limit', function () {
		    $(this)
		    .parent().parent()
		    .find( 'input[type="text"]' )
		    .prop( 'disabled', ! this.checked )
		    .prop( 'required', this.checked );
		});
	});
</script>