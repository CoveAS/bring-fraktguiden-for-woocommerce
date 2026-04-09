<?php
/**
 * Kitchen Sink - Component Library & Design System Reference
 *
 * This page demonstrates all available UI components and CSS classes
 * used throughout the Bring Fraktguiden admin interface.
 *
 * Only visible when BRING_ENVIRONMENT === 'local'
 */
?>

<div class="wrap bfg-admin-page bfg-admin-page__kitchen-sink">
	<div class="bfg-page__header">
		<h1><bfg-t>Kitchen Sink - Component Library</bfg-t></h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<!-- Boxes & Containers -->
		<bfg-section>
			<bfg-section.header title="Boxes & Containers"
				description="Primary container component used throughout the admin"></bfg-section.header>
			<bfg-section.section>
				<p><strong>Classes:</strong> <code>.bfg-section</code>, <code>.bfg-section__header</code>,
					<code>.bfg-section__section</code>
				</p>
				<p><bfg-t>Use .bfg-section for main content sections. Contains header with title/description and section for
						content.</bfg-t>
				</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Typography -->
		<bfg-section>
			<bfg-section.header title="Typography" description="8pt scaling system for consistent visual rhythm"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Type Scale</bfg-t>
				</h3>
				<table class="bfg-type-scale-table" style="width: 100%; border-collapse: collapse; margin-bottom: 32px;">
					<thead>
						<tr style="text-align: left; border-bottom: 1px solid var(--bfg-border);">
							<th style="padding: 8px 16px 8px 0; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Size</th>
							<th style="padding: 8px 16px; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Line Height</th>
							<th style="padding: 8px 16px; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Weight</th>
							<th style="padding: 8px 0 8px 16px; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Example</th>
						</tr>
					</thead>
					<tbody>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>48px</code></td>
							<td style="padding: 16px;"><code>56px</code></td>
							<td style="padding: 16px;"><code>600</code> Semibold</td>
							<td style="padding: 16px 0 16px 16px; font-size: 48px; line-height: 56px; font-weight: 600;">Display</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>32px</code></td>
							<td style="padding: 16px;"><code>40px</code></td>
							<td style="padding: 16px;"><code>600</code> Semibold</td>
							<td style="padding: 16px 0 16px 16px; font-size: 32px; line-height: 40px; font-weight: 600;">Heading XL</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>28px</code></td>
							<td style="padding: 16px;"><code>36px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 28px; line-height: 36px; font-weight: 500;">Heading L</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>24px</code></td>
							<td style="padding: 16px;"><code>32px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 24px; line-height: 32px; font-weight: 500;">Heading M</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>20px</code></td>
							<td style="padding: 16px;"><code>28px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 20px; line-height: 28px; font-weight: 500;">Heading S</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>16px</code></td>
							<td style="padding: 16px;"><code>24px</code></td>
							<td style="padding: 16px;"><code>400</code> Regular</td>
							<td style="padding: 16px 0 16px 16px; font-size: 16px; line-height: 24px; font-weight: 400;">Body / Base</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>15px</code></td>
							<td style="padding: 16px;"><code>24px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 15px; line-height: 24px; font-weight: 500;">Label</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>14px</code></td>
							<td style="padding: 16px;"><code>20px</code></td>
							<td style="padding: 16px;"><code>400</code> Regular</td>
							<td style="padding: 16px 0 16px 16px; font-size: 14px; line-height: 20px; font-weight: 400;">Small / Caption</td>
						</tr>
						<tr>
							<td style="padding: 16px 16px 16px 0;"><code>13px</code></td>
							<td style="padding: 16px;"><code>20px</code></td>
							<td style="padding: 16px;"><code>400</code> Regular</td>
							<td style="padding: 16px 0 16px 16px; font-size: 13px; line-height: 20px; font-weight: 400;">Extra Small</td>
						</tr>
					</tbody>
				</table>

				<h3 class="bfg-field-group-title">
					<bfg-t>Font Weights</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-8">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">400</code>
						<span style="font-size: 20px; font-weight: 400;">Regular — Body text, descriptions, captions</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">500</code>
						<span style="font-size: 20px; font-weight: 500;">Medium — Labels, headings, buttons</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">600</code>
						<span style="font-size: 20px; font-weight: 600;">Semibold — Display headings, field group titles</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">700</code>
						<span style="font-size: 20px; font-weight: 700;">Bold — Strong emphasis</span>
					</div>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Medium Weight Sizes</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-8">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">14px</code>
						<span style="font-size: 14px; font-weight: 500;">Medium 14 — Small labels, compact UI</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">16px</code>
						<span style="font-size: 16px; font-weight: 500;">Medium 16 — Default labels, buttons</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">18px</code>
						<span style="font-size: 18px; font-weight: 500;">Medium 18 — Larger labels, subheadings</span>
					</div>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Text Formatting</bfg-t>
				</h3>
				<p>Regular paragraph text with <strong>bold text</strong> and <em>italic text</em>.</p>
				<p>Links look <a href="#">like this</a> and inline <code>code snippets</code> use monospace.</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Notice Banners -->
		<bfg-section>
			<bfg-section.header title="Notice Banners"></bfg-section.header>
			<bfg-section.section>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-4">
					<bfg-notice type="warning">This is a warning notice banner</bfg-notice>
					<bfg-notice type="info">This is an info notice banner</bfg-notice>
					<bfg-notice type="success">This is a success notice banner</bfg-notice>
					<bfg-notice type="error">This is an error notice banner</bfg-notice>
				</div>
			</bfg-section.section>
		</bfg-section>

		<!-- Buttons & Links -->
		<bfg-section>
			<bfg-section.header title="Buttons"></bfg-section.header>
			<bfg-section.section>
