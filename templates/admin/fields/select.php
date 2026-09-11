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

// Determine the current selected label
$selectedLabel = $placeholder ?: '';
$selectedValue = $value ?: $default;
$isPlaceholder = true;
foreach ($options as $key => $option) {
	if ($selectedValue == $key) {
		$selectedLabel = $option;
		$isPlaceholder = false;
		break;
	}
}
if (!$selectedLabel && !empty($options)) {
	$firstKey = array_key_first($options);
	$selectedLabel = $options[$firstKey];
	$selectedValue = $firstKey;
	$isPlaceholder = false;
}

$uniqueId = 'bfg-select-' . esc_attr($name) . '-' . wp_rand();
?>
<div class="bfg-custom-select" id="<?php echo $uniqueId; ?>">
	<!-- Hidden native select for form submission -->
	<select
		name="<?php echo esc_attr($name); ?>"
		type="<?php echo esc_attr($type); ?>"
		<?php Field::attributes($custom_attributes); ?>
		<?php if ($css): ?>
			style="<?php echo esc_attr($css); ?>"
		<?php endif; ?>
		class="bfg-custom-select__native"
		tabindex="-1"
		aria-hidden="true"
	>
		<?php if ($placeholder): ?>
			<option <?php echo $value ? '' : 'selected'; ?> disabled><?php echo esc_attr($placeholder); ?></option>
		<?php endif; ?>
		<?php foreach ($options as $key => $option): ?>
			<option
				value="<?php echo esc_attr($key); ?>"
				<?php if ($selectedValue == $key): ?>
					selected="selected"
				<?php endif;?>
			><?php echo esc_html($option); ?></option>
		<?php endforeach; ?>
	</select>

	<!-- Custom visible select trigger -->
	<button type="button" class="bfg-custom-select__trigger" aria-haspopup="listbox" aria-expanded="false">
		<span class="bfg-custom-select__value <?php echo $isPlaceholder ? 'is-placeholder' : ''; ?>"><?php echo esc_html($selectedLabel); ?></span>
		<svg class="bfg-custom-select__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M6 9l6 6 6-6"/>
		</svg>
	</button>

	<!-- Custom dropdown list -->
	<div class="bfg-custom-select__dropdown" role="listbox">
		<?php foreach ($options as $key => $option):
			$isSelected = ($selectedValue == $key);
		?>
			<div
				class="bfg-custom-select__option <?php echo $isSelected ? 'is-selected' : ''; ?>"
				data-value="<?php echo esc_attr($key); ?>"
				role="option"
				aria-selected="<?php echo $isSelected ? 'true' : 'false'; ?>"
			>
				<span class="bfg-custom-select__option-text"><?php echo esc_html($option); ?></span>
				<?php if ($isSelected): ?>
					<svg class="bfg-custom-select__check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<polyline points="20 6 9 17 4 12"></polyline>
					</svg>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
