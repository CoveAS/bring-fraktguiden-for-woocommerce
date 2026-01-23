<?php
/**
 * Badge Component
 *
 * @var array $args {
 *     @type string $text Badge text.
 *     @type string $type Badge type: 'completed', 'in-progress', 'default'.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'text' => '',
	'type' => 'default',
]);

$type_classes = [
	'completed' => 'bfg-badge--completed',
	'in-progress' => 'bfg-badge--in-progress',
	'default' => '',
];

$type_class = $type_classes[$args['type']] ?? '';
?>
<span class="bfg-badge <?php echo esc_attr($type_class); ?>">
	<?php echo esc_html($args['text']); ?>
</span>
