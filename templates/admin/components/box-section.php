<?php
/**
 * Box Section Component
 *
 * @var array $args {
 *     @type string $content Inner HTML content.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'content' => '',
]);
?>
<div class="bfg-box__section">
	<?php echo $args['content']; ?>
</div>
