<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__booking">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1>
				<t>Booking</t>
			</h1>
		</div>
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<form method="post" action="options.php" id="bfg-booking-form" novalidate>
			<?php settings_fields('bring_fraktguiden_booking'); ?>

			<!-- Live region for screen reader announcements -->
			<div aria-live="polite" aria-atomic="true" class="sr-only" id="bfg-form-announcements"></div>

			<bfg-box>
				<bfg-box.header title="MyBring Booking"
					description="Book orders directly from the order page with MyBring integration"></bfg-box.header>

				<bfg-box.section>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_enabled; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_without_bring; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_test_mode_enabled; ?>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</bfg-box.section>
			</bfg-box>

			<bfg-box>
				<bfg-box.header title="Shipping Address"
					description="By default, your WooCommerce store address is used as the &quot;from&quot; address during booking."></bfg-box.header>

				<bfg-box.section>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_use_custom_address; ?>
					</div>

					<div id="bfg-custom-shipping-address" style="">
						<bfg-field.text id="booking_address_store_name" label="Store Name"
							description="Your business name as it appears on shipping labels.">
							<input type="text" id="booking_address_store_name" name="booking_address_store_name"
								maxlength="35" placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>" />
						</bfg-field.text>

						<div class="bfg-field">
							<label for="booking_address_street1">
								<t>Street Address 1</t>
							</label>
							<input type="text" id="booking_address_street1" name="booking_address_street1"
								maxlength="35" autocomplete="address-line1" />
						</div>

						<div class="bfg-field">
							<label for="booking_address_street2">
								<t>Street Address 2</t>
							</label>
							<input type="text" id="booking_address_street2" name="booking_address_street2"
								maxlength="35" autocomplete="address-line2" />
						</div>

						<div class="bfg-field">
							<h3 class="bfg-field-group-title">
								<t>Address details</t>
							</h3>
							<div class="bfgu:flex bfgu:flex-col bfgu:gap-8">
								<div class="bfgu:flex-1">
									<label for="booking_address_postcode">
										<t>Postcode</t>
									</label>
									<input type="text" id="booking_address_postcode" name="booking_address_postcode"
										autocomplete="postal-code" />
								</div>
								<div class="bfgu:flex-1">
									<label for="booking_address_city">
										<t>City</t>
									</label>
									<input type="text" id="booking_address_city" name="booking_address_city"
										autocomplete="address-level2" />
								</div>
								<div class="bfgu:flex-1">
									<bfg-field.select id="booking_address_country" name="booking_address_country"
										label="Country">
										<?php foreach ($countries as $code => $name) {
											$selected = $code === $base_country ? ' selected' : '';
											echo '<option value="' . esc_attr($code) . '"' . $selected . '>' . esc_html($name) . '</option>';
										} ?>
									</bfg-field.select>
								</div>
							</div>
						</div>
					</div>
				</bfg-box.section>

				<bfg-box.header class="bfg-box__header--divider" title="Contact Information"></bfg-box.header>

				<bfg-box.section>
					<bfg-field.text
						id="booking_address_reference"
						label="Reference"
						description="The store's reference printed on the shipping label. Usually {order_id}, but can also be {products}.">
						<input
							type="text"
							id="booking_address_reference"
							name="booking_address_reference"
							placeholder="<?php echo esc_attr__('e.g. {order_id}', 'bring-fraktguiden-for-woocommerce'); ?>"
							maxlength="35"
							required
						/>
					</bfg-field.text>

					<bfg-field.text
						id="booking_address_contact_person"
						label="Contact Person">
						<input
							type="text"
							id="booking_address_contact_person"
							name="booking_address_contact_person"
							autocomplete="name"
							required
						/>
					</bfg-field.text>

					<bfg-field.text
						id="booking_address_phone"
						label="Phone">
						<input
							type="tel"
							id="booking_address_phone"
							name="booking_address_phone"
							autocomplete="tel"
							required
						/>
					</bfg-field.text>

					<bfg-field.text
						id="booking_address_email"
						label="Email">
						<input
							type="email"
							id="booking_address_email"
							name="booking_address_email"
							autocomplete="email"
							required
						/>
					</bfg-field.text>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</bfg-box.section>
			</bfg-box>

			<bfg-box>
				<bfg-box.header title="Processing"
					description="Change order status after booking or printing labels"></bfg-box.header>

				<bfg-box.section>
					<bfg-notice type="warning">
						<strong><bfg-t>WARNING!</bfg-t></strong> <bfg-t>This will change the status even if the order is
							completed</bfg-t>
					</bfg-notice>

					<bfg-field.select id="auto_set_status_after_booking_success"
						name="auto_set_status_after_booking_success" label="Order status after booking"
						description="Order status will be automatically set when successfully booked">
						<?php foreach ($booking_status_options as $value => $label) {
							$selected = $value === $booking_status_value ? ' selected' : '';
							echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
						} ?>
					</bfg-field.select>

					<bfg-field.select id="auto_set_status_after_print_label_success"
						name="auto_set_status_after_print_label_success" label="Order status after printing"
						description="Order status will be automatically set when a label is downloaded">
						<?php foreach ($print_status_options as $value => $label) {
							$selected = $value === $print_status_value ? ' selected' : '';
							echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
						} ?>
					</bfg-field.select>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</bfg-box.section>
			</bfg-box>

			<bfg-box>
				<bfg-box.header title="Home Delivery"
					description="Configure package type for home delivery services"></bfg-box.header>

				<bfg-box.section>
					<bfg-field.select id="booking_home_delivery_package_type" name="booking_home_delivery_package_type"
						label="Package type for home delivery" description="Only applies to home delivery services">
						<?php foreach ($package_type_options as $value => $label) {
							$selected = $value === $package_type_value ? ' selected' : '';
							echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
						} ?>
					</bfg-field.select>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</bfg-box.section>
			</bfg-box>
		</form>
	</div>
