<?php
/**
 * Checkbox Box Component
 *
 * Renders a checkbox field inside a card-style container.
 *
 * @var array $args {
 *     @type \BringFraktguiden\Fields\Field $field The checkbox field object.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'field' => null,
]);

if (!$args['field']) {
	return;
}
?>
<div class="bfg-field bfg-field--checkbox-box">
	<?php echo $args['field']; ?>
</div>
