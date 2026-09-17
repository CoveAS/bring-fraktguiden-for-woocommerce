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
	<div class="bfg-step__icon bfg-step__icon--completed bfg-step__done-only">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
			<polyline points="20 6 9 17 4 12"></polyline>
		</svg>
	</div>
	<div class="bfg-step__icon bfg-step__icon--number bfg-step__todo-only"><?php echo esc_html($i + 1); ?></div>

	<div class="bfg-step__content">
		<?php echo esc_html($step->label); ?>
		<p class="bfg-step-form__line">
			<span class="bfg-step__done-only"><t>Bring gave a price for a test parcel</t></span>
			<span class="bfg-step__todo-only"><?php echo esc_html($step->description); ?></span>
			<button type="button" class="bfg-step-form__toggle" aria-controls="bfg-test-shipping-panel"
				aria-expanded="<?php echo $bfg_open ? 'true' : 'false'; ?>">
				<span class="bfg-step__done-only"><t>Test again</t></span>
				<span class="bfg-step__todo-only"><t>Test</t></span>
			</button>
		</p>
	</div>

	<span class="bfg-badge bfg-badge--completed bfg-step__done-only"><t>Done</t></span>
	<?php if ($isNext): ?>
		<span class="bfg-badge bfg-badge--in-progress bfg-step__todo-only"><t>In Progress</t></span>
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
