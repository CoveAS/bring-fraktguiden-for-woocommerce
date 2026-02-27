<?php
/**
 * Validated Input Field Component
 *
 * Renders an input field with validation support, error display, and accessibility features.
 *
 * @var array $args {
 *     @type string $id            Required. Field ID.
 *     @type string $name          Required. Field name attribute.
 *     @type string $type          Input type: 'text', 'email', 'tel'. Default 'text'.
 *     @type string $label         Required. Field label text.
 *     @type bool   $required      Whether field is required. Default false.
 *     @type array  $validation    Validation rules: ['required', 'email', 'phone'].
 *     @type string $error_message Error message to display.
 *     @type string $description   Help text displayed below field.
 *     @type string $placeholder   Placeholder text.
 *     @type int    $maxlength     Max length attribute.
 *     @type string $autocomplete  Autocomplete attribute.
 *     @type string $value         Current value.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'id' => '',
	'name' => '',
	'type' => 'text',
	'label' => '',
	'required' => false,
	'validation' => [],
	'error_message' => '',
	'description' => '',
	'placeholder' => '',
	'maxlength' => null,
	'autocomplete' => '',
	'value' => '',
]);

$validation_rules = is_array($args['validation']) ? implode('|', $args['validation']) : $args['validation'];
$error_id = $args['id'] . '_error';
$help_id = $args['description'] ? $args['id'] . '_help' : '';

$describedby_parts = [];
if ($help_id) {
	$describedby_parts[] = $help_id;
}
if ($args['error_message']) {
	$describedby_parts[] = $error_id;
}
$aria_describedby = implode(' ', $describedby_parts);
?>
<div class="bfg-field"<?php echo $validation_rules ? ' data-validate="' . esc_attr($validation_rules) . '"' : ''; ?>>
	<label for="<?php echo esc_attr($args['id']); ?>">
		<?php echo esc_html($args['label']); ?>
		<?php if ($args['required']): ?>
			<span class="bfg-required" aria-hidden="true">*</span>
		<?php endif; ?>
	</label>
	<input
		type="<?php echo esc_attr($args['type']); ?>"
		id="<?php echo esc_attr($args['id']); ?>"
		name="<?php echo esc_attr($args['name']); ?>"
		value="<?php echo esc_attr($args['value']); ?>"
		<?php if ($args['maxlength']): ?>maxlength="<?php echo esc_attr($args['maxlength']); ?>"<?php endif; ?>
		<?php if ($args['placeholder']): ?>placeholder="<?php echo esc_attr($args['placeholder']); ?>"<?php endif; ?>
		<?php if ($args['autocomplete']): ?>autocomplete="<?php echo esc_attr($args['autocomplete']); ?>"<?php endif; ?>
		<?php if ($args['required']): ?>required aria-required="true"<?php endif; ?>
		<?php if ($aria_describedby): ?>aria-describedby="<?php echo esc_attr($aria_describedby); ?>"<?php endif; ?>
	/>
	<?php if ($args['error_message']): ?>
		<div class="bfg-field__validation bfg-field__validation--error" id="<?php echo esc_attr($error_id); ?>" role="alert">
			<svg class="bfg-field__validation-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
				<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
			</svg>
			<span><?php echo esc_html($args['error_message']); ?></span>
		</div>
	<?php endif; ?>
	<?php if ($args['description']): ?>
		<p class="bfg-description" id="<?php echo esc_attr($help_id); ?>"><?php echo wp_kses_post($args['description']); ?></p>
	<?php endif; ?>
</div>
