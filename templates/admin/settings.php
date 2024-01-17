<?php

use BringFraktguiden\Admin\SettingsPage;

?>

<script>
</script>

<style>
	.bfg__notice-list-hide {
		display: none;
	}

	.toplevel_page_bring_fraktguiden_settings #wpcontent {
		padding: 0;
	}

	.toplevel_page_bring_fraktguiden_settings .wrap {
		margin: 0;
	}

	.bfg-notices {
		padding: 0 16px;
	}

	.bfg-page__main {
		padding-top: 56px;
	}

	.bfg-page__content {
		padding: 10px 16px;
		max-width: 700px;
		margin: 0 auto;
	}

	.bfg-page__header {
		background: #fff;
		box-sizing: border-box;
		padding: 0 16px;
		position: fixed;
		width: calc(100% - 160px);
		top: 32px;
		z-index: 1001;
	}


	@media (max-width: 782px) {
		.bfg-page__header {
			flex-flow: row wrap;
			top: 46px;
			width: 100%;
		}
	}

	@media (max-width: 600px) {
		#wpadminbar {
			position: fixed;
		}
	}

	@media (min-width: 783px) and (max-width: 960px) {
		.bfg-page__header {
			width: calc(100% - 36px);
		}
	}

	@media (max-width: 530px) {
		.bfg-page__main {
			padding-top: 100px;
		}
	}


	.bfg-steps {
		background: #fff;
		margin-bottom: 1px;
	}
	@media (max-width: 782px) {
		.bfg-steps {
			margin: 0 -16px 1px;
		}
	}
	.bfg-step {
		display: flex;
		align-items: center;
		gap: 16px;
		padding: 16px 24px;
		border: 1px solid #ccc;
		margin-bottom: -1px;
		position: relative;
		color: #2271b1;
		font-size: 14px;
		cursor: pointer;
	}
	.bfg-step:hover {
		border-color: #64c477;
		background-color: #f6f7f7;
		z-index: 2;
	}
	.bfg-step:active {
		border-color: #64c477;
		z-index: 3;
	}
	.bfg-step__number {
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		border: 1px solid #2271b1;
		height: 30px;
		width: 30px;
	}
	.bfg-step__label {
		font-weight: bold;
	}
	.bfg-step--completed .bfg-step__label {
		text-decoration: line-through;
	}
	.bfg-step--completed .bfg-step__number {
		background: #2271b1;
	}
</style>

<?php
$steps = [
	[
		'label' => __('Add shipping method to a shipping zone', 'bring-fraktguiden-for-woocommerce'),
		'completed' => true,
	],
	[
		'label' => __('Select shipping services', 'bring-fraktguiden-for-woocommerce'),
		'completed' => false,
	],
	[
		'label' => __('Set up fallback rates', 'bring-fraktguiden-for-woocommerce'),
		'completed' => false,
	],
	[
		'label' => __('Test shipping for a product', 'bring-fraktguiden-for-woocommerce'),
		'completed' => false,
	],
];
?>
<div class="wrap">
	<div class="bfg-page__header">
		<h1><?php esc_html_e('Bring Fraktguiden for WooCommerce Settings', 'bring-fraktguiden-for-woocommerce'); ?></h1>
	</div>
	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<div class="bfg-page__content">
			<h2> <?php esc_html_e('Lorem ipsum', 'bring-fraktguiden-for-woocommerce'); ?> </h2>
			<div class="bfg-steps">
				<div>

					<?php foreach ($steps as $i => $step): ?>

						<div class="bfg-step <?php echo $step['completed'] ? 'bfg-step--completed' : '';?>">
							<div class="bfg-step__number">
								<?php if ($step['completed']): ?>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="#FFFFFF" d="M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"></path></svg>
								<?php else: ?>
									<?php echo $i + 1 ?>
								<?php endif; ?>
							</div>
							<div class="bfg-step__label"><?php echo esc_html($step['label'])?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<form method="post" action="options.php">
				<?php

				settings_fields('bring_fraktguiden_plugin_page');
				do_settings_sections('bring_fraktguiden_plugin_page');
				submit_button();
				?>
			</form>

		</div>
	</div>
</div>

