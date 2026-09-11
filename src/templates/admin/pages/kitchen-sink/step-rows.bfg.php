<?php /* Kitchen Sink: Step Rows */ ?>

<!-- Step Row -->
<bfg-section>
	<bfg-section.header title="Step Rows"></bfg-section.header>
	<bfg-section.section>
		<div class="bfg-steps-list">
			<bfg-step.completed href="#">
				<bfg-t>Add shipping method</bfg-t>
				<bfg-step-desc><bfg-t>Add the Bring method to your shipping zone</bfg-t></bfg-step-desc>
				<bfg-badge.completed><t>Done</t></bfg-badge.completed>
			</bfg-step.completed>

			<bfg-step.completed href="#">
				<bfg-t>Select Shipping services</bfg-t>
				<bfg-step-desc><bfg-t>Choose your Bring services to offer</bfg-t></bfg-step-desc>
				<bfg-badge.completed><t>Done</t></bfg-badge.completed>
			</bfg-step.completed>

			<bfg-step.in-progress number="3">
				<bfg-t>Connect your Bring account</bfg-t>
				<bfg-step-desc><bfg-t>Add your Bring login email and API key</bfg-t></bfg-step-desc>
				<button class="bfg-btn bfg-btn--primary bfg-btn--sm"><bfg-t>Connect account</bfg-t></button>
				<bfg-badge.in-progress><t>In progress</t></bfg-badge.in-progress>
			</bfg-step.in-progress>

			<bfg-step.pending href="#" number="4">
				<bfg-t>Set fallback rates</bfg-t>
				<bfg-step-desc><bfg-t>Configure backup shipping rates</bfg-t></bfg-step-desc>
			</bfg-step.pending>

			<bfg-step.pending href="#" number="5">
				<bfg-t>Test shipping</bfg-t>
				<bfg-step-desc><bfg-t>Test with a sample product</bfg-t></bfg-step-desc>
			</bfg-step.pending>
		</div>
	</bfg-section.section>
</bfg-section>
