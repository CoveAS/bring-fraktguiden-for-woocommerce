<?php
/**
 * Input with Suffix Component
 *
 * @var array $args {
 *     @type \BringFraktguiden\Fields\Field $field  The field object.
 *     @type string                         $suffix The suffix text (cm, kg, currency symbol).
 *     @type string                         $size   Suffix size: 'sm' or 'lg'. Default 'sm'.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'field' => null,
	'suffix' => '',
	'size' => 'sm',
]);

$suffix_class = $args['size'] === 'lg' ? 'bfg-suffix-lg' : 'bfg-suffix';
?>
<div class="bfg-input bfg-input--number">
	<?php echo $args['field']->field(); ?>
	<span class="<?php echo esc_attr($suffix_class); ?>"><?php echo esc_html($args['suffix']); ?></span>
</div>
