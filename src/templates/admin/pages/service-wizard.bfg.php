<?php

/**
 * @var string $country
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__service-wizard">
	<div class="bfg-page__header">
		<h1>
			<t>Select services</t>
		</h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php if (defined('BRING_ENVIRONMENT') && BRING_ENVIRONMENT === 'local'): ?>
			<bfg-notice type="warning">
				<t>This site is running in a local environment and production setting has been deactivated!</t>
			</bfg-notice>
		<?php endif; ?>

		<div class="bfg-page__content">
			<h2>
				<t>Shipping services guide</t>
			</h2>
			<p>
				<?php echo strtr(
					esc_html__('Please answer a few questions and get recommendations for which shipping services you should enable. Alternatively you can {{a}}skip the guide{{/a}} and choose services manually', 'bring-fraktguiden-for-woocommerce'),
					[
						'{{a}}' => sprintf('<a href="%s">', $settings_url),
						'{{/a}}' => '</a>'
					]
				); ?>

			</p>
			<bfg-section>
				<div class="bfg-section__header">
					<h2>
						<t>Who are you shipping to?</t>
					</h2>
					<p>
						<t>Choose the type of customers you ship to.</t>
					</p>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox" name="shipping_to" value="individuals">
						<span>
							<t>Individuals</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox" name="shipping_to" value="business">
						<span>
							<t>Business</t>
						</span>
					</label>
				</div>
				<bfg-section.section>
					<p>
						<t>Please make a selection to continue</t>
					</p>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<div class="bfg-section__header">
					<h2>
						<t>Where do you ship to?</t>
					</h2>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox" value="domestic">
						<?php if ($country): ?>
							<span><?php echo esc_html(sprintf(__('Within %s', 'bring-fraktguiden-for-woocommerce'), $country)); ?></span>
						<?php else: ?>
							<span>
								<t>Within my country</t>
							</span>
						<?php endif; ?>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox" value="international">
						<span>
							<t>To other countries</t>
						</span>
					</label>
				</div>
				<bfg-section.section>
					<p>
						<t>Please make a selection to continue</t>
					</p>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<div class="bfg-section__header">
					<h2>
						<t>How heavy are the packages you're sending?</t>
					</h2>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>0 < 5 kg</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>5 < 35 kg</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>35 kg +</t>
						</span>
					</label>
				</div>
				<bfg-section.section>
					<p>
						<t>Please make a selection to continue</t>
					</p>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<div class="bfg-section__header">
					<h2>
						<t>Do you have an RFID-enabled printer and labels?</t>
					</h2>
					<p class="bfg-description">
						<t>If your packages weigh less than 5 kg, you can use the "Pakke i postkassen" shipping option
							with
							RFID tracking. Compatible printer models include:</t>
					</p>
					<ul>
						<li>Zebra R410 (PDF)</li>
						<li>Zebra 500R (PDF)</li>
						<li>Intermec (H oneywell) PC43d RFID</li>
					</ul>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Yes, my printer can print RFID labels</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>No, I have a regular label printer</t>
						</span>
					</label>
				</div>
				<bfg-section.section>
					<p>
						<t>Please make a selection to continue</t>
					</p>
				</bfg-section.section>
			</bfg-section>

			<bfg-section>
				<div class="bfg-section__header">
					<h2>
						<t>Based on your selection we recommend that you enable these services</t>
					</h2>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Pickup parcel</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Home delivery parcel</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Mailbox parcel</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Mailbox parcel with tracking</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Business parcel</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>PickUp Parcel</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Home Delivery Parcel</t>
						</span>
					</label>
				</div>
				<div class="bfg-section__checkbox">
					<label>
						<input type="checkbox">
						<span>
							<t>Business Pallet</t>
						</span>
					</label>
				</div>
				<bfg-section.section>
					<button class="bfg-btn bfg-btn--primary">
						<t>Enable selected services</t>
					</button>
				</bfg-section.section>
			</bfg-section>
		</div>
	</div>
</div>