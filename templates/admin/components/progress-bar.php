<?php
/**
 * Progress Bar Component
 *
 * @var array $args {
 *     @type int    $completed Total completed steps.
 *     @type int    $total     Total steps.
 *     @type string $label     Optional label text.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'completed' => 0,
	'total' => 1,
	'label' => '',
]);

$percentage = $args['total'] > 0 ? ($args['completed'] / $args['total']) * 100 : 0;
?>
<div class="bfg-progress-container">
	<?php if ($args['label']): ?>
		<span class="bfg-progress-badge"><?php echo esc_html($args['label']); ?></span>
	<?php endif; ?>
	<div class="bfg-progress-bar-new">
		<div class="bfg-progress-bar-fill" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
	</div>
</div>
