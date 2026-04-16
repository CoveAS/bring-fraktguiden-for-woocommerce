<?php /* Kitchen Sink: Buttons */ ?>

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
