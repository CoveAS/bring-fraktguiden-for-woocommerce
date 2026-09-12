<?php

use BringFraktguiden\Admin\FallbackPrice;

/**
 * The fallback price step of the setup page. The whole row, not only the form.
 *
 * The other step rows wrap in a link, and a form may not sit inside a link.
 * So this step builds its row from the same classes.
 *
 * @var BringFraktguiden\Admin\Step $step
 * @var int $i
 * @var bool $isNext
 */

$bfg_fallback = FallbackPrice::current();
$bfg_open = !$step->completed && $isNext;
$bfg_currency = get_option('woocommerce_currency');
$bfg_advanced = admin_url('admin.php?page=bring_fraktguiden_fallback');
$bfg_service = $bfg_fallback->service ?: FallbackPrice::service();
$bfg_services = FallbackPrice::services();
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
			<span>
				<?php if ($bfg_fallback->state === FallbackPrice::PRICE): ?>
					<?php printf(
						/* translators: 1: the price, 2: the currency of the shop. */
						esc_html__('These carts cost: %1$s %2$s', 'bring-fraktguiden-for-woocommerce'),
						esc_html(number_format_i18n($bfg_fallback->price, 2)),
						esc_html($bfg_currency)
					); ?>
				<?php elseif ($bfg_fallback->state === FallbackPrice::NO_SHIPPING): ?>
					<t>These carts get no shipping</t>
				<?php elseif ($bfg_fallback->state === FallbackPrice::CUSTOM): ?>
					<t>You set your own price for each case</t>
				<?php else: ?>
					<?php echo esc_html($step->description); ?>
				<?php endif; ?>
			</span>
			<button type="button" class="bfg-step-form__toggle" aria-controls="bfg-fallback-panel"
				aria-expanded="<?php echo $bfg_open ? 'true' : 'false'; ?>">
				<?php if ($step->completed): ?>
					<t>Change</t>
				<?php else: ?>
					<t>Answer</t>
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

	<div class="bfg-step-form__panel" id="bfg-fallback-panel" <?php echo $bfg_open ? '' : 'hidden'; ?>>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr(FallbackPrice::ACTION); ?>">
			<?php wp_nonce_field(FallbackPrice::ACTION); ?>

			<p class="bfg-step-form__intro">
				<t>Some carts are too big, too heavy or too full for a Bring parcel. Some come when the Bring API is quiet. Bring sends no price for them. What should the checkout show then?</t>
			</p>

			<div class="bfg-step-form__field">
				<label class="bfg-step-form__choice" for="bfg-fallback-answer-price">
					<input type="radio" name="bfg_fallback_answer" id="bfg-fallback-answer-price"
						value="<?php echo esc_attr(FallbackPrice::PRICE); ?>"
						<?php checked($bfg_fallback->state, FallbackPrice::PRICE); ?>>
					<t>Charge a fixed price</t>
				</label>
				<div class="bfg-step-form__row">
					<div class="bfg-input bfg-input--number bfgu:w-40 bfgu:shrink-0">
						<input class="bfg-step-form__input" type="number" id="bfg-fallback-price" name="bfg_fallback_price"
							step="0.1" min="0" value="<?php echo esc_attr($bfg_fallback->price ?: ''); ?>"
							placeholder="<?php esc_attr_e('Free: 0', 'bring-fraktguiden-for-woocommerce'); ?>">
						<span class="bfg-suffix-lg"><?php echo esc_html($bfg_currency); ?></span>
					</div>
					<label class="screen-reader-text" for="bfg-fallback-service">
						<t>Service</t>
					</label>
					<select class="bfgu:flex-1 bfgu:min-w-0" id="bfg-fallback-service" name="bfg_fallback_service">
						<?php foreach ($bfg_services as $bfg_id => $bfg_name): ?>
							<option value="<?php echo esc_attr($bfg_id); ?>" <?php selected($bfg_service, (string) $bfg_id); ?>>
								<?php echo esc_html($bfg_name); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<p class="bfg-step-form__help">
					<t>The customer pays this price. The booking sends the goods with this service.</t>
				</p>
			</div>

			<div class="bfg-step-form__field">
				<label class="bfg-step-form__choice">
					<input type="radio" name="bfg_fallback_answer"
						value="<?php echo esc_attr(FallbackPrice::NO_SHIPPING); ?>"
						<?php checked($bfg_fallback->state, FallbackPrice::NO_SHIPPING); ?>>
					<t>Show no shipping and let the customer call me</t>
				</label>
				<p class="bfg-step-form__help">
					<t>The customer cannot finish the order without a shipping price.</t>
				</p>
			</div>

			<?php if ($bfg_fallback->state === FallbackPrice::CUSTOM): ?>
				<div class="bfg-step-form__field">
					<label class="bfg-step-form__choice">
						<input type="radio" name="bfg_fallback_answer"
							value="<?php echo esc_attr(FallbackPrice::CUSTOM); ?>" checked disabled>
						<t>A different price for each case</t>
					</label>
					<p class="bfg-step-form__help">
						<t>Change these prices in advanced settings. Pick one of the answers above to go back to one price.</t>
					</p>
				</div>
			<?php endif; ?>

			<button type="submit" class="bfg-btn bfg-btn--primary bfg-btn--sm"><t>Save</t></button>
			<a class="bfg-btn bfg-btn--secondary bfg-btn--sm" href="<?php echo esc_url($bfg_advanced); ?>">
				<t>Advanced settings</t>
			</a>
		</form>
	</div>
</div>
