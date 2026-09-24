<?php

/**
 * A group of checkboxes. The field saves the list of keys that are checked.
 *
 * @var string $name
 * @var string $title
 * @var array  $value The checked keys.
 * @var array  $options Key => label.
 */
?>
<ul class="bfg-checkbox-list" role="group" aria-label="<?php echo esc_attr($title); ?>">
	<?php foreach ($options as $key => $option_label): ?>
		<li class="bfg-input--checkbox">
			<label>
				<input
					name="<?php echo esc_attr($name); ?>[]"
					type="checkbox"
					value="<?php echo esc_attr($key); ?>"
					<?php checked(in_array($key, (array) $value, true)); ?>
				>
				<span class="bfg-checkbox-title"><?php echo esc_html($option_label); ?></span>
			</label>
		</li>
	<?php endforeach; ?>
</ul>
