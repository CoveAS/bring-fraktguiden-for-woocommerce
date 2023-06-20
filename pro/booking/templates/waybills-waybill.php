<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

foreach ( $waybills as $customer_number => $waybill ) : ?>
	<?php
	if ( empty( $waybill ) ) {
		continue;
	}
	?>
	<h3>
		<?php esc_html_e( 'Bring order ID:', 'bring-fraktguiden-for-woocommerce' ) . ' ' . $waybill['data']['id']; ?>,
		<?php echo esc_html( $customer_number ); ?>
	</h3>
	<table class="mailbox-waybills">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Package number', 'bring-fraktguiden-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Recipient', 'bring-fraktguiden-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Information', 'bring-fraktguiden-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Contact', 'bring-fraktguiden-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Tracking code', 'bring-fraktguiden-for-woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $waybill['data']['attributes']['packages'] as $package ) : ?>
			<tr>
				<td>
					<?php echo esc_html( $package['packageNumber'] ); ?>
				</td>
				<td>
					<?php echo esc_html( $package['recipientName'] ); ?><br>
					<?php echo esc_html( $package['streetAddress'] ); ?><br>
					<?php echo esc_html( $package['postalPlace'] ); ?>
					<?php echo esc_html( $package['postalCode'] ); ?>
				</td>
				<td>
					<?php esc_html_e( 'RFID:', 'bring-fraktguiden-for-woocommerce' ); ?> <?php echo $package['rfid'] ? esc_html( __( 'Yes', 'bring-fraktguiden-for-woocommerce' ) ) : esc_html( __( 'No', 'bring-fraktguiden-for-woocommerce' ) ); ?><br>
					<?php esc_html_e( 'Weight:', 'bring-fraktguiden-for-woocommerce' ); ?> <?php echo esc_html( $package['weight'] ); ?>
				</td>
				<td><?php echo esc_html( $package['email'] ); ?><br><?php echo esc_html( $package['phoneNumber'] ); ?></td>
				<td><?php echo esc_html( $package['shipmentNumber'] ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>

	</table>
	<div style="text-align: right;">
		<a class="wp-core-ui button button-large button-alt" href="<?php echo esc_attr( $waybill['data']['attributes']['waybillUri'] ); ?>" target="_blank">
			<?php esc_html_e( 'Dowload waybill', 'bring-fraktguiden-for-woocommerce' ); ?>  &darr;
		</a>
	</div>
<?php endforeach; ?>
