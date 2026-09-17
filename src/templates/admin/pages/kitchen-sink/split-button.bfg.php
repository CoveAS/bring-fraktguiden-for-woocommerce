<?php /* Kitchen Sink: Split Button */ ?>

<!-- Split Button -->
<bfg-section>
	<bfg-section.header title="Split Button"
		description="A main action with a caret that opens more actions"></bfg-section.header>
	<bfg-section.section>
		<div class="bfgu:flex bfgu:gap-4">
			<bfg-split-button href="#" label="Print the labels">
				<a href="#"><bfg-t>Print the shipping label only</bfg-t></a>
				<a href="#"><bfg-t>Print the return label only</bfg-t></a>
			</bfg-split-button>

			<bfg-split-button class="bfg-split-button--primary" href="#" label="Print the labels">
				<a href="#"><bfg-t>Print the shipping label only</bfg-t></a>
				<a href="#"><bfg-t>Print the return label only</bfg-t></a>
			</bfg-split-button>
		</div>
		<p class="bfg-description bfgu:mt-4"><strong>Usage:</strong>
			<code>&lt;bfg-split-button href="#" label="Main action"&gt;&lt;a href="#"&gt;Other action&lt;/a&gt;&lt;/bfg-split-button&gt;</code>
		</p>
	</bfg-section.section>
</bfg-section>
