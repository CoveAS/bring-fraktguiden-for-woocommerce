<?php

use BringFraktguiden\Admin\Component;
use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var string $currency
 * @var Fields $fields
 */
?>

<style>
/* Form validation styles */
.bfg-field--valid input,
.bfg-field--valid select {
	border-color: #16a34a !important;
}

.bfg-field--error input,
.bfg-field--error select {
	border-color: #dc2626 !important;
}

.bfg-field__validation {
	display: none;
	align-items: center;
	gap: 8px;
	margin-top: 8px;
	font-size: 13px;
}

.bfg-field__validation.is-visible {
	display: flex;
}

.bfg-field__validation--error {
	color: #dc2626;
}

.bfg-field__validation-icon {
	width: 16px;
	height: 16px;
	flex-shrink: 0;
}

/* Required asterisk */
.bfg-required {
	color: #dc2626;
	font-weight: 600;
}

/* Submit button states */
.bfg-submit-wrapper {
	position: relative;
	display: inline-flex;
	align-items: center;
	gap: 16px;
}

.bfg-submit-wrapper .button-primary {
	transition: opacity 0.2s, background-color 0.2s;
}

.bfg-submit-wrapper .button-primary:disabled {
	opacity: 0.6;
	cursor: not-allowed;
}

.bfg-submit-wrapper.is-loading .button-primary {
	color: transparent;
}

.bfg-spinner {
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	width: 18px;
	height: 18px;
	border: 2px solid #fff;
	border-top-color: transparent;
	border-radius: 50%;
	animation: bfg-spin 0.8s linear infinite;
	opacity: 0;
	pointer-events: none;
}

.bfg-submit-wrapper.is-loading .bfg-spinner {
	opacity: 1;
}

@keyframes bfg-spin {
	to { transform: translate(-50%, -50%) rotate(360deg); }
}

.bfg-save-success {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: #16a34a;
	font-size: 14px;
	opacity: 0;
	transform: translateX(-10px);
	transition: opacity 0.3s, transform 0.3s;
}

.bfg-save-success.is-visible {
	opacity: 1;
	transform: translateX(0);
}

/* Responsive grid */
@media (max-width: 600px) {
	.bfgu-flex.bfgu-gap-8 {
		flex-direction: column;
	}

	.bfgu-flex-1 {
		width: 100% !important;
	}
}

