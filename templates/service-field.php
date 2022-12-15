<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

?>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $field_key ); ?>">
				<?php echo esc_html( $title ); // phpcs:ignore ?>
			</label>
		</th>
		<td class="forminp">
			<div id="shipping_services" class="pro-<?php echo Fraktguiden_Helper::pro_activated() ? 'enabled' : 'disabled'; ?>">
				<select class="select2" v-model="selected" multiple="multiple" name="<?php echo esc_attr( $field_key ); ?>[]">
					<optgroup v-for="optgroup in services_data" :label="optgroup.title">
						<option v-for="(option, option_id) in optgroup.services" :value="option_id">
							{{option.productName}}
						</option>
					</optgroup>
				</select>
				<shippingproduct
					v-for="service in services"
					:id="service.bring_product"
					:service_data="service.service_data"
					:service="service"
					:vas="service.vas"
					:key="service.bring_product"
				></shippingproducts>
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
