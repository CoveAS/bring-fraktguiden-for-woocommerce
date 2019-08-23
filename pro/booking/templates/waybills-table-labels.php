<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

?>

<style>
.mailbox-waybills,
.mailbox-labels {
	width: 100%;
	border-collapse: collapse;
}
.mailbox-waybills p,
.mailbox-labels p {
	margin: 0;
}
.mailbox-waybills th,
.mailbox-waybills td,
.mailbox-labels th,
.mailbox-labels td {
	padding: 0.25rem;
}
.mailbox-waybills th,
.mailbox-labels th {
	text-align: left;
	background-color: #eee;
}
.mailbox-waybills tr:nth-of-type(even),
.mailbox-labels tr:nth-of-type(even) {
	background-color: #eee;
}

.inactive strong {
	color: #999;
}
</style>

<?php foreach ( $consignments as $customer_number => $customer_consignments ) : ?>
	<h3><?php echo esc_html( $customer_number ); ?></h3>
	<table class="mailbox-labels">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Customer number', 'bring-fraktguiden-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Consignment number', 'bring-fraktguiden-for-woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $customer_consignments as $mailbox_label_id => $consignment ) :
				if ( empty( $consignment ) ) {
					continue;
				}

				$active = ! in_array( $consignment->get_consignment_number(), $inactive_consignment_numbers, true );
				?>
				<tr class="<?php echo $active ? 'active' : 'inactive'; ?>">
					<td>
					<?php if ( $new || $errors ) : ?>
					<label>
						<input type="checkbox" <?php echo ( $new ? '' : 'checked="checked"' ); // phpcs:ignore ?> value="<?php echo esc_attr( $consignment->get_consignment_number() ); ?>" name="consignment_numbers[<?php echo esc_attr( $consignment->get_customer_number() ); ?>][<?php echo esc_attr( $mailbox_label_id ); ?>]">
						<strong><?php echo esc_html( $consignment->get_consignment_number() ); ?></strong>
					</label>
					<?php else : ?>
					<strong><?php echo esc_html( $consignment->get_consignment_number() ); ?></strong>
					<?php endif; ?>
					<br>
					<small><?php esc_html_e( 'Date', 'bring-fraktguiden-for-woocommerce' ); ?>: <?php echo esc_html( $consignment->get_date_time() ); ?></small>
					</td>
					<td>
					<p>
						<a href="<?php echo esc_attr( $consignment->get_label_file()->get_download_url() ); ?>" target="_blank">
							<?php esc_html_e( 'Download label', 'bring-fraktguiden-for-woocommerce' ); ?>
						</a>
					</p>
					<small><?php esc_html_e( 'Order ID', 'bring-fraktguiden-for-woocommerce' ); ?>: <a href="<?php admin_url( 'edit.php?post_id=' . $consignment->order_id ); ?>">
					<?php echo esc_html( $consignment->order_id ); ?>
					</a></small>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endforeach; ?>

<div style="text-align: right;">
	<?php if ( ! empty( $consignments ) && $new ) : ?>
		<a class="wp-core-ui button button-large bring-select-all" href="#"><?php esc_html_e( 'Select all', 'bring-fraktguiden-for-woocommerce' ); ?></a>
		<script>
			jQuery( '.bring-select-all' ).on( 'click', function( e ) {
				e.preventDefault();
				jQuery( '.mailbox-labels input' ).prop( 'checked', 'checked' );
			})
		</script>
	<?php endif; ?>

	<?php if ( ! empty( $errors ) ) : ?>
		<input type="submit" class="wp-core-ui button button-large button-primary" value="<?php esc_html_e( 'Retry booking', 'bring-fraktguiden-for-woocommerce' ); ?>" name="retry_request">
	<?php endif; ?>
</div>

<?php if ( empty( $consignments ) ) : ?>
	<h3><?php esc_html_e( 'No labels available', 'bring-fraktguiden-for-woocommerce' ); ?></h3>
<?php endif; ?>
