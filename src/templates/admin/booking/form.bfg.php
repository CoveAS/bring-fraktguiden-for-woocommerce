<?php

use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguiden\Customs\NatureOfCargo;
use BringFraktguidenPro\Booking\Box\BookingForm;

/**
 * @var BookingForm          $form
 * @var Fraktguiden_Service  $service
 * @var Fraktguiden_Service[] $services
 * @var array                $customers
 * @var string               $customer_error
 * @var NatureOfCargo[]      $cargo_reasons
 * @var bool                 $needs_cargo
 * @var bool                 $wants_date
 * @var bool                 $has_shipping_line
 * @var bool                 $test_mode
 * @var string               $token
 */
?>

<div class="bfg-booking-form" data-bfg-form data-token="<?php echo esc_attr($token); ?>">

	<?php if (!$has_shipping_line) : ?>
		<bfg-notice><t>Add a shipping line to the order, then reload the page to book it.</t></bfg-notice>
	<?php endif; ?>

	<?php if ($customer_error) : ?>
		<bfg-notice><?php echo esc_html($customer_error); ?></bfg-notice>
	<?php endif; ?>

	<div class="bfg-booking-form__grid">

		<div class="bfg-field">
			<label for="bfg-bb-customer"><t>Customer number</t></label>
			<div class="bfg-input bfg-input--select">
				<select id="bfg-bb-customer" class="bfg-custom-select" data-field="customer_number">
					<?php foreach ($customers as $number => $label) : ?>
						<option value="<?php echo esc_attr($number); ?>" <?php selected((string) $number, $form->customer_number); ?>>
							<?php echo esc_html($label); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="bfg-field">
			<label for="bfg-bb-service"><t>Service</t></label>
			<div class="bfg-input bfg-input--select">
				<select id="bfg-bb-service" class="bfg-custom-select" data-field="service" data-bfg-reload>
					<?php if ($form->service && !isset($services[$form->service])) : ?>
						<?php // The order carries a service the shop no longer offers. Keep it, so booking the order does not silently change the service. ?>
						<option value="<?php echo esc_attr($form->service); ?>" selected>
							<?php echo esc_html($form->service); ?>
						</option>
					<?php endif; ?>
					<?php foreach ($services as $code => $option) : ?>
						<option value="<?php echo esc_attr($code); ?>" <?php selected((string) $code, $form->service); ?>>
							<?php echo esc_html($option->get_name_by_index()); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="bfg-field">
			<label for="bfg-bb-shipping-date"><t>Shipping date</t></label>
			<div class="bfg-booking-form__datetime">
				<input type="date" id="bfg-bb-shipping-date" data-field="shipping_date" value="<?php echo esc_attr($form->shipping_date); ?>">
				<input type="time" data-field="shipping_time" value="<?php echo esc_attr($form->shipping_time); ?>">
			</div>
		</div>

		<?php if ($wants_date) : ?>
			<div class="bfg-field">
				<label for="bfg-bb-delivery-date"><t>Delivery date the customer asked for</t></label>
				<div class="bfg-booking-form__datetime">
					<input type="date" id="bfg-bb-delivery-date" data-field="delivery_date" value="<?php echo esc_attr($form->delivery_date); ?>">
					<input type="time" data-field="delivery_time" value="<?php echo esc_attr($form->delivery_time); ?>">
				</div>
			</div>
		<?php endif; ?>

		<?php if ($needs_cargo) : ?>
			<div class="bfg-field">
				<label for="bfg-bb-cargo"><t>Why the goods move</t></label>
				<div class="bfg-input bfg-input--select">
					<select id="bfg-bb-cargo" class="bfg-custom-select" data-field="nature_of_cargo">
						<?php foreach ($cargo_reasons as $reason) : ?>
							<option value="<?php echo esc_attr($reason->value); ?>" <?php selected($reason->value, $form->nature_of_cargo); ?>>
								<?php echo esc_html($reason->label()); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		<?php endif; ?>

	</div>

	<h4 class="bfg-booking-form__title"><t>Packages</t></h4>

	<div class="bfg-booking-packages">
		<div class="bfg-booking-packages__head">
			<span><t>Weight</t></span>
			<span><t>Length</t></span>
			<span><t>Width</t></span>
			<span><t>Height</t></span>
			<span></span>
		</div>

		<div data-bfg-packages>
			<?php foreach ($form->packages as $index => $package) : ?>
				<div class="bfg-booking-packages__row" data-bfg-package>
					<div class="bfg-input bfg-input--number">
						<input type="number" step="0.01" min="0" data-package-field="weight_in_kg" value="<?php echo esc_attr($package->weight_in_kg); ?>" aria-label="<?php esc_attr_e('Weight in kg', 'bring-fraktguiden-for-woocommerce'); ?>">
						<span class="bfg-suffix">kg</span>
					</div>
					<div class="bfg-input bfg-input--number">
						<input type="number" step="1" min="0" data-package-field="length" value="<?php echo esc_attr($package->length); ?>" aria-label="<?php esc_attr_e('Length in cm', 'bring-fraktguiden-for-woocommerce'); ?>">
						<span class="bfg-suffix">cm</span>
					</div>
					<div class="bfg-input bfg-input--number">
						<input type="number" step="1" min="0" data-package-field="width" value="<?php echo esc_attr($package->width); ?>" aria-label="<?php esc_attr_e('Width in cm', 'bring-fraktguiden-for-woocommerce'); ?>">
						<span class="bfg-suffix">cm</span>
					</div>
					<div class="bfg-input bfg-input--number">
						<input type="number" step="1" min="0" data-package-field="height" value="<?php echo esc_attr($package->height); ?>" aria-label="<?php esc_attr_e('Height in cm', 'bring-fraktguiden-for-woocommerce'); ?>">
						<span class="bfg-suffix">cm</span>
					</div>
					<button type="button" class="bfg-btn bfg-btn--secondary bfg-btn--sm bfg-btn--icon-only bfg-booking-packages__remove" data-bfg-remove-package aria-label="<?php esc_attr_e('Remove package', 'bring-fraktguiden-for-woocommerce'); ?>">&times;</button>
				</div>
			<?php endforeach; ?>
		</div>

		<button type="button" class="bfg-btn bfg-btn--secondary bfg-btn--sm" data-bfg-add-package><t>Add a package</t></button>
	</div>

	<?php if ($service && $service->vas) : ?>
		<h4 class="bfg-booking-form__title"><t>Extra services</t></h4>

		<div class="bfg-booking-form__services">
			<?php foreach ($service->vas as $vas) : ?>
				<label class="bfg-booking-form__service">
					<input type="checkbox" data-vas="<?php echo esc_attr($vas->code); ?>" <?php checked($form->books($vas->code)); ?>>
					<span><?php echo esc_html($vas->name); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="bfg-booking-form__grid">
		<div class="bfg-field">
			<label for="bfg-bb-info-sender"><t>Extra address line, sender</t></label>
			<textarea id="bfg-bb-info-sender" rows="2" data-field="info_sender"><?php echo esc_textarea($form->info_sender); ?></textarea>
		</div>

		<div class="bfg-field">
			<label for="bfg-bb-info-recipient"><t>Extra address line, recipient</t></label>
			<textarea id="bfg-bb-info-recipient" rows="2" data-field="info_recipient"><?php echo esc_textarea($form->info_recipient); ?></textarea>
		</div>
	</div>

	<?php if ($order->get_customer_note()) : ?>
		<p class="bfg-booking-form__note">
			<strong><t>Note from the customer</t>:</strong>
			<?php echo esc_html($order->get_customer_note()); ?>
		</p>
	<?php endif; ?>

	<details class="bfg-booking-form__parties">
		<summary><t>Sender and recipient</t></summary>
		<?php require __DIR__ . '/parties.php'; ?>
	</details>

	<div class="bfg-booking-form__actions">
		<p
			class="bfg-booking-form__status"
			data-bfg-status
			aria-live="polite"
			data-saving="<?php esc_attr_e('Saving…', 'bring-fraktguiden-for-woocommerce'); ?>"
			data-saved="<?php esc_attr_e('Saved at %s', 'bring-fraktguiden-for-woocommerce'); ?>"
			data-failed="<?php esc_attr_e('Not saved. Edit a field to try again.', 'bring-fraktguiden-for-woocommerce'); ?>"
			hidden
		></p>
		<button type="button" class="bfg-btn bfg-btn--secondary" data-bfg-reset><t>Reset</t></button>
		<button type="button" class="bfg-btn bfg-btn--primary" data-bfg-book <?php disabled(!$has_shipping_line); ?>>
			<?php if ($test_mode) : ?>
				<t>Book with Bring, test mode</t>
			<?php else : ?>
				<t>Book with Bring</t>
			<?php endif; ?>
		</button>
	</div>
</div>
