<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

?>

<tr>
	<td class="fraktguiden-services-table-col-enabled">
		<input type="checkbox"
			id="<?php echo esc_attr( $service->id ); ?>"
			name="<?php echo esc_attr( $field_key ); ?>[]"
			value="<?php echo esc_attr( $service->key ); ?>"
			<?php echo( $service->enabled ? 'checked="checked"' : '' ); ?>
		/>
	</td>
	<td class="fraktguiden-services-table-col-name">
		<label class="fraktguiden-service"
			for="<?php echo esc_attr( $service->id ); ?>"
			data-productName="<?php echo esc_attr( $service->service_data['productName'] ); ?>"
			data-displayName="<?php echo esc_attr( $service->service_data['displayName'] ); ?>"
		>
			<?php echo esc_attr( $service->get_name_by_index( $this->shipping_method->service_name ) ); ?>
		</label>
		<?php if ( ! empty( $service->service_data['HelpText'] ) ) : ?>
		<span data-tip="<?php echo esc_attr( $service->service_data['HelpText'] ); ?>"
				class="woocommerce-help-tip"></span>
		<?php endif; ?>
		<input
			class="fraktguiden-service-custom-name"
			style="display: none"
			placeholder="<?php echo esc_attr( $service->service_data['productName'] ); ?>"
			name="<?php echo esc_attr( $service->custom_name_id ); ?>"
			value="<?php echo esc_attr( $service->custom_name ); ?>"
		/>
	</td>
	<?php if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) : ?>
	<td class="fraktguiden-services-table-col-custom-price">
		<input type="text"
			placeholder="..."
			name="<?php echo esc_attr( $service->custom_price_id ); ?>"
			value="<?php echo esc_attr( $service->custom_price ); ?>"
		/>
	</td>
	<td class="fraktguiden-services-table-col-customer-numbers">
		<input type="text"
			name="<?php echo esc_attr( $service->customer_number_id ); ?>"
			value="<?php echo esc_attr( $service->customer_number ); ?>"
			placeholder="..."
		/>
	</td>
	<td class="fraktguiden-services-table-col-free-shipping-threshold">
		<label for="<?php echo esc_attr( $service->free_shipping_id ); ?>">
			<input type="checkbox" class="bring-toggle-checkbox enable_free_shipping_limit"
				id="<?php echo esc_attr( $service->free_shipping_id ); ?>"
				name="<?php echo esc_attr( $service->free_shipping_id ); ?>"
				<?php echo esc_attr( $service->free_shipping ? 'checked="checked"' : '' ); ?>
			/>
			<em class="bring-toggle-alt"></em>
		</label>

		<input type="text" class="free_shipping_limit"
			name="<?php echo esc_attr( $service->free_shipping_threshold_id ); ?>"
			value="<?php echo esc_attr( $service->free_shipping_threshold ); ?>"
			placeholder="..."
		/>
	</td>
	<?php endif; ?>
</tr>
