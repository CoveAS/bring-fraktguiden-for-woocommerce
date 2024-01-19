<?php

use BringFraktguiden\Admin\FieldRenderer;

/**
 * @var string $title
 * @var string $type
 * @var string $label
 * @var string $desc_tip
 * @var string $default
 * @var string $placeholder
 * @var string $css
 * @var array $custom_attributes
 * @var array $options
 */
?>
<select
	type="<?php echo esc_attr($type);?>"
	<?php FieldRenderer::attributes($custom_attributes); ?>
	<?php if ($css): ?>
		style="<?php echo esc_attr($css); ?>"
	<?php endif; ?>
>
	<?php foreach ($options as $key => $option): ?>
		<option
			<?php echo $value === $key ? 'selected="selected"' : ''; ?>
		><?php echo esc_html($option); ?></option>
	<?php endforeach; ?>
</select>
