<?php

use BringFraktguiden\Fields\Field;

/**
 * The browser gets a plain select. custom-select.js turns it into the widget.
 * Without that script the field still works as a native select.
 *
 * @var string $name
 * @var string $title
 * @var string $value
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

$selected_value = $value !== '' ? $value : $default;
?>
<select
	id="<?php echo esc_attr($name); ?>"
	name="<?php echo esc_attr($name); ?>"
	class="bfg-custom-select"
	<?php Field::attributes($custom_attributes); ?>
	<?php if ($css): ?>style="<?php echo esc_attr($css); ?>"<?php endif; ?>
>
	<?php if ($placeholder): ?>
		<option value="" disabled <?php selected($selected_value, ''); ?>><?php echo esc_html($placeholder); ?></option>
	<?php endif; ?>
	<?php foreach ($options as $key => $option): ?>
		<option value="<?php echo esc_attr($key); ?>" <?php selected($selected_value, $key); ?>><?php echo esc_html($option); ?></option>
	<?php endforeach; ?>
</select>
