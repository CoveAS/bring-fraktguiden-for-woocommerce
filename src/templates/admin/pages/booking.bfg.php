<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__booking">
	<div class="bfg-page__header">
		<h1>
			<t>Booking</t>
		</h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<form method="post" action="options.php" id="bfg-booking-form" novalidate>
			<?php settings_fields('bring_fraktguiden_booking'); ?>

			<bfg-section>
				<bfg-section.header title="MyBring Booking"
					description="Book orders directly from the order page with MyBring integration"></bfg-section.header>

				<bfg-section.section>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_enabled; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_without_bring; ?></div>
					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_test_mode_enabled; ?>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<bfg-section.header title="Shipping Address"
					description="By default, your WooCommerce store address is used as the &quot;from&quot; address during booking."></bfg-section.header>

				<bfg-section.section>
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
				</bfg-section.section>

				<bfg-section.header class="bfg-section__header--divider" title="Contact Information"></bfg-section.header>

				<bfg-section.section>
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
							aria-required="true"
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
							aria-required="true"
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
							aria-required="true"
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
							aria-required="true"
							required
						/>
					</bfg-field.text>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<bfg-section.header title="Processing"
					description="Change order status after booking or printing labels"></bfg-section.header>

				<bfg-section.section>
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

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<bfg-section.header title="Home Delivery"
					description="Configure package type for home delivery services"></bfg-section.header>

				<bfg-section.section>
					<bfg-field.select id="booking_home_delivery_package_type" name="booking_home_delivery_package_type"
						label="Package type for home delivery" description="Only applies to home delivery services">
						<?php foreach ($package_type_options as $value => $label) {
							$selected = $value === $package_type_value ? ' selected' : '';
							echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
						} ?>
					</bfg-field.select>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>
		</form>
	</div>
</div>

<script>
	(function () {
		'use strict';

		const form = document.getElementById('bfg-booking-form');
		const checkbox = document.querySelector('input[name="booking_use_custom_address"]');
		const addressFields = document.getElementById('bfg-custom-shipping-address');

		// Toggle custom address fields
		function toggleAddressFields() {
			addressFields.style.display = checkbox.checked ? 'block' : 'none';
		}

		checkbox.addEventListener('change', toggleAddressFields);
		toggleAddressFields();

		// Wire up aria-describedby for fields whose description is rendered by the component
		form.querySelectorAll('.bfg-field').forEach(function (fieldEl) {
			const input = fieldEl.querySelector('input, select, textarea');
			const desc = fieldEl.querySelector('.bfg-description');
			if (!input || !desc || !input.id) return;
			const descId = input.id + '-description';
			desc.id = descId;
			input.setAttribute('aria-describedby', descId);
		});

		// Validate a single required field using the bfgField utility
		function validateField(field) {
			const value = field.value.trim();
			if (!value) {
				bfgField.showError(field, '<?php esc_html_e('This field is required.', 'bring-fraktguiden-for-woocommerce'); ?>');
				return false;
			}
			if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
				bfgField.showError(field, '<?php esc_html_e('Please enter a valid email address.', 'bring-fraktguiden-for-woocommerce'); ?>');
				return false;
			}
			if (field.type === 'tel' && !/^[\d\s\-+()]{6,}$/.test(value)) {
				bfgField.showError(field, '<?php esc_html_e('Please enter a valid phone number.', 'bring-fraktguiden-for-woocommerce'); ?>');
				return false;
			}
			bfgField.clearError(field);
			return true;
		}

		// Blur validation — only trigger after the user has interacted with the field
		const requiredFields = form.querySelectorAll('[required]');
		requiredFields.forEach(function (field) {
			field.addEventListener('blur', function () {
				if (field.value.trim()) validateField(field);
			});
			field.addEventListener('input', function () {
				if (field.closest('.bfg-field--has-error')) validateField(field);
			});
		});

		// Submit-time validation — prevent submission if any visible required field is invalid
		form.addEventListener('submit', function (e) {
			let firstInvalid = null;
			requiredFields.forEach(function (field) {
				if (field.offsetParent === null) return; // skip hidden fields
				if (!validateField(field) && !firstInvalid) firstInvalid = field;
			});
			if (firstInvalid) e.preventDefault();
		});

	})();
</script>