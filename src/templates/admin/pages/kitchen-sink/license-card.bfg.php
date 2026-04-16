<?php /* Kitchen Sink: License Card */ ?>

<!-- License Card -->
<bfg-section>
	<bfg-section.header title="License Card" description="Displays license details — title, status badge, key, and manage link"></bfg-section.header>
	<bfg-section.section>

		<h3 class="bfg-field-group-title bfgu:pb-1">
			<bfg-t>Active</bfg-t>
		</h3>
		<bfg-license-card
			title="Pro License"
			subtitle="Active until April 21, 2026"
			status="Active"
			status-color="green"
			license-key="XXXX–XXXX–AB3F"
			days="11 days remaining"
			manage-url="#"
			manage-label="Manage License">
		</bfg-license-card>

		<h3 class="bfg-field-group-title bfgu:pt-10 bfgu:pb-1">
			<bfg-t>Expiring soon</bfg-t>
		</h3>
		<bfg-license-card
			title="Pro License"
			subtitle="Expires April 21, 2026"
			status="Expiring soon"
			status-color="red"
			license-key="XXXX–XXXX–AB3F"
			days="8 days remaining"
			manage-url="#"
			manage-label="Renew License">
		</bfg-license-card>

		<h3 class="bfg-field-group-title bfgu:pt-10 bfgu:pb-1">
			<bfg-t>Expired</bfg-t>
		</h3>
		<bfg-license-card
			title="Pro License"
			subtitle="Expired March 1, 2026"
			status="Expired"
			status-color="gray"
			license-key="XXXX–XXXX–AB3F"
			days="Renew to restore access"
			manage-url="#"
			manage-label="Renew License">
		</bfg-license-card>

	</bfg-section.section>
</bfg-section>