</div>

<script>
	(function () {
		'use strict';

		const form = document.getElementById('bfg-booking-form');
		const checkbox = document.querySelector('input[name="booking_use_custom_address"]');
		const addressFields = document.getElementById('bfg-custom-shipping-address');
		const announcements = document.getElementById('bfg-form-announcements');

		// Toggle custom address fields
		function toggleAddressFields() {
			addressFields.style.display = checkbox.checked ? 'block' : 'none';
		}

		checkbox.addEventListener('change', toggleAddressFields);
		toggleAddressFields();

		// Validation functions
		const validators = {
			required: (value) => value.trim().length > 0,
			email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
			phone: (value) => /^[\d\s\-+()]{6,}$/.test(value.trim())
		};

		// Validate a single field
		function validateField(field) {
			const container = field.closest('[data-validate]') || field.closest('.bfg-field');
			if (!container || !container.dataset.validate) return true;

			const rules = container.dataset.validate.split('|');
			const value = field.value;
			let isValid = true;

			for (const rule of rules) {
				if (validators[rule] && !validators[rule](value)) {
					isValid = false;
					break;
				}
			}

			// Update visual state
			const errorEl = container.querySelector('.bfg-field__validation--error');

			container.classList.remove('bfg-field--valid', 'bfg-field--error');
			if (errorEl) errorEl.classList.remove('is-visible');

			if (value.trim().length > 0) {
				if (isValid) {
					container.classList.add('bfg-field--valid');
				} else {
					container.classList.add('bfg-field--error');
					if (errorEl) errorEl.classList.add('is-visible');
				}
			}

			return isValid;
		}

		// Add blur validation to all required fields
		const requiredFields = form.querySelectorAll('[required]');
		requiredFields.forEach(field => {
			field.addEventListener('blur', () => validateField(field));
			field.addEventListener('input', () => {
				// Clear error state on input
				const container = field.closest('[data-validate]') || field.closest('.bfg-field');
				if (container && container.classList.contains('bfg-field--error')) {
					validateField(field);
				}
			});
		});

		// Announce validation errors for screen readers
		function announceError(message) {
			announcements.textContent = message;
			setTimeout(() => {
				announcements.textContent = '';
			}, 1000);
		}

	})();
</script>