<?php
/**
 * Field Wrapper Component
 *
 * @var array $args {
 *     @type string $content Inner HTML content (label + field).
 *     @type string $class   Additional CSS classes.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'content' => '',
	'class' => '',
]);

$classes = 'bfg-field';
if ($args['class']) {
	$classes .= ' ' . $args['class'];
}
?>
<div class="<?php echo esc_attr($classes); ?>">
	<?php echo $args['content']; ?>
</div>
