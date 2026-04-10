<?php
/**
 * Pro Page — Free State
 *
 * Rendered when Pro has never been activated (no trial, no license).
 */

use BringFraktguiden\Admin\FieldRenderer;
?>

<!-- Free Version -->
<div class="bfg-section bfg-pro-free-state">

	<!-- Card 1: Pro Features upsell -->
	<div class="bfg-free-card bfg-pro-upsell-card">
		<div class="bfg-pro-upsell-card__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/>
				<path d="M5 21h14"/>
			</svg>
		</div>
		<div class="bfg-pro-upsell-card__body">
			<h2 class="bfg-complete-card__title"><t>Pro Features</t></h2>
			<p class="bfg-pro-upsell-card__desc"><t>Try everything free for 7 days, or purchase directly if you prefer.</t></p>
			<div class="bfg-pro-upsell-card__ctas">
				<form method="post" action="options.php" id="bfg-pro-activation-form-pro">
					<?php settings_fields('bring_fraktguiden_pro'); ?>
					<div style="display:none">
						<?php FieldRenderer::pro_enabled(); ?>
					</div>
					<button type="submit" class="bfg-btn bfg-btn--primary">
						<t>Start Free Trial</t>
					</button>
				</form>
				<a href="https://bringfraktguiden.no/" target="_blank" class="bfg-btn bfg-btn--secondary">
					<t>Purchase License</t>
				</a>
			</div>
		</div>
	</div>

	<!-- Card 2: Have a license key? -->
	<div class="bfg-free-card bfg-complete-card bfg-license-activate-card">
		<div class="bfg-complete-card__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<circle cx="7.5" cy="15.5" r="5.5"/>
				<path d="m21 2-9.6 9.6"/>
				<path d="m15.5 7.5 3 3L22 7l-3-3"/>
			</svg>
		</div>
		<div class="bfg-complete-card__body">
			<h3 class="bfg-complete-card__title"><t>Have a license key?</t></h3>
			<p class="bfg-complete-card__desc"><t>Activate your existing Pro license</t></p>
			<a href="#" class="bfg-btn bfg-btn--text" id="bfg-activate-license-toggle">
				<t>Activate License</t>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="12 5 19 12 12 19"></polyline>
				</svg>
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
		const proCheckbox = document.querySelector('#bfg-pro-activation-form-pro input[name="pro_enabled"]');
		const trialForm = document.getElementById('bfg-pro-activation-form-pro');
		if (trialForm) {
			trialForm.addEventListener('submit', function () {
				if (proCheckbox) {
					proCheckbox.checked = true;
				}
			});
		}

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