/* Screen reader only */
.sr-only {
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
	white-space: nowrap;
	border: 0;
}
</style>

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

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('MyBring Booking', 'bring-fraktguiden-for-woocommerce'),
					__('Book orders directly from the order page with MyBring integration', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field bfg-field--checkbox-box">
						<div class="bfg-input--checkbox">
							<label>
								<input type="checkbox" name="booking_enabled" value="yes" />
								<span><?php esc_html_e('Enable MyBring booking', 'bring-fraktguiden-for-woocommerce'); ?></span>
							</label>
							<p class="bfg-description"><?php esc_html_e('Allow booking shipments directly from WooCommerce order pages', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>
					</div>

					<div class="bfg-field bfg-field--checkbox-box">
						<div class="bfg-input--checkbox">
							<label>
								<input type="checkbox" name="booking_without_bring" value="yes" />
								<span><?php esc_html_e('Allow booking without Bring shipping', 'bring-fraktguiden-for-woocommerce'); ?></span>
							</label>
							<p class="bfg-description"><?php esc_html_e('Enable booking for orders that don\'t use Bring shipping methods', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>
					</div>

					<div class="bfg-field bfg-field--checkbox-box">
						<div class="bfg-input--checkbox">
							<label>
								<input type="checkbox" name="booking_test_mode_enabled" value="yes" checked />
								<span><?php esc_html_e('Enable test mode for MyBring booking', 'bring-fraktguiden-for-woocommerce'); ?></span>
							</label>
							<p class="bfg-description"><?php esc_html_e('When enabled, bookings will not be invoiced or fulfilled by Bring', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Shipping Address', 'bring-fraktguiden-for-woocommerce'),
					__('By default, your WooCommerce store address is used as the "from" address during booking.', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field bfg-field--checkbox-box">
						<div class="bfg-input--checkbox">
							<label>
								<input type="checkbox" name="booking_use_custom_address" id="booking_use_custom_address" value="yes" />
								<span><?php esc_html_e('Use a different shipping address', 'bring-fraktguiden-for-woocommerce'); ?></span>
							</label>
							<p class="bfg-description"><?php esc_html_e('Enable this if you ship from a different address than your WooCommerce store address.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>
					</div>

					<div id="bfg-custom-shipping-address" style="display: none;">
						<div class="bfg-field">
							<label for="booking_address_store_name"><?php esc_html_e('Store Name', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<input
								type="text"
								id="booking_address_store_name"
								name="booking_address_store_name"
								maxlength="35"
								placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>"
								aria-describedby="store_name_help"
							/>
							<p class="bfg-description" id="store_name_help"><?php esc_html_e('Your business name as it appears on shipping labels.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						</div>

						<div class="bfg-field">
							<label for="booking_address_street1"><?php esc_html_e('Street Address 1', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<input
								type="text"
								id="booking_address_street1"
								name="booking_address_street1"
								maxlength="35"
								autocomplete="address-line1"
							/>
						</div>

						<div class="bfg-field">
							<label for="booking_address_street2"><?php esc_html_e('Street Address 2', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<input
								type="text"
								id="booking_address_street2"
								name="booking_address_street2"
								maxlength="35"
								autocomplete="address-line2"
							/>
						</div>

						<div class="bfg-field">
							<label><?php esc_html_e('Address details', 'bring-fraktguiden-for-woocommerce'); ?></label>
							<div class="bfgu-flex bfgu-flex-col bfgu-gap-8">
								<div class="bfgu-flex-1">
									<label for="booking_address_postcode"><?php esc_html_e('Postcode', 'bring-fraktguiden-for-woocommerce'); ?></label>
									<input
										type="text"
										id="booking_address_postcode"
										name="booking_address_postcode"
										autocomplete="postal-code"
									/>
								</div>
								<div class="bfgu-flex-1">
									<label for="booking_address_city"><?php esc_html_e('City', 'bring-fraktguiden-for-woocommerce'); ?></label>
									<input
										type="text"
										id="booking_address_city"
										name="booking_address_city"
										autocomplete="address-level2"
									/>
								</div>
								<div class="bfgu-flex-1">
									<label for="booking_address_country"><?php esc_html_e('Country', 'bring-fraktguiden-for-woocommerce'); ?></label>
									<?php
									$countries = WC()->countries?->get_countries() ?: [];
									$base_country = WC()->countries?->get_base_country() ?: '';
									echo Component::customSelect('booking_address_country', $countries, $base_country);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo Component::boxHeader(
					__('Contact Information', 'bring-fraktguiden-for-woocommerce'),
					'',
					true
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field" data-validate="required">
						<label for="booking_address_reference">
							<?php esc_html_e('Reference', 'bring-fraktguiden-for-woocommerce'); ?>
							<span class="bfg-required" aria-hidden="true">*</span>
						</label>
						<input
							type="text"
							id="booking_address_reference"
							name="booking_address_reference"
							maxlength="35"
							placeholder="<?php esc_attr_e('e.g. {order_id}', 'bring-fraktguiden-for-woocommerce'); ?>"
							required
							aria-required="true"
							aria-describedby="reference_help reference_error"
						/>
						<div class="bfg-field__validation bfg-field__validation--error" id="reference_error" role="alert">
							<svg class="bfg-field__validation-icon" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
							</svg>
							<span><?php esc_html_e('Reference is required', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
							<p class="bfg-description" id="reference_help">
							<?php echo sprintf(
								esc_html__('The store\'s reference printed on the shipping label. Usually %s, but can also be %s.', 'bring-fraktguiden-for-woocommerce'),
								'<code>{order_id}</code>',
								'<code>{products}</code>'
							); ?>
						</p>
					</div>

					<div class="bfg-field" data-validate="required">
						<label for="booking_address_contact_person">
							<?php esc_html_e('Contact Person', 'bring-fraktguiden-for-woocommerce'); ?>
							<span class="bfg-required" aria-hidden="true">*</span>
						</label>
						<input
							type="text"
							id="booking_address_contact_person"
							name="booking_address_contact_person"
							required
							aria-required="true"
							aria-describedby="contact_person_error"
							autocomplete="name"
						/>
						<div class="bfg-field__validation bfg-field__validation--error" id="contact_person_error" role="alert">
							<svg class="bfg-field__validation-icon" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
							</svg>
							<span><?php esc_html_e('Contact person is required', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</div>

					<div class="bfg-field" data-validate="required|phone">
						<label for="booking_address_phone">
							<?php esc_html_e('Phone', 'bring-fraktguiden-for-woocommerce'); ?>
							<span class="bfg-required" aria-hidden="true">*</span>
						</label>
						<input
							type="tel"
							id="booking_address_phone"
							name="booking_address_phone"
							required
							aria-required="true"
							aria-describedby="phone_error"
							autocomplete="tel"
						/>
						<div class="bfg-field__validation bfg-field__validation--error" id="phone_error" role="alert">
							<svg class="bfg-field__validation-icon" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
							</svg>
							<span><?php esc_html_e('Valid phone number is required', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</div>

					<div class="bfg-field" data-validate="required|email">
						<label for="booking_address_email">
							<?php esc_html_e('Email', 'bring-fraktguiden-for-woocommerce'); ?>
							<span class="bfg-required" aria-hidden="true">*</span>
						</label>
						<input
							type="email"
							id="booking_address_email"
							name="booking_address_email"
							required
							aria-required="true"
							aria-describedby="email_error"
							autocomplete="email"
						/>
						<div class="bfg-field__validation bfg-field__validation--error" id="email_error" role="alert">
							<svg class="bfg-field__validation-icon" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
							</svg>
							<span><?php esc_html_e('Valid email address is required', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Processing', 'bring-fraktguiden-for-woocommerce'),
					__('Change order status after booking or printing labels', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<?php echo Component::noticeBanner(
						'<strong>' . esc_html__('WARNING!', 'bring-fraktguiden-for-woocommerce') . '</strong> ' .
						esc_html__('This will change the status even if the order is completed', 'bring-fraktguiden-for-woocommerce'),
						'warning'
					); ?>

					<div class="bfg-field">
						<label for="auto_set_status_after_booking_success"><?php esc_html_e('Order status after booking', 'bring-fraktguiden-for-woocommerce'); ?></label>
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
						<p class="bfg-description"><?php esc_html_e('Order status will be automatically set when successfully booked', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<div class="bfg-field">
						<label for="auto_set_status_after_print_label_success"><?php esc_html_e('Order status after printing', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<?php
						$saved_print_status = \Bring_Fraktguiden\Common\Fraktguiden_Helper::get_option('auto_set_status_after_print_label_success');
						$print_status_value = !empty($saved_print_status) ? $saved_print_status : 'none';
						$print_status_options = array_merge(
							['none' => __('None', 'bring-fraktguiden-for-woocommerce')],
							$order_statuses
						);
						echo Component::customSelect('auto_set_status_after_print_label_success', $print_status_options, $print_status_value);
						?>
						<p class="bfg-description"><?php esc_html_e('Order status will be automatically set when a label is downloaded', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<?php echo Component::boxHeader(
					__('Home Delivery', 'bring-fraktguiden-for-woocommerce'),
					__('Configure package type for home delivery services', 'bring-fraktguiden-for-woocommerce')
				); ?>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<label for="booking_home_delivery_package_type"><?php esc_html_e('Package type for home delivery', 'bring-fraktguiden-for-woocommerce'); ?></label>
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
						<p class="bfg-description"><?php esc_html_e('Only applies to home delivery services', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
(function() {
	'use strict';

	const form = document.getElementById('bfg-booking-form');
	const checkbox = document.getElementById('booking_use_custom_address');
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
