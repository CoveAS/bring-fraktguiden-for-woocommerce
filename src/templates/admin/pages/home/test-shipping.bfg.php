<?php

/**
 * The test step of the setup page. The whole row, not only the form.
 *
 * The other step rows wrap in a link, and a form may not sit inside a link.
 * So this step builds its row from the same classes.
 *
 * @var BringFraktguiden\Admin\Step $step
 * @var int $i
 * @var bool $isNext
 */

$bfg_open = !$step->completed && $isNext;

// The setup page has no product, so the test sends a sample parcel.
$product_id = 0;
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
				<?php if ($step->completed): ?>
					<t>Bring gave a price for a test parcel</t>
				<?php else: ?>
					<?php echo esc_html($step->description); ?>
				<?php endif; ?>
			</span>
			<button type="button" class="bfg-step-form__toggle" aria-controls="bfg-test-shipping-panel"
				aria-expanded="<?php echo $bfg_open ? 'true' : 'false'; ?>">
				<?php if ($step->completed): ?>
					<t>Test again</t>
				<?php else: ?>
					<t>Test</t>
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

	<div class="bfg-step-form__panel" id="bfg-test-shipping-panel" <?php echo $bfg_open ? '' : 'hidden'; ?>>
		<p class="bfg-step-form__intro">
			<t>Send a sample parcel of one kilo to an address and see what the checkout would show.</t>
		</p>
		<?php require dirname(__FILE__, 6) . '/build/templates/admin/parts/shipping-test.php'; ?>

		<p class="bfg-step-form__help">
			<t>The same test sits on every product, and tests the weight and the size of that product.</t>
			<a href="<?php echo esc_url(admin_url('edit.php?post_type=product')); ?>"><t>Open your products</t></a>
		</p>
	</div>
</div>
