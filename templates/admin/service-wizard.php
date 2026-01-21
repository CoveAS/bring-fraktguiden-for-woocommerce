<?php

/**
 * @var string $country
 */
?>

<div class="wrap">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><?php use Bring_Fraktguiden\Common\Fraktguiden_Helper;

				esc_html_e('Select services', 'bring-fraktguiden-for-woocommerce'); ?></h1>
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
				<p><?php esc_html_e('This site is running in a local environment and production setting has been deactivated!', 'bring-fraktguiden-for-woocommerce'); ?></p>
			</div>
		<?php endif; ?>

		<div class="bfg-page__content">
			<h2><?php esc_html_e(
				'Shipping services guide', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			<p>
				<?php echo strtr(
					esc_html__('Please answer a few questions and get recommendations for which shipping services you should enable. Alternatively you can {{a}}skip the guide{{/a}} and choose services manually', 'bring-fraktguiden-for-woocommerce'),
					[
						'{{a}}' => sprintf('<a href="%s">', Fraktguiden_Helper::get_settings_url()),
						'{{/a}}' => '</a>'
					]
				); ?>

			</p>
			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('Who are you shipping to?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
					<p><?php esc_html_e('Choose the type of customers you ship to.', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox" name="shipping_to" value="individuals">
						<span><?php esc_html_e('Individuals', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox" name="shipping_to" value="business">
						<span><?php esc_html_e('Business', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<p><?php esc_html_e('Please make a selection to continue', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('Where do you ship to?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox" value="domestic">
						<?php if ($country): ?>
							<span><?php echo esc_html(sprintf(__('Within %s', 'bring-fraktguiden-for-woocommerce'), $country)); ?></span>
						<?php else: ?>
							<span><?php esc_html_e('Within my country', 'bring-fraktguiden-for-woocommerce'); ?></span>
						<?php endif; ?>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox" value="international">
						<span><?php esc_html_e('To other countries', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<p><?php esc_html_e('Please make a selection to continue', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('How heavy are the packages you’re sending?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('0 < 5 kg', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('5 < 35 kg', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('35 kg +', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<p><?php esc_html_e('Please make a selection to continue', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('Do you have an RFID-enabled printer and labels?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
					<p class="bfg-description">
						<?php esc_html_e('If your packages weigh less than 5 kg, you can use the "Pakke i postkassen" shipping option with RFID tracking. Compatible printer models include:', 'bring-fraktguiden-for-woocommerce'); ?>
					</p>
					<ul>
						<li>Zebra R410 (PDF)</li>
						<li>Zebra 500R (PDF)</li>
						<li>Intermec (H	oneywell) PC43d RFID</li>
					</ul>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Yes, my printer can print RFID labels', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('No, I have a regular label printer', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<p><?php esc_html_e('Please make a selection to continue', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('Based on your selection we recommend that you enable these services', 'bring-fraktguiden-for-woocommerce'); ?></strong>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Pickup parcel', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Home delivery parcel', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Mailbox parcel', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Mailbox parcel with tracking', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Business parcel', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('PickUp Parcel', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Home Delivery Parcel', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Business Pallet', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<button class="button button-primary"><?php esc_html_e('Enable selected services', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</div>
			</div>


		</div>
	</div>
</div>

