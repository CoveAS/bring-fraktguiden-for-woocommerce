<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Admin\SettingsPage;
use BringFraktguiden\Admin\Step;

/**
 * @var array $steps
 * @var int $stepCount
 * @var int $stepsCompleted
 * @var ?Step $nextStep
 */
?>

<?php /* Styles moved to assets/css/bring-fraktguiden-admin-home.css */ ?>

<div class="wrap bfg-admin-page bfg-admin-page__home">
	<div class="bfg-page__header">
		<h1><t>Setup</t></h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-section">
			<?php
			// Only show "next step" highlighting if at least one step is completed
			// For fresh state (nothing completed), don't highlight any step as "in progress"
			$showNextStep = $stepsCompleted > 0 && $nextStep;
			$nextStepIndex = $showNextStep ? array_search($nextStep, $steps, true) : false;
			$currentStepNumber = $nextStepIndex !== false ? $nextStepIndex + 1 : 1;
			?>

			<!-- Setup Header -->
			<div class="bfg-setup-header">
				<h2 class="bfg-setup-header__title">
					<t>Get Started with Bring shipping</t>
				</h2>
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
							style="width: <?php echo ($stepsCompleted / $stepCount) * 100; ?>%;"></div>
					</div>
				</div>
			</div>

			<div class="bfg-steps-list">
				<?php foreach ($steps as $i => $step): ?>
					<?php
					// Only mark as "in progress" if we're showing the next step (i.e., at least one step completed)
					$isNext = $showNextStep && $nextStep === $step;
					?>
					<?php if ($step->completed): ?>
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
							<a class="bfg-btn bfg-btn--primary bfg-btn--sm" href="<?php echo esc_attr($step->action); ?>">
								<?php echo esc_html($step->actionText); ?>
							</a>
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
</div>