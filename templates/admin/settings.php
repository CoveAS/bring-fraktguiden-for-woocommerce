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
		<h1><?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?></h1>
	</div>
	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-page__content">
			<h2> <?php esc_html_e('Settings', 'bring-fraktguiden-for-woocommerce'); ?> </h2>
			<p>
			</p>

			<div class="bfg-box">
				<form method="post" action="options.php">
					<?php settings_fields('bring_fraktguiden_plugin_page'); ?>
					<div class="bfg-box__header">
						<h2><?php esc_html_e('Heading', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					</div>


					<div class="bfg-box__section">
						<p><?php esc_html_e('Settings go here?', 'bring-fraktguiden-for-woocommerce'); ?></p>
						<?php submit_button(); ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

