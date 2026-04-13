<?php
/**
 * Pro Page — Expired State
 *
 * Rendered when a license was previously active but has since expired.
 */

use BringFraktguiden\Admin\FieldRenderer;
?>

<!-- License Expired -->
<div class="bfg-section bfg-pro-free-state">

	<!-- Card 1: License expired + renew CTA -->
	<div class="bfg-free-card bfg-license-expired-card">
		<div class="bfg-license-expired-card__body">
			<div class="bfg-license-expired-card__text">
				<h2 class="bfg-complete-card__title"><t>License Expired</t></h2>
				<p class="bfg-pro-upsell-card__desc"><t>Your configurations are preserved. Renew your license to reactivate all Pro features.</t></p>
			</div>
			<div class="bfg-pro-upsell-card__ctas bfgu:flex-shrink-0">
				<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--primary">
					<t>Renew License</t>
				</a>
			</div>
		</div>
	</div>

	<!-- Card 2: Have a new license key? -->
	<div class="bfg-free-card bfg-complete-card bfg-license-activate-card">
		<div class="bfg-complete-card__body">
			<h3 class="bfg-complete-card__title"><t>Have a new license key?</t></h3>
			<p class="bfg-complete-card__desc"><t>Enter it below to activate your renewed license.</t></p>
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
