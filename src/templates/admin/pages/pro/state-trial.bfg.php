<?php
/**
 * Pro Page — Trial State
 *
 * Rendered when Pro is enabled in trial mode (activated but no paid license).
 *
 * @var int $days_remaining
 */

use BringFraktguiden\Admin\FieldRenderer;
?>

<!-- Trial Active -->
<div class="bfg-section bfg-pro-free-state">

	<!-- Banner: trial countdown + purchase CTA -->
	<div class="bfg-free-card bfg-trial-notice">
		<div class="bfg-trial-notice__body">
			<strong class="bfg-trial-notice__title">
				<?php printf(
					esc_html__('%d days left in your trial', 'bring-fraktguiden-for-woocommerce'),
					max(0, $days_remaining)
				); ?>
			</strong>
			<p class="bfg-trial-notice__desc"><t>Configure features below to try them on your live site</t></p>
		</div>
		<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--primary">
			<t>Purchase License</t>
		</a>
	</div>

	<!-- License card: activate existing license -->
	<div class="bfg-free-card bfg-complete-card bfg-license-activate-card">
		<div class="bfg-complete-card__body">
			<h3 class="bfg-complete-card__title"><t>License</t></h3>
			<p class="bfg-complete-card__desc"><t>Currently on trial. Already have a license?</t></p>
			<a href="#" class="bfg-btn bfg-btn--text" id="bfg-activate-license-toggle">
				<t>Activate License</t>
			</a>

			<!-- License Form (inline, revealed on click) -->
			<div id="bfg-license-form-section" style="display:none; margin-top: 1.25rem;">
				<form method="post" action="options.php" id="bfg-license-form-pro">
					<?php settings_fields('bring_fraktguiden_pro'); ?>
					<div style="display:none">
						<?php FieldRenderer::pro_enabled(); ?>
					</div>
					<div class="bfg-field">
						<label class="bfg-field__label">
							<t>Enter Your License Key</t>
						</label>
						<div class="bfg-pro-license-form__row" style="display:flex; gap: 0.5rem; align-items: center; width: 100%;">
							<div style="flex: 1; min-width: 0;"><?php FieldRenderer::test_url(); ?></div>
							<button type="submit" class="bfg-btn bfg-btn--primary" style="flex-shrink:0;">
								<t>Activate</t>
							</button>
						</div>
						<p class="bfg-description">
							<t>Your license key is a 16-character code you received after purchase</t>
						</p>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const toggle = document.getElementById('bfg-activate-license-toggle');
		const licenseSection = document.getElementById('bfg-license-form-section');
		if (toggle && licenseSection) {
			toggle.addEventListener('click', function (e) {
				e.preventDefault();
				toggle.style.display = 'none';
				licenseSection.style.display = '';
				licenseSection.querySelector('input').focus();
			});
		}
	});
</script>
