<?php

use BringFraktguiden\Customs\ConsentRoute;

/**
 * The callout that asks the shop to sign the customs declaration.
 *
 * It shows in the booking box and in the bulk booking modal, wherever an
 * export order or an NVIT order waits for the signature. The booking button
 * stays off until the shop signs. See resources/js/customs-consent.js.
 *
 * Only a shop owner may sign, so a shop worker reads where to get it done.
 */

$bfg_may_sign = current_user_can('manage_options');
?>

<div
	class="bfg-notice-banner bfg-booking-notice bfg-booking-notice--error bfg-consent"
	data-bfg-consent
	data-url="<?php echo esc_url(rest_url(ConsentRoute::ROUTE_NAMESPACE . ConsentRoute::ROUTE)); ?>"
	data-nonce="<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>"
	data-error="<?php esc_attr_e('The signature did not save. Nothing was booked. Try again.', 'bring-fraktguiden-for-woocommerce'); ?>"
>
	<?php require __DIR__ . '/notice-icon.php'; ?>
	<div class="bfg-consent__body">
		<p class="bfg-consent__title"><strong><t>Sign the customs declaration before you book</t></strong></p>
		<p><t>You confirm that the goods description, the value and the HS code of every order line are correct and complete, and that the goods are neither dangerous nor prohibited.</t></p>
		<p><t>Bring uses this confirmation as your signature on the customs declaration. A wrong declaration is your responsibility.</t></p>
		<p class="bfg-consent__scope"><t>One signature covers every Bring booking of this shop, now and later. You withdraw it in the booking settings.</t></p>

		<?php if ($bfg_may_sign) : ?>
			<p class="bfg-consent__error" data-bfg-consent-error role="alert" hidden></p>
			<p>
				<button type="button" class="bfg-btn bfg-btn--danger" data-bfg-consent-sign>
					<t>I confirm and sign</t>
				</button>
			</p>
		<?php else : ?>
			<p class="bfg-consent__scope"><t>Ask the shop owner to sign it in the Bring booking settings.</t></p>
		<?php endif; ?>
	</div>
</div>
