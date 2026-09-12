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

				<!-- Multi-select dropdown -->
				<select
					class="select2"
					multiple="multiple"
					name="<?php echo esc_attr( $field_key ); ?>[]"
				>
					<?php $selected = array_map( 'strval', (array) $service_options['selected'] ); ?>
					<?php foreach ( Fraktguiden_Helper::get_services_data() as $group_id => $group ) : ?>
						<optgroup label="<?php echo esc_attr( $group['title'] ); ?>">
							<?php foreach ( $group['services'] as $service_id => $service ) : ?>
								<option value="<?php echo esc_attr( $service_id ); ?>" <?php selected( in_array( (string) $service_id, $selected, true ) ); ?>>
									<?php echo esc_html( $service['productName'] ); ?>
								</option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>

				<!-- Service cards will be rendered here by JavaScript -->
				<div id="service-cards-container"></div>

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
