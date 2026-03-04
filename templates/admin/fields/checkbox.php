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

$desc_content = trim(implode(' ', [$description, $desc_tip]));
?>
<label>
	<input
		id="<?php echo esc_attr($name); ?>"
		name="<?php echo esc_attr($name); ?>"
		type="<?php echo esc_attr($type); ?>"
		value="1"
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
	<div class="bfg-checkbox-content">
		<span class="bfg-checkbox-title"><?php echo wp_kses_post($label); ?></span>
		<?php if ($desc_content): ?>
			<p class="bfg-checkbox-desc"><?php echo wp_kses_post($desc_content); ?></p>
		<?php endif; ?>
	</div>
</label>