<?php
/* ── Kitchen Sink: Button Matrix helpers ─────────────────────────── */
function bfg_ks_icon( int $px, float $sw, string $path ): string {
	return "<svg width=\"{$px}\" height=\"{$px}\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"{$sw}\" stroke-linecap=\"round\" stroke-linejoin=\"round\">{$path}</svg>";
}

function bfg_ks_row( string $label, string $variant, string $sz, int $px, float $sw, string $style = '', bool $disabled = false ): void {
	$v   = "bfg-btn bfg-btn--{$variant}" . ( $sz ? " {$sz}" : '' );
	$io  = "bfg-btn bfg-btn--{$variant} bfg-btn--icon-only" . ( $sz ? " {$sz}" : '' );
	$st  = $style    ? " style=\"{$style}\""  : '';
	$dis = $disabled ? ' disabled'            : '';
	$dl  = bfg_ks_icon( $px, $sw, '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>' );
	$arr = bfg_ks_icon( $px, $sw, '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>' );
	$pls = bfg_ks_icon( $px, $sw, '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>' );
	echo "
				<div class=\"bfgu:flex bfgu:items-center bfgu:gap-3\">
					<span style=\"width:68px;flex-shrink:0;font-size:10px;text-transform:uppercase;letter-spacing:.07em;color:var(--bfg-text-muted)\">{$label}</span>
					<button class=\"{$v}\"{$st}{$dis}>Button</button>
					<button class=\"{$v}\"{$st}{$dis}>{$dl} Button</button>
					<button class=\"{$v}\"{$st}{$dis}>Button {$arr}</button>
					<button class=\"{$io}\"{$st}{$dis}>{$pls}</button>
				</div>";
}

function bfg_ks_variant( string $name, string $variant, array $hover, array $pressed ): void {
	$sizes = [
		[ 'Small',  'bfg-btn--sm', 13, 2.5 ],
		[ 'Medium', '',            16, 2.0  ],
		[ 'Large',  'bfg-btn--lg', 18, 2.0  ],
	];
	echo "<div style=\"margin-bottom:40px;padding-bottom:40px;border-bottom:1px solid var(--bfg-border)\">";
	echo "<p class=\"bfg-field-group-title bfgu:mb-5\">" . esc_html( $name ) . "</p>";
	foreach ( $sizes as $i => [ $size_label, $sz, $px, $sw ] ) {
		$top = $i === 0 ? '0' : '32px';
		echo "<div style=\"margin-top:{$top};padding-top:" . ( $i === 0 ? '0' : '28px' ) . ";border-top:" . ( $i === 0 ? 'none' : '1px dashed var(--bfg-border)' ) . "\">";
		echo "<p style=\"font-size:12px;color:var(--bfg-text-muted);margin:0 0 10px;font-weight:600;text-transform:uppercase;letter-spacing:.06em\">{$size_label}</p>";
		echo "<div class=\"bfgu:flex bfgu:flex-col bfgu:gap-2\">";
		bfg_ks_row( 'Default',  $variant, $sz, $px, $sw );
		bfg_ks_row( 'Hover',    $variant, $sz, $px, $sw, implode( ';', $hover )    . ';pointer-events:none' );
		bfg_ks_row( 'Pressed',  $variant, $sz, $px, $sw, implode( ';', $pressed )  . ';pointer-events:none' );
		bfg_ks_row( 'Disabled', $variant, $sz, $px, $sw, '', true );
		echo "</div></div>";
	}
	echo "</div>";
}
?>

