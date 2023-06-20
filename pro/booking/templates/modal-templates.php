<?php
use BringFraktguidenPro\Booking\Views\Bring_Booking_Common_View;
?>
<script type="text/template" id="tmpl-bring-modal-bulk">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1 class="bgf-modal-header"><?php esc_html_e( 'Mybring Booking', 'bring-fraktguiden-for-woocommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'bring-fraktguiden-for-woocommerce' ); ?></span>
					</button>
				</header>
				<article>
					<div class="bring-form-field" style="margin-top:0">
						<?php esc_html_e( 'This will only book orders that has not been booked.', 'bring-fraktguiden-for-woocommerce' ); ?>
					</div>
					<div class="bring-form-field">
						<label><?php esc_html_e( 'Selected orders', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
						<span class="bring-modal-selected-orders-list"></span>
					</div>
					<div class="bring-form-field">
						<label><?php esc_html_e( 'Mybring Customer', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
						<?php Bring_Booking_Common_View::render_customer_selector( '_bring-modal-customer-selector' ); ?>
					</div>
					<div class="bring-form-field">
						<label><?php esc_html_e( 'Shipping Date', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
						<?php Bring_Booking_Common_View::render_shipping_date_time( '_bring-modal-shipping-date' ); ?>
					</div>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php echo Bring_Booking_Common_View::booking_label( true ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-bring-modal-bulk-errors">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1 class="bgf-modal-header"><?php esc_html_e( 'Mybring Booking errors', 'bring-fraktguiden-for-woocommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'bring-fraktguiden-for-woocommerce' ); ?></span>
					</button>
				</header>
				<article id="bring-error-modal-content">
					<ul>
						<li>Error</li>
					</ul>
				</article>
				<footer>
					<div class="inner">
						<button class="modal-close button button-primary button-large"><?php esc_html_e( 'Close modal panel', 'bring-fraktguiden-for-woocommerce' ) ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
