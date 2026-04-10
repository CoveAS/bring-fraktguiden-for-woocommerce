<?php
/**
 * BFG Pro License Form
 *
 * License key activation form with a section header. Used in the expired and trial
 * states of the Pro page where only the header text differs.
 *
 * @usage (expired state)
 *   <bfg-pro-license-form title="Have a new license key?" description="Enter it below to activate your renewed license."></bfg-pro-license-form>
 *
 * @usage (trial state)
 *   <bfg-pro-license-form title="Activate Your License" description="Already have a license? Enter it below to activate."></bfg-pro-license-form>
 *
 * @param string $title       Section header title
 * @param string $description Section header description
 */
?>

<div class="bfg-section">
	<div class="bfg-section__header">
		<h2><t>title</t></h2>
		<if :description>
			<p><t>description</t></p>
		</if>
	</div>

	<bfg-section.section>
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
	</bfg-section.section>
</div>
