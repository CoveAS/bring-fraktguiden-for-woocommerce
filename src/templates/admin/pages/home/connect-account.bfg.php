<?php

use BringFraktguiden\Admin\ConnectAccount;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * The connect step of the setup page. The whole row, not only the form.
 *
 * The other step rows wrap in a link, and a form may not sit inside a link.
 * So this step builds its row from the same classes.
 *
 * @var BringFraktguiden\Admin\Step $step
 * @var int $i
 * @var bool $isNext
 */

$bfg_failed = ($_GET[ConnectAccount::RESULT] ?? null) === 'no';
$bfg_open = $bfg_failed || (!$step->completed && $isNext);
$bfg_uid = (string) Fraktguiden_Helper::get_option('mybring_api_uid');
$bfg_quickship_subject = rawurlencode(__('Bring Fraktguiden for WooCommerce: I would like a shipping agreement', 'bring-fraktguiden-for-woocommerce'));
$bfg_quickship_body = rawurlencode(sprintf(
	/* translators: %s is the address of the shop. */
	__("Hi, I have a WooCommerce shop, %s, and I would like an account.\nCan you help me?", 'bring-fraktguiden-for-woocommerce'),
	home_url()
));
?>

<div class="bfg-step bfg-step--form <?php echo $step->completed ? 'bfg-step--completed' : ($isNext ? 'bfg-step--in-progress' : 'bfg-step--pending'); ?>">
	<?php if ($step->completed): ?>
		<div class="bfg-step__icon bfg-step__icon--completed">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
				<polyline points="20 6 9 17 4 12"></polyline>
			</svg>
		</div>
	<?php else: ?>
		<div class="bfg-step__icon bfg-step__icon--number"><?php echo esc_html($i + 1); ?></div>
	<?php endif; ?>

	<div class="bfg-step__content">
		<?php echo esc_html($step->label); ?>
		<p class="bfg-step-form__line">
			<span class="bfg-connect__state">
				<?php if ($step->completed): ?>
					<?php printf(
						esc_html__('Connected as %s', 'bring-fraktguiden-for-woocommerce'),
						esc_html($bfg_uid)
					); ?>
				<?php else: ?>
					<?php echo esc_html($step->description); ?>
				<?php endif; ?>
			</span>
			<button type="button" class="bfg-step-form__toggle" aria-controls="bfg-connect-panel"
				aria-expanded="<?php echo $bfg_open ? 'true' : 'false'; ?>">
				<?php if ($step->completed): ?>
					<t>Change</t>
				<?php else: ?>
					<t>Connect</t>
				<?php endif; ?>
			</button>
		</p>
	</div>

	<?php if ($step->completed): ?>
		<bfg-badge.completed>
			<t>Done</t>
		</bfg-badge.completed>
	<?php elseif ($isNext): ?>
		<bfg-badge.in-progress>
			<t>In Progress</t>
		</bfg-badge.in-progress>
	<?php endif; ?>

	<div class="bfg-step-form__panel" id="bfg-connect-panel" <?php echo $bfg_open ? '' : 'hidden'; ?>>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr(ConnectAccount::ACTION); ?>">
			<?php wp_nonce_field(ConnectAccount::ACTION); ?>

			<p class="bfg-step-form__intro">
				<t>Bring needs two things: the email you log in with, and an API key.</t>
			</p>

			<div class="bfg-connect__signup">
				<p class="bfg-step-form__intro">
					<t>Save as much as 30 to 40 percent against a normal Bring account.</t>
					<t>Our partner Quickship opens an account for you, or takes over the one you have.</t>
				</p>
				<p class="bfg-connect__signup-links">
					<a class="bfg-btn bfg-btn--secondary bfg-btn--sm" href="mailto:support@quickship.no?subject=<?php echo esc_attr($bfg_quickship_subject); ?>&amp;body=<?php echo esc_attr($bfg_quickship_body); ?>">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<rect x="2" y="4" width="20" height="16" rx="2"></rect>
							<polyline points="2 6 12 13 22 6"></polyline>
						</svg>
						<t>Set up an account</t>
					</a>
					<a class="bfg-connect__signup-link" href="tel:+4740001714">+47 40 00 17 14</a>
					<a class="bfg-connect__signup-link" href="https://quickship.no/fraktavtale/" target="_blank" rel="noopener">
						<t>Read more</t>
						<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
							<polyline points="15 3 21 3 21 9"></polyline>
							<line x1="10" y1="14" x2="21" y2="3"></line>
						</svg>
					</a>
				</p>
			</div>

			<div class="bfg-step-form__field">
				<label class="bfg-step-form__label" for="bfg-api-uid"><t>Bring login email</t></label>
				<input class="bfg-step-form__input" type="email" id="bfg-api-uid" name="mybring_api_uid" required
					value="<?php echo esc_attr($bfg_uid); ?>">
				<p class="bfg-step-form__help">
					<t>Use the email you log in to Bring with, not your shop address.</t>
					<a href="https://www.mybring.com/useradmin/account/profile" target="_blank" rel="noopener">
						<t>Find it on your Bring profile</t>
					</a>
				</p>
			</div>

			<div class="bfg-step-form__field">
				<label class="bfg-step-form__label" for="bfg-api-key"><t>API key</t></label>
				<input class="bfg-step-form__input" type="text" id="bfg-api-key" name="mybring_api_key" required
					value="<?php echo esc_attr(Fraktguiden_Helper::get_option('mybring_api_key')); ?>">
				<p class="bfg-step-form__help">
					<a href="https://www.mybring.com/useradmin/account/settings/api" target="_blank" rel="noopener">
						<t>Open your Bring API settings</t>
					</a>
					<t>Copy the key from that page and paste it here.</t>
				</p>
			</div>

			<div class="bfg-connect__result" role="status">
				<?php if ($bfg_failed): ?>
					<p class="bfg-connect__error"><?php echo esc_html(ConnectAccount::message()); ?></p>
					<ul class="bfg-connect__checks">
						<li><t>Check that the whole API key is copied, with no space at either end.</t></li>
						<li><t>Check that the email is the one you log in to Bring with.</t></li>
					</ul>
				<?php endif; ?>
			</div>

			<button type="submit" class="bfg-btn bfg-btn--primary bfg-btn--sm"><t>Save and test</t></button>
		</form>
	</div>
</div>
