<?php

use BringFraktguiden\Fields\Field;

/**
 * A group of radio cards. Each option carries its own title and description, so
 * the reader can tell the choices apart without leaving the page.
 *
 * An option is either a plain string, which becomes the title, or an array with
 * a 'title' and a 'description'.
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

$checked_value = $value !== '' ? $value : $default;
?>
<div class="bfg-radio-group" role="radiogroup" aria-label="<?php echo esc_attr($title); ?>">
	<?php foreach ($options as $key => $option): ?>
		<?php
		$option_title = is_array($option) ? ($option['title'] ?? '') : $option;
		$option_desc = is_array($option) ? ($option['description'] ?? '') : '';
		?>
		<label class="bfg-radio-card">
			<input
				name="<?php echo esc_attr($name); ?>"
				type="radio"
				value="<?php echo esc_attr($key); ?>"
				<?php Field::attributes($custom_attributes); ?>
				<?php checked($checked_value, $key); ?>
			>
			<div class="bfg-radio-content bfgu:flex bfgu:flex-col bfgu:items-start bfgu:gap-1.5">
				<span class="bfg-radio-title"><?php echo esc_html($option_title); ?></span>
				<?php if ($option_desc): ?>
					<p class="bfg-radio-desc"><?php echo wp_kses_post($option_desc); ?></p>
				<?php endif; ?>
			</div>
		</label>
	<?php endforeach; ?>
</div>
