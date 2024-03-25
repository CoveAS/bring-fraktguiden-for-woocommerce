<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Field;

/**
 * @var string $title
 * @var string $name
 * @var bool $value
 * @var string $type
 * @var string $label
 * @var string $description
 * @var string $desc_tip
 * @var string $default
 * @var string $placeholder
 * @var string $css
 * @var array $custom_attributes
 * @var array $options
 */
?>
<label>
	<input
		name="<?php echo esc_attr($name); ?>"
		type="<?php echo esc_attr($type); ?>"
		value="<?php echo esc_attr($default); ?>"
		<?php Field::attributes($custom_attributes); ?>
		<?php if ($placeholder): ?>
			placeholder="<?php echo esc_attr($placeholder); ?>"
		<?php endif; ?>
		<?php if ($css): ?>
			style="<?php echo esc_attr($css); ?>"
		<?php endif; ?>
		<?php if ($value): ?>
			checked="checked"
		<?php endif; ?>
	>
	<span><?php echo wp_kses_post($label); ?></span>
</label>
