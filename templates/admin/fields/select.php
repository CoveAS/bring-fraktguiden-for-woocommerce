<?php

use BringFraktguiden\Admin\FieldRenderer;
use BringFraktguiden\Fields\Field;

/**
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
?>
<select
	name="<?php echo esc_attr($name); ?>"
	type="<?php echo esc_attr($type); ?>"
	<?php Field::attributes($custom_attributes); ?>
	<?php if ($css): ?>
		style="<?php echo esc_attr($css); ?>"
	<?php endif; ?>
>
	<?php if ($placeholder): ?>
		<option <?php echo $value ? '' : 'selected'; ?> disabled><?php echo esc_attr($placeholder); ?></option>
	<?php endif; ?>
	<?php foreach ($options as $key => $option): ?>
		<option
			value="<?php echo esc_attr($key); ?>"
			<?php if ($value && $value == $key ): ?>
				selected="selected"
			<?php elseif ($default && $default == $key ): ?>
				selected="selected"
			<?php endif;?>
		><?php echo esc_html($option); ?></option>
	<?php endforeach; ?>
</select>
