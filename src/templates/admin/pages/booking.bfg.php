<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Fields;

/**
 * @var Fields $fields
 */
?>

<div class="wrap bfg bfg-admin-page bfg-admin-page__booking">
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
					<div class="bfg-checkbox-group">
						<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_enabled; ?></div>
						<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_without_bring; ?></div>
						<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->booking_test_mode_enabled; ?>
						</div>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<bfg-section.header title="Contact Information"
					description="The sender name, phone and email Bring shows on every shipment."></bfg-section.header>

				<bfg-section.section>
					<div id="bfg-contact-information">
						<div class="bfg-field">
							<?php echo $fields->booking_address_reference->label(); ?>
							<?php echo $fields->booking_address_reference; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->booking_address_contact_person->label(); ?>
							<?php echo $fields->booking_address_contact_person; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->booking_address_phone->label(); ?>
							<?php echo $fields->booking_address_phone; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->booking_address_email->label(); ?>
							<?php echo $fields->booking_address_email; ?>
						</div>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<bfg-section.header title="Customs and NVIT"
					description="What Bring needs when a shipment crosses a border"></bfg-section.header>

				<bfg-section.section>
					<p>
						<?php echo strtr(
							esc_html__('NVIT means Norwegian goods in transit. A parcel that travels from one place in Norway to another, through Sweden or Finland, needs customs data for each order line. Read more at {{bring}}Bring{{/a}} and at {{toll}}the Norwegian Customs Authority{{/a}}.', 'bring-fraktguiden-for-woocommerce'),
							[
								'{{bring}}' => '<a href="https://www.bring.no/en/services/customs/norwegian-goods-in-transit-changes" target="_blank" rel="noopener">',
								'{{toll}}' => '<a href="https://www.toll.no/no/bedrift/transport-og-tollager/norske-varer-i-transitt" target="_blank" rel="noopener">',
								'{{/a}}' => '</a>',
							]
						); ?>
					</p>

					<div class="bfg-field bfg-field--checkbox-box"><?php echo $fields->customs_consent; ?></div>

					<div class="bfg-field">
						<?php echo $fields->customs_exporter_number->label(); ?>
						<?php echo $fields->customs_exporter_number; ?>
					</div>

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

					<div class="bfg-field">
						<?php echo $fields->auto_set_status_after_booking_success->label(); ?>
						<?php echo $fields->auto_set_status_after_booking_success; ?>
					</div>

					<div class="bfg-field">
						<?php echo $fields->auto_set_status_after_print_label_success->label(); ?>
						<?php echo $fields->auto_set_status_after_print_label_success; ?>
					</div>

					<button type="submit" class="bfg-btn bfg-btn--primary"><?php esc_html_e('Save Changes', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<bfg-section.header title="Home Delivery"
					description="Configure package type for home delivery services"></bfg-section.header>

				<bfg-section.section>
					<div class="bfg-field">
						<?php echo $fields->booking_home_delivery_package_type->label(); ?>
						<?php echo $fields->booking_home_delivery_package_type; ?>
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
						<div class="bfg-field">
							<?php echo $fields->booking_address_store_name->label(); ?>
							<?php echo $fields->booking_address_store_name; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->booking_address_street1->label(); ?>
							<?php echo $fields->booking_address_street1; ?>
						</div>

						<div class="bfg-field">
							<?php echo $fields->booking_address_street2->label(); ?>
							<?php echo $fields->booking_address_street2; ?>
						</div>

						<div class="bfg-field">
							<h3 class="bfg-field-group-title">
								<t>Address details</t>
							</h3>
							<div class="bfgu:flex bfgu:flex-col bfgu:gap-8">
								<div class="bfgu:flex-1">
									<?php echo $fields->booking_address_postcode->label(); ?>
									<?php echo $fields->booking_address_postcode->field(); ?>
								</div>
								<div class="bfgu:flex-1">
									<?php echo $fields->booking_address_city->label(); ?>
									<?php echo $fields->booking_address_city->field(); ?>
								</div>
								<div class="bfgu:flex-1">
									<?php echo $fields->booking_address_country->label(); ?>
									<?php echo $fields->booking_address_country->field(); ?>
								</div>
							</div>
						</div>
					</div>

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

		// Contact Information fields are only checked once the user edits one of them.
		// A shop saved before these fields existed can still save the other sections.
		const contactGroup = document.getElementById('bfg-contact-information');
		const contactSection = contactGroup.closest('.bfg-section__section');
		const contactFields = Array.from(contactGroup.querySelectorAll('[required]'));
		const loadedValues = new Map(contactFields.map(function (field) { return [field, field.value]; }));

		function contactEdited() {
			return contactFields.some(function (field) { return field.value !== loadedValues.get(field); });
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
			// The Save button of the Contact Information section always checks the fields.
			const skipContact = !contactEdited() && !contactSection.contains(e.submitter);

			let firstInvalid = null;
			requiredFields.forEach(function (field) {
				if (field.offsetParent === null) return; // skip hidden fields
				if (skipContact && contactFields.includes(field)) return;
				if (!validateField(field) && !firstInvalid) firstInvalid = field;
			});
			if (firstInvalid) e.preventDefault();
		});

	})();
</script>