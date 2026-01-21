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
			<h1><?php esc_html_e('Booking', 'bring-fraktguiden-for-woocommerce'); ?></h1>
		</div>
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php if (defined('BRING_ENVIRONMENT') && BRING_ENVIRONMENT === 'local'): ?>
			<div class="bfg-notice-banner">
				<span class="bfg-notice-icon">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</span>
				<p><?php esc_html_e('This site is running in a local environment and production settings has been deactivated.', 'bring-fraktguiden-for-woocommerce'); ?></p>
			</div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields('bring_fraktguiden_booking'); ?>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<h2><?php esc_html_e('MyBring Booking', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Book orders directly from the order page with MyBring integration', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

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
				<div class="bfg-box__header">
					<h2><?php esc_html_e('Store Address and Contact Information', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('This address will be your "from" address and will populate the details given to Bring during the booking process', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<label><?php esc_html_e('Store Name', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<input type="text" name="booking_address_store_name" maxlength="35" value="<?php echo esc_attr(get_bloginfo('name')); ?>" />
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Street Address 1', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<input type="text" name="booking_address_street1" maxlength="35" />
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Street Address 2', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<input type="text" name="booking_address_street2" maxlength="35" />
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Address details', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<div class="bfg-flex bfg-gap-8">
							<div class="bfg-field-sub">
								<label><?php esc_html_e('Postcode', 'bring-fraktguiden-for-woocommerce'); ?></label>
								<input type="text" name="booking_address_postcode" />
							</div>
							<div class="bfg-field-sub">
								<label><?php esc_html_e('City', 'bring-fraktguiden-for-woocommerce'); ?></label>
								<input type="text" name="booking_address_city" />
							</div>
							<div class="bfg-field-sub">
								<label><?php esc_html_e('Country', 'bring-fraktguiden-for-woocommerce'); ?></label>
								<select name="booking_address_country">
									<?php
									$countries = WC()->countries?->get_countries();
									$base_country = WC()->countries?->get_base_country();
									foreach ($countries as $code => $name) {
										printf(
											'<option value="%s" %s>%s</option>',
											esc_attr($code),
											selected($base_country, $code, false),
											esc_html($name)
										);
									}
									?>
								</select>
							</div>
						</div>
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Reference', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<input type="text" name="booking_address_reference" maxlength="35" />
						<p class="bfg-description">
							<?php echo sprintf(
								esc_html__('Specify shipper or consignee reference. Available macros: %s', 'bring-fraktguiden-for-woocommerce'),
								'{order_id}, {products}'
							); ?>
						</p>
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Contact Person', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<input type="text" name="booking_address_contact_person" />
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Contact details', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<div class="bfg-flex bfg-gap-8">
							<div class="bfg-field-sub">
								<label><?php esc_html_e('Phone', 'bring-fraktguiden-for-woocommerce'); ?></label>
								<input type="text" name="booking_address_phone" />
							</div>
							<div class="bfg-field-sub">
								<label><?php esc_html_e('Email', 'bring-fraktguiden-for-woocommerce'); ?></label>
								<input type="email" name="booking_address_email" />
							</div>
						</div>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<h2><?php esc_html_e('Processing', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Change order status after booking or printing labels', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-box__section">
					<div class="bfg-notice-banner">
						<span class="bfg-notice-icon">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>
						<p>
							<strong><?php esc_html_e('WARNING!', 'bring-fraktguiden-for-woocommerce'); ?></strong>
							<?php esc_html_e('This will change the status even if the order is completed', 'bring-fraktguiden-for-woocommerce'); ?>
						</p>
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Order status after booking', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<select name="auto_set_status_after_booking_success">
							<option value="none"><?php esc_html_e('None', 'bring-fraktguiden-for-woocommerce'); ?></option>
							<?php
							$order_statuses = wc_get_order_statuses();
							foreach ($order_statuses as $status => $label) {
								printf('<option value="%s">%s</option>', esc_attr($status), esc_html($label));
							}
							?>
						</select>
						<p class="bfg-description"><?php esc_html_e('Order status will be automatically set when successfully booked', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<div class="bfg-field">
						<label><?php esc_html_e('Order status after printing', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<select name="auto_set_status_after_print_label_success">
							<option value="none"><?php esc_html_e('None', 'bring-fraktguiden-for-woocommerce'); ?></option>
							<?php
							foreach ($order_statuses as $status => $label) {
								printf('<option value="%s">%s</option>', esc_attr($status), esc_html($label));
							}
							?>
						</select>
						<p class="bfg-description"><?php esc_html_e('Order status will be automatically set when a label is downloaded', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<h2><?php esc_html_e('Home Delivery', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p><?php esc_html_e('Configure package type for home delivery services', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-box__section">
					<div class="bfg-field">
						<label><?php esc_html_e('Package type for home delivery', 'bring-fraktguiden-for-woocommerce'); ?></label>
						<select name="booking_home_delivery_package_type">
							<option value="hd_eur" selected>HD_EUR_PALLET</option>
							<option value="hd_half">HD_HALF_PALLET</option>
							<option value="hd_quarter">HD_QUARTER_PALLET</option>
							<option value="hd_loose">HD_SPECIAL_PALLET</option>
						</select>
						<p class="bfg-description"><?php esc_html_e('Only applies to home delivery services', 'bring-fraktguiden-for-woocommerce'); ?></p>
					</div>

					<?php submit_button(__('Save Changes', 'bring-fraktguiden-for-woocommerce')); ?>
				</div>
			</div>
		</form>
	</div>
</div>
