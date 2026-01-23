<?php
/**
 * Feature List Component
 *
 * @var array $args {
 *     @type array $features Array of feature strings.
 *     @type bool  $compact  Whether to use compact styling.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'features' => [],
	'compact' => false,
]);

$list_class = 'bfg-pro-features-grid';
if ($args['compact']) {
	$list_class .= ' bfg-pro-features-grid--compact';
}

$checkmark_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<ul class="<?php echo esc_attr($list_class); ?>">
	<?php foreach ($args['features'] as $feature): ?>
		<li>
			<?php echo $checkmark_svg; ?>
			<?php echo esc_html($feature); ?>
		</li>
	<?php endforeach; ?>
</ul>
