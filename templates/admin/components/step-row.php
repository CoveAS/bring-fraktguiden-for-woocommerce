<?php
/**
 * Step Row Component
 *
 * @var array $args {
 *     @type int    $number       Step number.
 *     @type string $label        Step title.
 *     @type string $description  Step description.
 *     @type string $action       URL for the step action.
 *     @type bool   $completed    Whether step is completed.
 *     @type bool   $in_progress  Whether step is currently active.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'number' => 1,
	'label' => '',
	'description' => '',
	'action' => '#',
	'completed' => false,
	'in_progress' => false,
]);

$row_class = 'bfg-step-row';
if ($args['in_progress']) {
	$row_class .= ' bfg-step--in-progress';
}

$checkmark_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="#16A34A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<a href="<?php echo esc_url($args['action']); ?>" class="<?php echo esc_attr($row_class); ?>">
	<div class="bfg-step-row__indicator">
		<?php if ($args['completed']): ?>
			<?php echo $checkmark_svg; ?>
		<?php else: ?>
			<div class="bfg-step-row__number"><?php echo esc_html($args['number']); ?></div>
		<?php endif; ?>
	</div>
	<div class="bfg-step-row__content">
		<div class="bfg-step-row__label"><?php echo esc_html($args['label']); ?></div>
		<?php if ($args['description']): ?>
			<div class="bfg-step-row__description"><?php echo esc_html($args['description']); ?></div>
		<?php endif; ?>
	</div>
	<?php if ($args['completed']): ?>
		<span class="bfg-badge bfg-badge--completed"><?php esc_html_e('Completed', 'bring-fraktguiden-for-woocommerce'); ?></span>
	<?php elseif ($args['in_progress']): ?>
		<span class="bfg-badge bfg-badge--in-progress"><?php esc_html_e('In progress', 'bring-fraktguiden-for-woocommerce'); ?></span>
	<?php endif; ?>
</a>
