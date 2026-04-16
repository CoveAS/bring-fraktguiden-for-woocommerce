<?php /* Kitchen Sink: Buttons */ ?>

<?php
$ks_variants = [
	[ 'Primary',   'primary'   ],
	[ 'Secondary', 'secondary' ],
	[ 'Outline',   'outline'   ],
];

$ks_sizes = [
	[ 'Small',  'bfg-btn--sm' ],
	[ 'Medium', ''             ],
	[ 'Large',  'bfg-btn--lg' ],
];

$ks_states = [
	[ 'Default', ''                 ],
	[ 'Hover',   'bfg-btn--hover'   ],
	[ 'Pressed', 'bfg-btn--pressed' ],
	[ 'Disabled','bfg-btn--disabled'],
];

$ks_text_states = [
	[ 'Default', ''                 ],
	[ 'Hover',   'bfg-btn--hover'   ],
	[ 'Pressed', 'bfg-btn--pressed' ],
	[ 'Disabled','bfg-btn--disabled'],
];
?>

<!-- Buttons -->
<bfg-section>
	<bfg-section.header title="Buttons"></bfg-section.header>
	<bfg-section.section>

		<?php foreach ( $ks_variants as [ $variant_label, $variant ] ) : ?>
		<div class="bfg-ks-variant">
			<p class="bfg-field-group-title bfgu:mb-5"><?php echo esc_html( $variant_label ); ?></p>
			<?php foreach ( $ks_sizes as [ $size_label, $size_class ] ) : ?>
			<div class="bfg-ks-size">
				<p class="bfg-ks-size-label"><?php echo esc_html( $size_label ); ?></p>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-2">
					<?php foreach ( $ks_states as [ $state_label, $state_class ] ) :
						$classes = trim( "bfg-btn bfg-btn--{$variant} {$size_class} {$state_class}" );
					?>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span class="bfg-ks-state-label"><?php echo esc_html( $state_label ); ?></span>
						<button class="<?php echo esc_attr( $classes ); ?>"><bfg-t>Button</bfg-t></button>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>

		<!-- Full Width -->
		<div class="bfg-ks-variant">
			<p class="bfg-field-group-title bfgu:mb-3"><bfg-t>Full Width</bfg-t></p>
			<div style="max-width:500px">
				<button class="bfg-btn bfg-btn--primary bfg-btn--full-width"><bfg-t>Full Width Button</bfg-t></button>
			</div>
		</div>

		<!-- Text -->
		<div>
			<p class="bfg-field-group-title bfgu:mb-5"><bfg-t>Text</bfg-t></p>
			<?php foreach ( $ks_sizes as [ $size_label, $size_class ] ) : ?>
			<div class="bfg-ks-size">
				<p class="bfg-ks-size-label"><?php echo esc_html( $size_label ); ?></p>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-2">
					<?php foreach ( $ks_text_states as [ $state_label, $state_class ] ) :
						$classes = trim( "bfg-btn bfg-btn--text {$size_class} {$state_class}" );
					?>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
						<span class="bfg-ks-state-label"><?php echo esc_html( $state_label ); ?></span>
						<button class="<?php echo esc_attr( $classes ); ?>"><bfg-t>Text Button</bfg-t></button>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

	</bfg-section.section>
</bfg-section>
