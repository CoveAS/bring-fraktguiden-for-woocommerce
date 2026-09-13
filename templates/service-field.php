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

			<?php if ( ! Fraktguiden_Helper::pro_activated() ) : ?>
				<dialog class="bfg bfg-modal" id="bfg-pro-lock-dialog">
					<div class="bfg-modal__head">
						<h2 class="bfg-modal__title"><?php esc_html_e( 'Pro only', 'bring-fraktguiden-for-woocommerce' ); ?></h2>
						<button type="button" class="bfg-modal__close" data-bfg-dialog-close aria-label="<?php esc_attr_e( 'Close', 'bring-fraktguiden-for-woocommerce' ); ?>">&times;</button>
					</div>
					<div class="bfg-modal__body">
						<p><?php esc_html_e( 'Service overrides and pickup points need a Pro license.', 'bring-fraktguiden-for-woocommerce' ); ?></p>
						<p><?php esc_html_e( 'You can also start a free trial on the license page.', 'bring-fraktguiden-for-woocommerce' ); ?></p>
					</div>
					<div class="bfg-modal__foot">
						<a class="bfg-btn bfg-btn--primary bfg-btn--sm" href="<?php echo esc_url( admin_url( 'admin.php?page=bring_fraktguiden_pro' ) ); ?>">
							<?php esc_html_e( 'Go to license settings', 'bring-fraktguiden-for-woocommerce' ); ?>
						</a>
					</div>
				</dialog>
			<?php endif; ?>
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