<?php
	bfg_ks_variant( 'Primary', 'primary',
		[ 'background:#1D4ED8', 'border-color:#1D4ED8', 'color:#fff' ],
		[ 'background:#1e40af', 'border-color:#1e40af', 'color:#fff' ]
	);
	bfg_ks_variant( 'Secondary', 'secondary',
		[ 'background:#F9FAFB', 'border-color:#9CA3AF', 'color:#000' ],
		[ 'background:#F3F4F6', 'color:#111827', 'transform:translateY(1px)' ]
	);
	bfg_ks_variant( 'Outline', 'outline',
		[ 'background:#DBEAFE', 'color:#1D4ED8', 'border-color:#2563EB' ],
		[ 'background:#BFDBFE', 'color:#1D4ED8', 'transform:translateY(1px)' ]
	);
	bfg_ks_variant( 'Ghost', 'ghost',
		[ 'background:#F3F4F6', 'color:#111827' ],
		[ 'background:#E5E7EB', 'color:#111827', 'transform:translateY(1px)' ]
	);
	bfg_ks_variant( 'Grey', 'grey',
		[ 'background:#E5E7EB', 'color:#111827' ],
		[ 'background:#D1D5DB', 'color:#111827', 'transform:translateY(1px)' ]
	);
?>

				<!-- ── Full Width ─────────────────────────────────────────────── -->
				<p class="bfg-field-group-title bfgu:mb-3"><bfg-t>Full Width</bfg-t></p>
				<div class="bfgu:flex bfgu:gap-4 bfgu:mb-0" style="max-width:500px">
					<button class="bfg-btn bfg-btn--primary bfg-btn--full-width">
						<bfg-t>Full Width Button</bfg-t>
					</button>
				</div>

