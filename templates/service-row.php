<tr>
	<td class="fraktguiden-services-table-col-enabled">
		<label for="<?php echo esc_attr( $service->id ); ?>">
			<input type="checkbox" class="bring-toggle-checkbox"
				id="<?php echo esc_attr( $service->id ); ?>"
				name="<?php echo esc_attr( $field_key ); ?>[]"
				value="<?php echo esc_attr( $service->key ); ?>"
				<?php echo( $service->enabled ? 'checked="checked"' : '' ); ?>
			/>
			<em class="bring-toggle-alt"></em>
		</label>
	</td>
	<td class="fraktguiden-services-table-col-name">
		<span data-tip="<?php echo esc_attr( $service->service_data['HelpText'] ); ?>"
				class="woocommerce-help-tip"></span>
		<label class="fraktguiden-service"
			for="<?php echo esc_attr( $service->id ); ?>"
			data-productName="<?php echo esc_attr( $service->service_data['productName'] ); ?>"
			data-displayName="<?php echo esc_attr( $service->service_data['displayName'] ); ?>"
		>
			<?php echo esc_attr( $service->get_name_by_index( $this->shipping_method->service_name ) ); ?>
		</label>
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
			placeholder="<?php _e( '', 'bring-fraktguiden' ) ?>"
			name="<?php echo esc_attr( $service->custom_price_id ); ?>"
			value="<?php echo esc_attr( $service->custom_price ); ?>"
		/>
	</td>

	<td class="fraktguiden-services-table-col-customer-numbers">
		<span data-tip="<?php _e( 'Allows you to offer different shipping options from different shipping accounts. Useful for when allowing international and cargo shipping options', 'bring-fraktguiden' ); ?>" class="woocommerce-help-tip"></span>

		<label for="toggle_separate_customer_number_id_<?php echo esc_attr( $service->free_shipping_id ); ?>">
			<input type="checkbox" class="bring-toggle-checkbox enable_free_shipping_limit"
				id="toggle_separate_customer_number_id_<?php echo esc_attr( $service->free_shipping_id ); ?>"
				name="toggle_separate_customer_number_id_<?php echo esc_attr( $service->free_shipping_id ); ?>"
				<?php echo esc_attr( $service->free_shipping ? 'checked="checked"' : '' ); ?>
			/>
			<em class="bring-toggle-alt"></em>
		</label>

		<input type="text" class="customer_number_overwrite" disabled
			name="<?php echo esc_attr( $service->customer_number_id ); ?>"
			value="<?php echo esc_attr( $service->customer_number ); ?>"
			placeholder=""
		/>
	</td>
	<td class="fraktguiden-services-table-col-free-shipping-threshold">
		<span data-tip="<?php _e( 'Allows you to enable free shipping when the customers cart reached this value', 'bring-fraktguiden' ); ?>" class="woocommerce-help-tip"></span>

		<label for="<?php echo esc_attr( $service->free_shipping_id ); ?>">
			<input type="checkbox" class="bring-toggle-checkbox enable_free_shipping_limit"
				id="<?php echo esc_attr( $service->free_shipping_id ); ?>"
				name="<?php echo esc_attr( $service->free_shipping_id ); ?>"
				<?php echo esc_attr( $service->free_shipping ? 'checked="checked"' : '' ); ?>
			/>
			<em class="bring-toggle-alt"></em>
		</label>

		<input type="text" class="free_shipping_limit" disabled
			name="<?php echo esc_attr( $service->free_shipping_threshold_id ); ?>"
			value="<?php echo esc_attr( $service->free_shipping_threshold ); ?>"
			placeholder=""
		/>
	</td>
	<?php endif; ?>
</tr>
