<?php
/**
 * Custom Select Component
 *
 * @var array $args {
 *     @type string $name        Required. Input name attribute.
 *     @type array  $options     Required. Associative array of value => label options.
 *     @type string $selected    Optional. Currently selected value.
 *     @type string $placeholder Optional. Placeholder text.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'name' => '',
	'options' => [],
	'selected' => '',
	'placeholder' => '',
]);

$name = $args['name'];
$options = $args['options'];
$selected = $args['selected'];
$placeholder = $args['placeholder'];

// Determine the current selected label
$selectedLabel = $placeholder ?: '';
$selectedValue = $selected;
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
<div class="bfg-input bfg-input--select">
	<div class="bfg-custom-select" id="<?php echo $uniqueId; ?>">
		<!-- Hidden native select for form submission -->
		<select
			name="<?php echo esc_attr($name); ?>"
			id="<?php echo esc_attr($name); ?>"
			class="bfg-custom-select__native"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php if ($placeholder): ?>
				<option value="" <?php echo !$selected ? 'selected' : ''; ?> disabled><?php echo esc_html($placeholder); ?></option>
			<?php endif; ?>
			<?php foreach ($options as $key => $option): ?>
				<option
					value="<?php echo esc_attr($key); ?>"
					<?php selected($selected, $key); ?>
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
</div>
