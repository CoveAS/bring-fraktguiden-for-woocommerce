<?php

use BringFraktguiden\Admin\FieldRenderer;

/**
 * @var string $name
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
<input
	name="<?php echo esc_attr($name);?>"
	type="<?php echo esc_attr($type);?>"
	value="<?php echo esc_attr($default); ?>"
	<?php FieldRenderer::attributes($custom_attributes); ?>
	<?php if ($placeholder): ?>
		placeholder="<?php echo esc_attr($placeholder); ?>"
	<?php endif; ?>
	<?php if ($css): ?>
		style="<?php echo esc_attr($css); ?>"
	<?php endif; ?>
>
