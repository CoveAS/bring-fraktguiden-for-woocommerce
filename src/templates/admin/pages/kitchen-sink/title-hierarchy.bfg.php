<?php /* Kitchen Sink: Title Hierarchy */ ?>

<?php
$ks_levels = [
	[ 1, 'Hero / Page Title',       '32px · Semibold 600 · lh 1.2 &nbsp;/&nbsp; 16px · Regular 400 · lh 1.5' ],
	[ 2, 'Section Title',           '24px · Semibold 600 · lh 1.3 &nbsp;/&nbsp; 16px · Regular 400 · lh 1.5' ],
	[ 3, 'Subsection Title',        '20px · Semibold 600 · lh 1.3 &nbsp;/&nbsp; 14px · Regular 400 · lh 1.5' ],
	[ 4, 'Card / Component Title',  '18px · Medium 500 · lh 1.4 &nbsp;/&nbsp; 14px · Regular 400 · lh 1.5'   ],
	[ 5, 'Small Component Title',   '16px · Medium 500 · lh 1.4 &nbsp;/&nbsp; 14px · Regular 400 · lh 1.5'   ],
	[ 6, 'Label / Caption Title',   '14px · Medium 500 · lh 1.4 &nbsp;/&nbsp; 14px · Regular 400 · lh 1.5'   ],
];
?>

<!-- Title Hierarchy -->
<bfg-section>
	<bfg-section.header title="Title Hierarchy" description="6-level heading system with heading and description pairings"></bfg-section.header>
	<bfg-section.section>
		<div class="bfgu:flex bfgu:flex-col bfgu:divide-y bfgu:divide-[var(--bfg-border)]">
			<?php foreach ( $ks_levels as [ $level, $role, $spec ] ) : ?>
			<div class="bfgu:py-6 bfgu:flex bfgu:gap-8 bfgu:items-start">
				<div class="bfgu:w-48 bfgu:shrink-0">
					<code class="bfg-ks-level-label">Level <?php echo $level; ?></code>
					<p class="bfg-ks-level-sublabel"><?php echo esc_html( $role ); ?></p>
				</div>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-1">
					<p class="bfg-ks-heading-example bfg-ks-heading-<?php echo $level; ?>">The quick brown fox</p>
					<p class="bfg-ks-spec-text"><?php echo $spec; ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</bfg-section.section>
</bfg-section>
