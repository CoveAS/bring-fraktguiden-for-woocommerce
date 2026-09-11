<?php

/**
 * @var string $country
 * @var string $country_code
 * @var string $settings_url
 * @var array  $services
 * @var array  $active_services
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

			<?php if (!$services): ?>
				<bfg-notice type="warning">
					<?php echo esc_html(sprintf(
						/* translators: %s: the country the shop sends from. */
						__('Bring sells no shipping service from %s, so the guide has nothing to recommend. Check the From country setting, or choose services manually.', 'bring-fraktguiden-for-woocommerce'),
						$country ?: $country_code
					)); ?>
				</bfg-notice>
				<p>
					<a href="<?php echo esc_url($settings_url); ?>" class="bfg-btn bfg-btn--primary">
						<t>Go to the settings</t>
					</a>
				</p>
			<?php else: ?>
			<form method="post" class="bfg-wizard">
				<?php wp_nonce_field(\BringFraktguiden\Admin\ServiceWizard::NONCE); ?>

				<bfg-section class="bfg-wizard__step" data-step="recipient">
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
							<input type="checkbox" name="recipient[]" value="private">
							<span>
								<t>Individuals</t>
							</span>
						</label>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="recipient[]" value="business">
							<span>
								<t>Business</t>
							</span>
						</label>
					</div>
					<bfg-section.section>
						<p class="bfg-wizard__hint">
							<t>Please make a selection to continue</t>
						</p>
					</bfg-section.section>
				</bfg-section>

				<bfg-section class="bfg-wizard__step" data-step="destination" hidden>
					<div class="bfg-section__header">
						<h2>
							<t>Where do you ship to?</t>
						</h2>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="destination[]" value="domestic">
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
							<input type="checkbox" name="destination[]" value="international">
							<span>
								<t>To other countries</t>
							</span>
						</label>
					</div>
					<bfg-section.section>
						<p class="bfg-wizard__hint">
							<t>Please make a selection to continue</t>
						</p>
					</bfg-section.section>
				</bfg-section>

				<bfg-section class="bfg-wizard__step" data-step="weight" hidden>
					<div class="bfg-section__header">
						<h2>
							<t>How heavy are the packages you're sending?</t>
						</h2>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="weight[]" value="0-5">
							<span>
								<t>Under 5 kg</t>
							</span>
						</label>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="weight[]" value="5-35">
							<span>
								<t>5 to 35 kg</t>
							</span>
						</label>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="weight[]" value="35-">
							<span>
								<t>Over 35 kg</t>
							</span>
						</label>
					</div>
					<bfg-section.section>
						<p class="bfg-wizard__hint">
							<t>Please make a selection to continue</t>
						</p>
					</bfg-section.section>
				</bfg-section>

				<bfg-section class="bfg-wizard__step" data-step="rfid" hidden>
					<div class="bfg-section__header">
						<h2>
							<t>Do you have an RFID-enabled printer and labels?</t>
						</h2>
						<p class="bfg-description">
							<t>A package under 5 kg can travel as Pakke i postkassen with RFID tracking. These printer models print an RFID label:</t>
						</p>
						<ul>
							<li>Zebra R410 (PDF)</li>
							<li>Zebra 500R (PDF)</li>
							<li>Intermec (Honeywell) PC43d RFID</li>
						</ul>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="rfid[]" value="yes">
							<span>
								<t>Yes, my printer can print RFID labels</t>
							</span>
						</label>
					</div>
					<div class="bfg-section__checkbox">
						<label>
							<input type="checkbox" name="rfid[]" value="no">
							<span>
								<t>No, I have a regular label printer</t>
							</span>
						</label>
					</div>
					<bfg-section.section>
						<p class="bfg-wizard__hint">
							<t>Please make a selection to continue</t>
						</p>
					</bfg-section.section>
				</bfg-section>

				<bfg-section class="bfg-wizard__step bfg-wizard__result" data-step="result" hidden>
					<div class="bfg-section__header">
						<h2>
							<t>Based on your selection we recommend that you enable these services</t>
						</h2>
					</div>

					<?php foreach ($services as $service): ?>
						<div class="bfg-section__checkbox bfg-wizard__service" data-code="<?php echo esc_attr($service['code']); ?>" hidden>
							<label>
								<input type="checkbox" name="bfg_services[]" value="<?php echo esc_attr($service['code']); ?>">
								<span><?php echo esc_html($service['name']); ?></span>
							</label>
						</div>
					<?php endforeach; ?>

					<div class="bfg-wizard__empty" hidden>
						<bfg-notice type="warning">
							<t>Bring has no service that matches your answers. Change an answer, or choose services manually on the settings page.</t>
						</bfg-notice>
					</div>

					<?php if ($active_services): ?>
						<bfg-notice type="warning">
							<?php echo esc_html(sprintf(
								/* translators: %d: number of services that are active now. */
								_n(
									'This shop offers %d Bring service today. The guide replaces it.',
									'This shop offers %d Bring services today. The guide replaces them.',
									count($active_services),
									'bring-fraktguiden-for-woocommerce'
								),
								count($active_services)
							)); ?>
						</bfg-notice>
					<?php endif; ?>

					<bfg-section.section>
						<button
							type="submit"
							class="bfg-btn bfg-btn--primary"
							<?php if ($active_services): ?>data-confirm="<?php echo esc_attr__('This replaces the Bring services the shop offers today. Continue?', 'bring-fraktguiden-for-woocommerce'); ?>"<?php endif; ?>>
							<t>Enable selected services</t>
						</button>
					</bfg-section.section>
				</bfg-section>
			</form>
			<?php endif; ?>
		</div>
	</div>
</div>