<?php
/* ── Text Button ─────────────────────────────────────────────────── */
$text_sizes = [
	[ 'Small',   'bfg-btn--sm', 13 ],
	[ 'Medium',  '',            16 ],
	[ 'Large',   'bfg-btn--lg', 18 ],
];
echo "<div style=\"margin-top:40px;padding-top:40px;border-top:1px solid var(--bfg-border)\">";
echo "<p class=\"bfg-field-group-title bfgu:mb-5\">Text</p>";
foreach ( $text_sizes as $i => [ $size_label, $sz, $px ] ) {
	$v   = 'bfg-btn bfg-btn--text' . ( $sz ? " {$sz}" : '' );
	$top = $i === 0 ? '0' : '32px';
	$border = $i === 0 ? 'none' : '1px dashed var(--bfg-border)';
	echo "<div style=\"margin-top:{$top};padding-top:" . ( $i === 0 ? '0' : '28px' ) . ";border-top:{$border}\">";
	echo "<p style=\"font-size:12px;color:var(--bfg-text-muted);margin:0 0 10px;font-weight:600;text-transform:uppercase;letter-spacing:.06em\">{$size_label}</p>";
	echo "<div class=\"bfgu:flex bfgu:flex-col bfgu:gap-2\">";
	foreach ( [ 'Default' => '', 'Hover' => 'color:var(--bfg-primary-hover);text-decoration:underline;pointer-events:none', 'Pressed' => 'color:#1e40af;pointer-events:none', 'Disabled' => '' ] as $state => $style ) {
		$st  = $style ? " style=\"{$style}\"" : '';
		$dis = $state === 'Disabled' ? ' disabled' : '';
		echo "
				<div class=\"bfgu:flex bfgu:items-center bfgu:gap-3\">
					<span style=\"width:68px;flex-shrink:0;font-size:10px;text-transform:uppercase;letter-spacing:.07em;color:var(--bfg-text-muted)\">{$state}</span>
					<button class=\"{$v}\"{$st}{$dis}>Text Button</button>
				</div>";
	}
	echo "</div></div>";
}
echo "</div>";
?>

			</bfg-section.section>
		</bfg-section>


		<!-- Badges -->
		<bfg-section>
			<bfg-section.header title="Badges"></bfg-section.header>
			<bfg-section.section>
				<div class="bfgu:flex bfgu:gap-4 bfgu:items-center bfgu:mb-3">
					<bfg-badge.completed>Done</bfg-badge.completed>
					<bfg-badge.in-progress>In progress</bfg-badge.in-progress>
				</div>
				<div class="bfgu:flex bfgu:gap-4 bfgu:items-center bfgu:mb-5">
					<bfg-badge.completed.md>Done</bfg-badge.completed.md>
					<bfg-badge.completed.text>Done</bfg-badge.completed.text>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-badge</code>,
					<code>.bfg-badge--completed</code>, <code>.bfg-badge--in-progress</code>,
					<code>.bfg-badge--md</code>, <code>.bfg-badge--completed-text</code>
				</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Form Fields -->
		<bfg-section>
			<bfg-section.header title="Form Fields" description="All input types with MD / LG size variants"></bfg-section.header>
			<bfg-section.section>

				<?php $size_label = 'width:28px;flex-shrink:0;font-size:10px;text-transform:uppercase;letter-spacing:.07em;color:var(--bfg-text-muted);display:flex;align-items:center'; ?>

				<!-- ── Text Input ──────────────────────────────────────────── -->
				<h3 class="bfg-field-group-title"><bfg-t>Text Input</bfg-t></h3>
				<div>
					<bfg-field.text id="demo-text" label="Label" description="Help text goes here">
						<input type="text" id="demo-text" name="demo-text" placeholder="Placeholder text">
					</bfg-field.text>
				</div>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10" style="">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">MD</span>
						<div class="bfg-field bfgu:flex-1" style="margin:0"><input type="text" placeholder="Medium — 40px" style="width:100%"></div>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">LG</span>
						<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0"><input type="text" placeholder="Large — 48px" style="width:100%"></div>
					</div>
				</div>

				<!-- ── Number Input ────────────────────────────────────────── -->
				<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>Number Input</bfg-t></h3>
				<div>
					<bfg-field.number id="demo-number" name="demo-number" label="Amount (small suffix)" value="100" suffix="NOK"></bfg-field.number>
					<bfg-field.number id="demo-number-lg-s" name="demo-number-lg-s" label="Amount (large suffix)" value="250" suffix-lg="NOK"></bfg-field.number>
				</div>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10" style="">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">MD</span>
						<div class="bfg-field bfgu:flex-1" style="margin:0">
							<div class="bfg-input bfg-input--number"><input type="number" value="100"><span class="bfg-suffix">NOK</span></div>
						</div>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">LG</span>
						<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0">
							<div class="bfg-input bfg-input--number"><input type="number" value="100"><span class="bfg-suffix">NOK</span></div>
						</div>
					</div>
				</div>

				<!-- ── Select ──────────────────────────────────────────────── -->
				<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>Select</bfg-t></h3>
				<div>
					<bfg-field.select id="demo-select" name="demo-select" label="Label" :options="[
						'option1' => __('Option 1', 'bring-fraktguiden-for-woocommerce'),
						'option2' => __('Option 2', 'bring-fraktguiden-for-woocommerce'),
						'option3' => __('Option 3', 'bring-fraktguiden-for-woocommerce'),
					]" value="option1" placeholder="Select an option"></bfg-field.select>
				</div>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10" style="">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">MD</span>
						<div class="bfg-field bfgu:flex-1" style="margin:0"><select style="width:100%"><option>Option 1</option><option>Option 2</option></select></div>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">LG</span>
						<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0"><select style="width:100%"><option>Option 1</option><option>Option 2</option></select></div>
					</div>
				</div>

				<!-- ── Checkbox ────────────────────────────────────────────── -->
				<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>Checkbox</bfg-t></h3>
				<div class="bfgu:mb-10" style="">
					<bfg-field.checkbox name="demo-checkbox-1" value="1" title="With description"
						description="Description text appears below the title"></bfg-field.checkbox>
					<bfg-field.checkbox name="demo-checkbox-2" value="1" title="Checked state" checked></bfg-field.checkbox>
				</div>

				<!-- ── Grid Layouts ────────────────────────────────────────── -->
				<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>3-Column Grid</bfg-t></h3>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">MD</span>
						<div class="bfg-field bfgu:flex-1" style="margin:0">
							<div class="bfgu:flex bfgu:gap-3">
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="120"><span class="bfg-suffix">cm</span></div></div>
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="80"><span class="bfg-suffix">cm</span></div></div>
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="60"><span class="bfg-suffix">cm</span></div></div>
							</div>
						</div>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">LG</span>
						<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0">
							<div class="bfgu:flex bfgu:gap-3">
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="120"><span class="bfg-suffix">cm</span></div></div>
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="80"><span class="bfg-suffix">cm</span></div></div>
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="60"><span class="bfg-suffix">cm</span></div></div>
							</div>
						</div>
					</div>
				</div>

				<h3 class="bfg-field-group-title"><bfg-t>2-Column Grid</bfg-t></h3>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">MD</span>
						<div class="bfg-field bfgu:flex-1" style="margin:0">
							<div class="bfgu:flex bfgu:gap-3">
								<div class="bfgu:flex-1"><input type="text" value="Standard Shipping" style="width:100%"></div>
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="99"><span class="bfg-suffix-lg">NOK</span></div></div>
							</div>
						</div>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span style="<?php echo $size_label; ?>">LG</span>
						<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0">
							<div class="bfgu:flex bfgu:gap-3">
								<div class="bfgu:flex-1"><input type="text" value="Standard Shipping" style="width:100%"></div>
								<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="99"><span class="bfg-suffix-lg">NOK</span></div></div>
							</div>
						</div>
					</div>
				</div>

			</bfg-section.section>
		</bfg-section>

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
						<bfg-t>API conversion</bfg-t>
						<bfg-step-desc><bfg-t>Connect your Bring API credentials</bfg-t></bfg-step-desc>
						<button class="bfg-btn bfg-btn--primary bfg-btn--sm"><bfg-t>Connect API</bfg-t></button>
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

		<!-- Access Links -->
		<bfg-section>
			<bfg-section.header title="Access Links"
				description="Clickable rows for navigation without status tracking"></bfg-section.header>
			<bfg-section.section>
				<div class="bfg-steps-list">
					<bfg-access-link href="#">
						<bfg-t>MyBring Booking</bfg-t>
						<bfg-step-desc><bfg-t>Create shipping labels directly in WooCommerce</bfg-t></bfg-step-desc>
					</bfg-access-link>

					<bfg-access-link href="#">
						<bfg-t>Pick-up Points</bfg-t>
						<bfg-step-desc><bfg-t>Let customers choose their preferred pickup location</bfg-t></bfg-step-desc>
					</bfg-access-link>

					<bfg-access-link href="#">
						<bfg-t>Free Shipping Threshold</bfg-t>
						<bfg-step-desc><bfg-t>Offer free shipping when cart exceeds a value</bfg-t></bfg-step-desc>
					</bfg-access-link>

					<bfg-access-link href="#">
						<bfg-t>Fixed Prices</bfg-t>
						<bfg-step-desc><bfg-t>Set your own shipping prices instead of Bring rates</bfg-t></bfg-step-desc>
					</bfg-access-link>
				</div>
				<p class="bfg-description bfgu:mt-4"><strong>Usage:</strong>
					<code>&lt;bfg-access-link href="#"&gt;Title&lt;bfg-step-desc&gt;Description&lt;/bfg-step-desc&gt;&lt;/bfg-access-link&gt;</code>
				</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Subscription Status -->
		<bfg-section>
			<bfg-section.header title="Subscription Status" description="Displays license and subscription details"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Active License</bfg-t>
				</h3>
				<bfg-subscription-info class="bfgu:mb-0">
					<bfg-subscription-item.lock class="bfgu:p-0" label="LICENSE STATUS" value="Active" detail="License: PRO-2026-XXXX"></bfg-subscription-item.lock>
					<bfg-subscription-item.calendar class="bfgu:p-0" label="VALID UNTIL" value="March 13, 2027" detail="365 days remaining"></bfg-subscription-item.calendar>
				</bfg-subscription-info>

				<h3 class="bfg-field-group-title">
					<bfg-t>Trial License</bfg-t>
				</h3>
				<bfg-subscription-info class="bfgu:mb-0">
					<bfg-subscription-item.lock class="bfgu:p-0" label="LICENSE STATUS" value="Trial" detail="Activated: March 25, 2026"></bfg-subscription-item.lock>
					<bfg-subscription-item.calendar class="bfgu:p-0" label="VALID UNTIL" value="April 1, 2026" detail="7 days remaining"></bfg-subscription-item.calendar>
				</bfg-subscription-info>
			</bfg-section.section>
		</bfg-section>

		<!-- Feature Cards -->
		<bfg-section>
			<bfg-section.header title="Feature Cards" description="Used across Pro page states — gray icon default, blue when active"></bfg-section.header>
			<bfg-section.section>

				<h3 class="bfg-field-group-title">
					<bfg-t>Default state (gray icon)</bfg-t>
				</h3>
				<div class="bfg-pro-feature-card-grid bfgu:mb-8">
					<bfg-feature-card>
						<bfg-feature-card.icon>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
								<line x1="16" y1="2" x2="16" y2="6"></line>
								<line x1="8" y1="2" x2="8" y2="6"></line>
								<line x1="3" y1="10" x2="21" y2="10"></line>
							</svg>
						</bfg-feature-card.icon>
						<strong class="bfg-feature-card__title"><t>MyBring Booking</t></strong>
						<span class="bfg-feature-card__desc"><t>Book shipments directly from WooCommerce</t></span>
						<bfg-feature-card.benefits>
							<li><t>7-day free trial</t></li>
							<li><t>Test on live site</t></li>
						</bfg-feature-card.benefits>
					</bfg-feature-card>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Active state (blue icon, linked)</bfg-t>
				</h3>
				<div class="bfg-pro-feature-card-grid">
					<bfg-feature-card href="#" active>
						<bfg-feature-card.icon>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
								<line x1="16" y1="2" x2="16" y2="6"></line>
								<line x1="8" y1="2" x2="8" y2="6"></line>
								<line x1="3" y1="10" x2="21" y2="10"></line>
							</svg>
						</bfg-feature-card.icon>
						<strong class="bfg-feature-card__title"><t>MyBring Booking</t></strong>
						<span class="bfg-feature-card__desc"><t>Book shipments directly from WooCommerce</t></span>
						<bfg-feature-card.benefits>
							<li><t>7-day free trial</t></li>
							<li><t>Test on live site</t></li>
						</bfg-feature-card.benefits>
					</bfg-feature-card>
				</div>

			</bfg-section.section>
		</bfg-section>


		<!-- Color & Style Reference -->
		<bfg-section>
			<bfg-section.header title="Component CSS Reference"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Main Classes</bfg-t>
				</h3>
				<ul>
					<li><code>.bfg-admin-page</code> - Main page wrapper</li>
					<li><code>.bfg-page__main</code> - Main content area</li>
					<li><code>.bfg-page__header</code> - Page header section</li>
					<li><code>.bfg-page__header-row</code> - Header row with title and badge</li>
					<li><code>.bfg-section</code> - Card/box container</li>
					<li><code>.bfg-section__header</code> - Box header with title</li>
					<li><code>.bfg-section__section</code> - Box content section</li>
				</ul>

				<h3 class="bfg-field-group-title bfgu:mt-8">
					<bfg-t>Form Classes</bfg-t>
				</h3>
				<ul>
					<li><code>.bfg-field</code> - Form field wrapper</li>
					<li><code>.bfg-field--checkbox-box</code> - Checkbox field variant</li>
					<li><code>.bfg-input</code> - Input wrapper</li>
					<li><code>.bfg-input--number</code> - Number input with suffix</li>
					<li><code>.bfg-input--select</code> - Select input wrapper</li>
					<li><code>.bfg-custom-select</code> - Custom select component</li>
					<li><code>.bfg-suffix</code> - Input suffix (small)</li>
					<li><code>.bfg-suffix-lg</code> - Input suffix (large)</li>
				</ul>

				<h3 class="bfg-field-group-title bfgu:mt-8">
					<bfg-t>Utility Classes</bfg-t>
				</h3>
				<ul>
					<li><code>.bfgu:flex</code> - Flex container</li>
					<li><code>.bfgu:flex-row</code> - Flex direction row</li>
					<li><code>.bfgu:flex-1</code> - Flex item (flex: 1)</li>
					<li><code>.bfgu:gap-4</code> - Gap between flex items</li>
					<li><code>.bfgu:mt-8</code> - Margin top</li>
					<li><code>.bfgu:mb-5</code> - Margin bottom</li>
				</ul>
			</bfg-section.section>
		</bfg-section>
	</div>
</div>