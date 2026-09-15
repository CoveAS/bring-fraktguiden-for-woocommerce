<?php
/**
 * The dialogs of the bulk booking action on the orders list.
 *
 * @var array  $customers      Customer number => label.
 * @var string $customer_error Why the customer numbers are missing.
 * @var string $customer_number The customer number the shop books with.
 * @var array  $shipping_date  Keys date, hour and minute.
 * @var string $book_label     The label of the booking button.
 * @var string $bulk_url       The one route the modal loads its markup from.
 * @var string $bulk_nonce     The nonce of that route.
 */
?>
<dialog class="bfg bfg-modal bfg-modal--full" id="bfg-bulk-book">
	<div class="bfg-modal__head">
		<h2 class="bfg-modal__title"><t>Mybring Booking</t></h2>
		<button type="button" class="bfg-modal__close" data-bfg-dialog-close aria-label="<?php esc_attr_e('Close', 'bring-fraktguiden-for-woocommerce'); ?>">&times;</button>
	</div>
	<div class="bfg-modal__body">
		<?php if ($customer_error) : ?>
			<bfg-notice><?php echo esc_html($customer_error); ?></bfg-notice>
		<?php endif; ?>

		<div
			data-bfg-bulk-modal
			data-url="<?php echo esc_url($bulk_url); ?>"
			data-nonce="<?php echo esc_attr($bulk_nonce); ?>"
		></div>

		<div class="bfg-bulk-book__fields">
			<div class="bfg-field">
				<label for="bfg-bulk-book-customer"><t>Customer number</t></label>
				<div class="bfg-input bfg-input--select">
					<select id="bfg-bulk-book-customer" name="_bring-customer-number" class="bfg-custom-select">
						<?php foreach ($customers as $number => $label) : ?>
							<option value="<?php echo esc_attr($number); ?>" <?php selected((string) $number, (string) $customer_number); ?>>
								<?php echo esc_html($label); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="bfg-field">
				<label for="bfg-bulk-book-date"><t>Shipping date</t></label>
				<div class="bfg-booking-form__datetime">
					<input type="date" id="bfg-bulk-book-date" value="<?php echo esc_attr($shipping_date['date']); ?>">
					<input type="time" id="bfg-bulk-book-time" value="<?php echo esc_attr($shipping_date['hour'] . ':' . $shipping_date['minute']); ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="bfg-modal__foot">
		<button type="button" class="bfg-btn bfg-btn--primary bfg-btn--sm" id="bfg-bulk-book-send">
			<?php echo esc_html($book_label); ?>
		</button>
	</div>
</dialog>

<dialog class="bfg bfg-modal" id="bfg-bulk-result">
	<div class="bfg-modal__head">
		<h2 class="bfg-modal__title"><t>Mybring Booking</t></h2>
		<button type="button" class="bfg-modal__close" data-bfg-dialog-close aria-label="<?php esc_attr_e('Close', 'bring-fraktguiden-for-woocommerce'); ?>">&times;</button>
	</div>
	<div class="bfg-modal__body">
		<ul class="bfg-bulk-book__summary" id="bfg-bulk-result-summary"></ul>
		<ul class="bfg-bulk-book__errors" id="bfg-bulk-errors-list"></ul>
	</div>
	<div class="bfg-modal__foot">
		<a class="bfg-btn bfg-btn--primary bfg-btn--sm" id="bfg-bulk-result-print" href="#" target="_blank" rel="noopener" hidden><t>Print labels</t></a>
		<button type="button" class="bfg-btn bfg-btn--sm" data-bfg-dialog-close><t>Close</t></button>
	</div>
</dialog>
