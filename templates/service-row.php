<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

?>

<tr>
	<td class="fraktguiden-services-table-col-enabled">
		<label for="<?php echo esc_attr( $service->id ); ?>">
			<input
				type="checkbox"
				class="bring-toggle-checkbox"
				id="<?php echo esc_attr( $service->id ); ?>"
				name="<?php echo esc_attr( $field_key ); ?>[]"
				value="<?php echo esc_attr( $service->key ); ?>"
				<?php echo( $service->enabled ? 'checked="checked"' : '' ); ?>
			>
			<em class="bring-toggle-alt"></em>
		</label>
	</td>

	<td class="fraktguiden-services-table-col-name">
		<label
			class="fraktguiden-service"
			for="<?php echo esc_attr( $service->id ); ?>"
			data-productName="<?php echo esc_attr( $service->service_data['productName'] ); ?>"
			data-displayName="<?php echo esc_attr( $service->service_data['displayName'] ); ?>"
		>
			<?php echo esc_html( $service->get_name_by_index( $this->shipping_method->service_name ) ); ?>
		</label>

		<input
			type="text"
			class="fraktguiden-service-custom-name"
			style="display: none;"
			placeholder="<?php echo esc_attr( $service->service_data['productName'] ); ?>"
			name="<?php echo esc_attr( $service->custom_name_id ); ?>"
			value="<?php echo esc_attr( $service->custom_name ); ?>"
		>

		<?php if ( ! empty( $service->service_data['HelpText'] ) ) : ?>
		<span data-tip="<?php echo esc_attr( $service->service_data['HelpText'] ); ?>" class="woocommerce-help-tip"></span>
		<?php endif; ?>
	</td>

	<?php if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) : ?>

	<td class="fraktguiden-services-table-col-custom-price">
		<input
			type="number"
			step=".01"
			min="0"
			placeholder="0.00"
			name="<?php echo esc_attr( $service->custom_price_id ); ?>"
			value="<?php echo esc_attr( $service->custom_price ); ?>"
		>
	</td>

	<td class="fraktguiden-services-table-col-customer-numbers">
		<input
			type="text"
			class="customer_number_overwrite"
			name="<?php echo esc_attr( $service->customer_number_id ); ?>"
			value="<?php echo esc_attr( $service->customer_number ); ?>"
			placeholder=""
		>
	</td>

	<td class="fraktguiden-services-table-col-free-shipping-threshold">
		<label for="<?php echo esc_attr( $service->free_shipping_id ); ?>">
			<input
				type="checkbox"
				class="bring-toggle-checkbox enable_free_shipping_limit"
				id="<?php echo esc_attr( $service->free_shipping_id ); ?>"
				name="<?php echo esc_attr( $service->free_shipping_id ); ?>"
				<?php echo esc_attr( $service->free_shipping ? 'checked="checked"' : '' ); ?>
			>
			<em class="bring-toggle-alt"></em>
		</label>

		<input
			type="number"
			step=".01"
			min="0"
			placeholder="0.00"
			class="free_shipping_limit"
			name="<?php echo esc_attr( $service->free_shipping_threshold_id ); ?>"
			value="<?php echo esc_attr( $service->free_shipping_threshold ); ?>"
			<?php echo esc_attr( $service->free_shipping ? '' : 'readonly="readonly"' ); ?>
		>
	</td>

	<?php endif; ?>
</tr>
