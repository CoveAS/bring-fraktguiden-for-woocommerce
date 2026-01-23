<?php
/**
 * Box Header Component
 *
 * @var array $args {
 *     @type string $title       Required. Section title.
 *     @type string $description Optional. Description text.
 *     @type bool   $divider     Optional. Whether to show top divider.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'title' => '',
	'description' => '',
	'divider' => false,
]);

$divider_class = $args['divider'] ? 'bfg-box__header--divider' : '';
?>
<div class="bfg-box__header <?php echo esc_attr($divider_class); ?>">
	<h2><?php echo esc_html($args['title']); ?></h2>
	<?php if ($args['description']): ?>
		<p><?php echo wp_kses_post($args['description']); ?></p>
	<?php endif; ?>
</div>
