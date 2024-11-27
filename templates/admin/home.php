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

<script>
</script>

<?php
?>
<div class="wrap">
	<div class="bfg-page__header">
		<h1><?php esc_html_e('Home', 'bring-fraktguiden-for-woocommerce'); ?></h1>
	</div>
	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-page__content">
			<h2> <?php esc_html_e('Get started with Bring shipping', 'bring-fraktguiden-for-woocommerce'); ?> </h2>
			<p>

				<?php
				echo esc_html(
					sprintf(
						__('%d out of %d complete.', 'bring-fraktguiden-for-woocommerce'),
						$stepsCompleted,
						$stepCount,
					)
				);
				?>
			</p>
			<progress class="bfg-progress-bar" max="<?php echo $stepCount; ?>"
					  value="<?php echo $stepsCompleted; ?>"></progress>
			<div class="bfg-steps">
				<div class="bfg-steps__header">
					<h3><?php echo esc_html($nextStep->label); ?></h3>
					<p><?php echo esc_html($nextStep->description); ?></p>
					<a class="button button-primary"
					   href="<?php echo esc_attr($nextStep->action); ?>"><?php echo esc_html($nextStep->actionText); ?></a>
				</div>
				<?php foreach ($steps as $i => $step): ?>
					<a href="<?php echo esc_attr($step->action); ?>"
					   class="bfg-step <?php echo $step->completed ? 'bfg-step--completed' : ''; ?>">
						<div class="bfg-step__number">
							<?php if ($step->completed): ?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
									 aria-hidden="true" focusable="false">
									<path fill="#FFFFFF"
										  d="M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"></path>
								</svg>
							<?php else: ?>
								<?php echo $i + 1 ?>
							<?php endif; ?>
						</div>
						<div class="bfg-step__label"><?php echo esc_html($step->label) ?></div>
					</a>
				<?php endforeach; ?>
			</div>

			<div class="bfg-box">
				<form method="post" action="options.php">
					<?php settings_fields('bring_fraktguiden_home'); ?>
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Do more with our PRO features', 'bring-fraktguiden-for-woocommerce'); ?></h2>
						<ul class="bfg-mb-5">
							<li><?php esc_html_e('Book orders directly with mybring.com from within WooCommerce', 'bring-fraktguiden-for-woocommerce'); ?>
								<sup>1</sup></li>
							<li><?php esc_html_e('Set a free shipping threshold per service', 'bring-fraktguiden-for-woocommerce'); ?></li>
							<li><?php esc_html_e('Option to set a fixed price per service', 'bring-fraktguiden-for-woocommerce'); ?></li>
							<li><?php esc_html_e('Customize the name of the shipping rates', 'bring-fraktguiden-for-woocommerce'); ?></li>
							<li><?php esc_html_e('Enable pick-up-points for supported services', 'bring-fraktguiden-for-woocommerce'); ?>
								<sup>2</sup></li>
							<li><?php esc_html_e('Get prioritised support', 'bring-fraktguiden-for-woocommerce'); ?></li>
						</ul>
					</div>

					<div class="bfg-box__checkbox"><?php FieldRenderer::pro_enabled(); ?></div>
					<div class="bfg-box__checkbox"><?php FieldRenderer::test_mode(); ?></div>
					<div class="bfg-box__section">
						<p><?php esc_html_e('Using our PRO features requires a license. For test and development sites you can activate a development mode where you can test the PRO features without requiring a license. When you first activate the PRO you will get a free 7 day trial before you are required to purchase a license.', 'bring-fraktguiden-for-woocommerce'); ?></p>
						<?php submit_button(); ?>
					</div>
				</form>
			</div>

			<small><sup>1</sup> <?php esc_html_e('Domestic shipments only. We\'re working on building support for international shipping.', 'bring-fraktguiden-for-woocommerce'); ?>
			</small>
			<small><sup>2</sup> <?php esc_html_e('List of currently supported services for using pickup point: Pickup parcel (5800), Pakke til Pakkeboks (5801), Express next day (4850), Business parcel (5000), Norgespakke (3067), PICKUP_PARCEL and PICKUP_PARCEL_BULK', 'bring-fraktguiden-for-woocommerce'); ?>
			</small>
		</div>
	</div>
</div>

