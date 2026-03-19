<?php

use BringFraktguiden\Admin\Component;
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
			<h1><?php esc_html_e('Booking', 'bring-fraktguiden-for-woocommerce'); ?></h1>
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
						<bfg-field.text
							id="booking_address_store_name"
							label="Store Name"
							description="Your business name as it appears on shipping labels.">
							<input
								type="text"
								id="booking_address_store_name"
								name="booking_address_store_name"
								maxlength="35"
								placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>"
							/>
						</bfg-field.text>

						<div class="bfg-field">
							<label
								for="booking_address_street1"><?php esc_html_e('Street Address 1', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<input type="text" id="booking_address_street1" name="booking_address_street1"
								maxlength="35" autocomplete="address-line1" />
						</div>

						<div class="bfg-field">
							<label
								for="booking_address_street2"><?php esc_html_e('Street Address 2', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<input type="text" id="booking_address_street2" name="booking_address_street2"
								maxlength="35" autocomplete="address-line2" />
						</div>

						<div class="bfg-field">
							<h3 class="bfg-field-group-title">
								<?php esc_html_e('Address details', 'bring-fraktguiden-for-woocommerce'); ?></h3>
							<div class="bfgu:flex bfgu:flex-col bfgu:gap-8">
								<div class="bfgu:flex-1">
									<label
										for="booking_address_postcode"><?php esc_html_e('Postcode', 'bring-fraktguiden-for-woocommerce'); ?></label>
									<input type="text" id="booking_address_postcode" name="booking_address_postcode"
										autocomplete="postal-code" />
								</div>
								<div class="bfgu:flex-1">
									<label
										for="booking_address_city"><?php esc_html_e('City', 'bring-fraktguiden-for-woocommerce'); ?></label>
									<input type="text" id="booking_address_city" name="booking_address_city"
										autocomplete="address-level2" />
								</div>
								<div class="bfgu:flex-1">
									<label
										for="booking_address_country"><?php esc_html_e('Country', 'bring-fraktguiden-for-woocommerce'); ?></label>
									<?php
									$countries = WC()->countries?->get_countries() ?: [];
									$base_country = WC()->countries?->get_base_country() ?: '';
									echo Component::customSelect('booking_address_country', $countries, $base_country);
									?>
								</div>
							</div>
						</div>
					</div>
				</bfg-box.section>

				<bfg-box.header class="bfg-box__header--divider" title="Contact Information"></bfg-box.header>

				<bfg-box.section>
					<?php echo Component::validatedInputField([
						'id' => 'booking_address_reference',
						'name' => 'booking_address_reference',
						'type' => 'text',
						'label' => __('Reference', 'bring-fraktguiden-for-woocommerce'),
						'required' => true,
						'validation' => ['required'],
						'error_message' => __('Reference is required', 'bring-fraktguiden-for-woocommerce'),
						'description' => sprintf(
							__('The store\'s reference printed on the shipping label. Usually %s, but can also be %s.', 'bring-fraktguiden-for-woocommerce'),
							'<code>{order_id}</code>',
							'<code>{products}</code>'
						),
						'placeholder' => __('e.g. {order_id}', 'bring-fraktguiden-for-woocommerce'),
						'maxlength' => 35,
					]); ?>

					<?php echo Component::validatedInputField([
						'id' => 'booking_address_contact_person',
						'name' => 'booking_address_contact_person',
						'type' => 'text',
						'label' => __('Contact Person', 'bring-fraktguiden-for-woocommerce'),
						'required' => true,
						'validation' => ['required'],
						'error_message' => __('Contact person is required', 'bring-fraktguiden-for-woocommerce'),
						'autocomplete' => 'name',
					]); ?>

					<?php echo Component::validatedInputField([
						'id' => 'booking_address_phone',
						'name' => 'booking_address_phone',
						'type' => 'tel',
						'label' => __('Phone', 'bring-fraktguiden-for-woocommerce'),
						'required' => true,
						'validation' => ['required', 'phone'],
						'error_message' => __('Valid phone number is required', 'bring-fraktguiden-for-woocommerce'),
						'autocomplete' => 'tel',
					]); ?>

					<?php echo Component::validatedInputField([
						'id' => 'booking_address_email',
						'name' => 'booking_address_email',
						'type' => 'email',
						'label' => __('Email', 'bring-fraktguiden-for-woocommerce'),
						'required' => true,
						'validation' => ['required', 'email'],
						'error_message' => __('Valid email address is required', 'bring-fraktguiden-for-woocommerce'),
						'autocomplete' => 'email',
					]); ?>

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

					<div class="bfg-field">
						<label
							for="auto_set_status_after_booking_success"><?php esc_html_e('Order status after booking', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<?php
						$order_statuses = wc_get_order_statuses();
						$saved_booking_status = \Bring_Fraktguiden\Common\Fraktguiden_Helper::get_option('auto_set_status_after_booking_success');
						$booking_status_value = !empty($saved_booking_status) ? $saved_booking_status : 'wc-bring-shipment';
						$booking_status_options = array_merge(
							['none' => __('None', 'bring-fraktguiden-for-woocommerce')],
							$order_statuses
						);
						echo Component::customSelect('auto_set_status_after_booking_success', $booking_status_options, $booking_status_value);
						?>
						<p class="bfg-description">
							<?php esc_html_e('Order status will be automatically set when successfully booked', 'bring-fraktguiden-for-woocommerce'); ?>
						</p>
					</div>

					<div class="bfg-field">
						<label
							for="auto_set_status_after_print_label_success"><?php esc_html_e('Order status after printing', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<?php
						$saved_print_status = \Bring_Fraktguiden\Common\Fraktguiden_Helper::get_option('auto_set_status_after_print_label_success');
						$print_status_value = !empty($saved_print_status) ? $saved_print_status : 'none';
						$print_status_options = array_merge(
							['none' => __('None', 'bring-fraktguiden-for-woocommerce')],
							$order_statuses
						);
						echo Component::customSelect('auto_set_status_after_print_label_success', $print_status_options, $print_status_value);
						?>
						<p class="bfg-description">
							<?php esc_html_e('Order status will be automatically set when a label is downloaded', 'bring-fraktguiden-for-woocommerce'); ?>
						</p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</bfg-box.section>
			</bfg-box>

			<bfg-box>
				<bfg-box.header title="Home Delivery"
					description="Configure package type for home delivery services"></bfg-box.header>

				<bfg-box.section>
					<div class="bfg-field">
						<label
							for="booking_home_delivery_package_type"><?php esc_html_e('Package type for home delivery', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<?php
						$saved_package_type = \Bring_Fraktguiden\Common\Fraktguiden_Helper::get_option('booking_home_delivery_package_type');
						$package_type_value = !empty($saved_package_type) ? $saved_package_type : 'hd_eur';
						$package_type_options = [
							'hd_eur' => 'HD_EUR_PALLET',
							'hd_half' => 'HD_HALF_PALLET',
							'hd_quarter' => 'HD_QUARTER_PALLET',
							'hd_loose' => 'HD_SPECIAL_PALLET',
						];
						echo Component::customSelect('booking_home_delivery_package_type', $package_type_options, $package_type_value);
						?>
						<p class="bfg-description">
							<?php esc_html_e('Only applies to home delivery services', 'bring-fraktguiden-for-woocommerce'); ?>
						</p>
					</div>

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