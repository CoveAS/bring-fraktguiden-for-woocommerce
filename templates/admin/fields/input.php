<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Field;

/**
 * @var string $value
 * @var string $name
 * @var string $title
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
<input
	id="<?php echo esc_attr($name); ?>"
	name="<?php echo esc_attr($name); ?>"
	type="<?php echo esc_attr($type); ?>"
	value="<?php echo esc_attr($value); ?>"
	<?php Field::attributes($custom_attributes); ?>
	<?php if ($placeholder !== ''): ?>
		placeholder="<?php echo esc_attr($placeholder); ?>"
	<?php endif; ?>
	<?php if ($css): ?>
		style="<?php echo esc_attr($css); ?>"
	<?php endif; ?>
>
