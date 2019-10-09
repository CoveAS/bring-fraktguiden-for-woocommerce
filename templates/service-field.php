<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */
?>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $field_key ); ?>">
				<?php esc_html_e( 'Services', 'bring-fraktguiden-for-woocommerce' ); // phpcs:ignore ?>
			</label>
		</th>
		<td class="forminp">
			<div id="shipping_services">
				<select class="select2" v-model="selected" multiple="multiple" name="<?php echo esc_attr( $field_key ); ?>[]">
					<optgroup v-for="optgroup in services_data" :label="optgroup.title">
						<option v-for="(option, option_id) in optgroup.services" :value="option_id">
							{{option.productName}}
						</option>
					</optgroup>
				</select>
				<shippingproduct v-for="service in services" v-bind="service" v-bind:key="service.id"></shippingproducts>
			</div>
		</td>
	</tr>
<tr>
	<td colspan="2">
		<script>
			jQuery( document ).ready( function ($) {
				$( document ).on( 'change', '.bring-toggle-checkbox', function () {
					$( this )
					.closest( 'td' )
					.find( '> input' )
					.prop( 'readonly', ! this.checked )
					.prop( 'required', this.checked );
				});
			});
		</script>
	</td>
</tr>
