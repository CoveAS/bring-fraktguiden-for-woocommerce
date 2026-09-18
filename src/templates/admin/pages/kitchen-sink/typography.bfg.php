<?php /* Kitchen Sink: Typography */ ?>

<!-- Typography -->
<bfg-section>
	<bfg-section.header title="Typography" description="Four sizes. A size outside this list is a bug."></bfg-section.header>
	<bfg-section.section>
		<h3 class="bfg-field-group-title">
			<bfg-t>Type Scale</bfg-t>
		</h3>
		<p class="bfgu:mb-4">Every font size in the admin UI comes from one of these four steps. A step sets a size and a line height, never a weight.</p>
		<table class="bfg-type-scale-table">
			<thead>
				<tr>
					<th>Token</th>
					<th>Size</th>
					<th>Line Height</th>
					<th>Where it belongs</th>
					<th>Example</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>--bfg-font-2xl</code></td>
					<td><code>24px</code></td>
					<td><code>32px</code></td>
					<td>Page title, card headline, big number</td>
					<td class="bfg-text-2xl">2xl</td>
				</tr>
				<tr>
					<td><code>--bfg-font-base</code></td>
					<td><code>16px</code></td>
					<td><code>24px</code></td>
					<td>Body text, field label, card title</td>
					<td class="bfg-text-base">base</td>
				</tr>
				<tr>
					<td><code>--bfg-font-sm</code></td>
					<td><code>14px</code></td>
					<td><code>20px</code></td>
					<td>Description, hint, button</td>
					<td class="bfg-text-sm">sm</td>
				</tr>
				<tr>
					<td><code>--bfg-font-xs</code></td>
					<td><code>12px</code></td>
					<td><code>16px</code></td>
					<td>Badge, code, meta label</td>
					<td class="bfg-text-xs">xs</td>
				</tr>
			</tbody>
		</table>

		<h3 class="bfg-field-group-title">
			<bfg-t>Font Weights</bfg-t>
		</h3>
		<p class="bfgu:mb-4">Two weights. A weight is a separate choice from a size, so set it with a Tailwind utility.</p>
		<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-8">
			<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
				<code class="bfgu:w-12 bfgu:shrink-0">400</code>
				<span class="bfg-text-base bfgu:font-normal">Regular — body text, descriptions, hints</span>
			</div>
			<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
				<code class="bfgu:w-12 bfgu:shrink-0">600</code>
				<span class="bfg-text-base bfgu:font-semibold">Semibold — titles, labels, buttons, emphasis</span>
			</div>
		</div>

		<h3 class="bfg-field-group-title">
			<bfg-t>Text Formatting</bfg-t>
		</h3>
		<p>Regular paragraph text with <strong>bold text</strong> and <em>italic text</em>.</p>
		<p>Links look <a href="#">like this</a> and inline <code>code snippets</code> use monospace.</p>
	</bfg-section.section>
</bfg-section>
