<?php

?>

<div class="wrap">
	<div class="bfg-page__header">
		<h1><?php use Bring_Fraktguiden\Common\Fraktguiden_Helper;

			esc_html_e('Select services', 'bring-fraktguiden-for-woocommerce'); ?></h1>
	</div>
	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>
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
					<strong><?php esc_html_e('Shipping to businesses or to customers?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Customer', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Business', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<p><?php esc_html_e('Please make a selection to continue', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('Domestic or international shipping?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('Domestic', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__checkbox">
					<label>
						<input type="checkbox">
						<span><?php esc_html_e('International', 'bring-fraktguiden-for-woocommerce'); ?></span>
					</label>
				</div>
				<div class="bfg-box__section">
					<p><?php esc_html_e('Please make a selection to continue', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>
			</div>

			<div class="bfg-box">
				<div class="bfg-box__header">
					<strong><?php esc_html_e('Weight of packages?', 'bring-fraktguiden-for-woocommerce'); ?></strong>
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
					<strong><?php esc_html_e('Do you have a printer with RFID support', 'bring-fraktguiden-for-woocommerce'); ?></strong>
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

