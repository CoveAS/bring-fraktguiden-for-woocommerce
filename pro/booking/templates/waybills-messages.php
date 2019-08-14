<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

?>

<style>
.mailbox-waybill-errors {
	width: 100%;
}
.mailbox-waybill-errors__title {
	margin-bottom: 0.5rem;
	color: #C00;
}
.mailbox-waybill-errors__message {
	padding: 0.25rem;
	color: #900;
	background-color: #FFEEEE;
	margin-top: 0;
}
.mailbox-waybill-success {
	color: #060;
	background-color: #EEFFEE;
	padding: 0.5rem;
	border: 1px solid #9C9;
}
</style>

<?php if ( ! empty( $errors ) ) : ?>
	<div class="mailbox-waybill-errors">
		<?php foreach ( $errors as $customer_number => $error_messages ) : ?>
		<h3 class="mailbox-waybill-errors__title"><?php esc_html_e( 'Error', 'bring-fraktguiden-for-woocommerce' ) . ' ' . $customer_number; ?></h3>
		<ul class="mailbox-waybill-errors__messages">
			<?php foreach ( $error_messages as $error_message ) : ?>
				<li class="mailbox-waybill-errors__message"><?php echo esc_html( $error_message ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endforeach; ?>
	</div>
<?php elseif ( ! $new ) : ?>
	<h3 class="mailbox-waybill-success"><?php esc_html_e( 'Waybill completed', 'bring-fraktguiden-for-woocommerce' ); ?></h3>
<?php endif; ?>
