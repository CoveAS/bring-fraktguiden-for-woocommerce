<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Admin\SettingsPage;
use BringFraktguiden\Admin\AddShippingMethod;
use BringFraktguiden\Admin\ConnectAccount;
use BringFraktguiden\Admin\Step;

/**
 * @var array $steps
 * @var int $stepCount
 * @var int $stepsCompleted
 * @var ?Step $nextStep
 * @var array $zones
 * @var ?int $zonesAdded
 */
?>

<?php /* Styles moved to assets/css/bring-fraktguiden-admin-home.css */ ?>

<div class="wrap bfg bfg-admin-page bfg-admin-page__home" data-setup-complete="<?php echo $stepsCompleted === $stepCount ? 'true' : 'false'; ?>">
	<div class="bfg-page__header">
		<h1><t>Setup</t></h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
			<?php if ($zonesAdded !== null): ?>
				<bfg-notice>
					<?php if ($zonesAdded > 0): ?>
						<?php printf(
							_n(
								'Bring Fraktguiden was added to %d shipping zone.',
								'Bring Fraktguiden was added to %d shipping zones.',
								$zonesAdded,
								'bring-fraktguiden-for-woocommerce'
							),
							$zonesAdded
						); ?>
					<?php else: ?>
						<t>No shipping zone was changed.</t>
					<?php endif; ?>
				</bfg-notice>
			<?php endif; ?>
			<?php if (($_GET[ConnectAccount::RESULT] ?? null) === 'yes'): ?>
				<bfg-notice type="success">
					<?php echo esc_html(ConnectAccount::message()); ?>
				</bfg-notice>
			<?php endif; ?>
		</div>

		<div class="bfg-section">
			<?php
			$showNextStep = (bool) $nextStep;
				$nextStepIndex = $showNextStep ? array_search($nextStep, $steps, true) : false;
			$currentStepNumber = $nextStepIndex !== false ? $nextStepIndex + 1 : $stepCount;
			?>

			<!-- Setup Header -->
			<div class="bfg-setup-header">
				<div class="bfgu:flex bfgu:justify-between bfgu:items-center">
					<h2 class="bfg-setup-header__title">
						<t>Get Started with Bring shipping</t>
					</h2>
					<a href="<?php echo esc_url(admin_url('admin.php?page=bring_fraktguiden_pro')); ?>" class="bfg-badge bfg-badge--outline bfg-badge--pro">
						<span class="bfg-badge__dot bfg-badge__dot--green"></span>
						<t>Pro available</t>
					</a>
				</div>
				<p class="bfg-setup-header__subtitle">
					<t>Complete these steps to configure Bring Shipping</t>
				</p>

				<div class="bfg-setup-header__stats">
					<div class="bfg-setup-header__stat">
						<span class="bfg-setup-header__stat-label">
							<t>Current Step</t>
						</span>
						<span class="bfg-setup-header__stat-value">
							<?php printf(__('Step %d of %d', 'bring-fraktguiden-for-woocommerce'), $currentStepNumber, $stepCount); ?>
						</span>
					</div>
					<div class="bfg-setup-header__stat bfg-setup-header__stat--right">
						<span class="bfg-setup-header__stat-label">
							<t>Progress</t>
						</span>
						<span class="bfg-setup-header__stat-value">
							<?php printf(__('%d of %d completed', 'bring-fraktguiden-for-woocommerce'), $stepsCompleted, $stepCount); ?>
						</span>
					</div>
				</div>

				<div class="bfg-progress-container">
					<div class="bfg-progress-bar-new">
						<div class="bfg-progress-bar-fill"
							style="width: <?php echo $stepCount ? ($stepsCompleted / $stepCount) * 100 : 0; ?>%;"></div>
					</div>
				</div>
			</div>

			<div class="bfg-steps-list">
				<?php foreach ($steps as $i => $step): ?>
					<?php
					$isNext = $showNextStep && $nextStep === $step;
					?>
					<?php if ($step->form): ?>
						<?php require dirname(__FILE__, 5) . '/build/templates/admin/pages/home/' . $step->form . '.php'; ?>
					<?php elseif ($step->completed): ?>
						<bfg-step.completed :href="$step->action">
							<?php echo esc_html($step->label); ?>
							<bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
							<bfg-badge.completed>
								<t>Done</t>
							</bfg-badge.completed>
						</bfg-step.completed>
					<?php elseif ($isNext): ?>
						<bfg-step.in-progress :number="$i + 1">
							<?php echo esc_html($step->label); ?>
							<bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
							<?php if ($step->dialog): ?>
								<button type="button" class="bfg-btn bfg-btn--primary bfg-btn--sm" data-bfg-dialog="<?php echo esc_attr($step->dialog); ?>">
									<?php echo esc_html($step->actionText); ?>
								</button>
							<?php else: ?>
								<a class="bfg-btn bfg-btn--primary bfg-btn--sm" href="<?php echo esc_attr($step->action); ?>">
									<?php echo esc_html($step->actionText); ?>
								</a>
							<?php endif; ?>
							<bfg-badge.in-progress>
								<t>In Progress</t>
							</bfg-badge.in-progress>
						</bfg-step.in-progress>
					<?php else: ?>
						<bfg-step.pending :href="$step->action" :number="$i + 1">
							<?php echo esc_html($step->label); ?>
							<bfg-step-desc><?php echo esc_html($step->description); ?></bfg-step-desc>
						</bfg-step.pending>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<dialog class="bfg bfg-modal" id="bfg-add-shipping-method">
		<div class="bfg-modal__head">
			<h2 class="bfg-modal__title"><t>Add Bring to shipping zones</t></h2>
			<button type="button" class="bfg-modal__close" data-bfg-dialog-close aria-label="<?php esc_attr_e('Close', 'bring-fraktguiden-for-woocommerce'); ?>">&times;</button>
		</div>
		<?php if (!$zones): ?>
			<div class="bfg-modal__body">
				<p><t>This shop has no shipping zone yet.</t></p>
			</div>
			<div class="bfg-modal__foot">
				<a class="bfg-btn bfg-btn--primary bfg-btn--sm" href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')); ?>">
					<t>Make a shipping zone</t>
				</a>
			</div>
		<?php else: ?>
			<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
				<input type="hidden" name="action" value="<?php echo esc_attr(AddShippingMethod::ACTION); ?>">
				<?php wp_nonce_field(AddShippingMethod::ACTION); ?>
				<div class="bfg-modal__body">
					<p><t>A shipping zone is a group of places with its own shipping prices.</t></p>
					<p><t>Tick a zone to show Bring shipping options to customers there.</t></p>
					<?php foreach ($zones as $zone): ?>
						<label class="bfg-zone-row">
							<input type="checkbox" name="zones[]" value="<?php echo esc_attr($zone['id']); ?>"
								checked <?php echo $zone['added'] ? 'disabled' : ''; ?>>
							<span class="bfg-zone-row__regions"><?php echo esc_html($zone['regions']); ?></span>
							<?php if ($zone['name'] !== $zone['regions']): ?>
								<span class="bfg-zone-row__name"><?php echo esc_html($zone['name']); ?></span>
							<?php endif; ?>
							<?php if ($zone['added']): ?>
								<span class="bfg-zone-row__note"><t>Already added</t></span>
							<?php endif; ?>
						</label>
					<?php endforeach; ?>
				</div>
				<div class="bfg-modal__foot">
					<button type="submit" class="bfg-btn bfg-btn--primary bfg-btn--sm"><t>Add Bring</t></button>
				</div>
			</form>
		<?php endif; ?>
	</dialog>
</div>
