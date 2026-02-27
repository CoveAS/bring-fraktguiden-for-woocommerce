<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Fields\Field;

/**
 * Component renderer for reusable admin UI components.
 *
 * Usage:
 *   echo Component::render('notice-banner', ['message' => 'Hello', 'type' => 'warning']);
 *   echo Component::noticeBanner('Hello', 'warning');
 *   echo Component::inputWithSuffix($field, 'cm');
 */
class Component
{
	/**
	 * Render a component template with the given arguments.
	 *
	 * @param string $name Component name (maps to templates/admin/components/{name}.php)
	 * @param array  $args Arguments to pass to the template
	 * @return string Rendered HTML
	 */
	public static function render(string $name, array $args = []): string
	{
		$template_path = dirname(__DIR__, 3) . '/templates/admin/components/' . $name . '.php';

		if (!file_exists($template_path)) {
			return '';
		}

		ob_start();
		require $template_path;
		return ob_get_clean();
	}

	/**
	 * Render an input with a suffix (unit) component.
	 *
	 * @param Field  $field  The field object
	 * @param string $suffix The suffix text (cm, kg, currency symbol)
	 * @param string $size   Suffix size: 'sm' or 'lg'
	 * @return string Rendered HTML
	 */
	public static function inputWithSuffix(Field $field, string $suffix, string $size = 'sm'): string
	{
		return self::render('input-with-suffix', [
			'field' => $field,
			'suffix' => $suffix,
			'size' => $size,
		]);
	}

	/**
	 * Render a notice banner component.
	 *
	 * @param string $message The message to display
	 * @param string $type    Banner type: 'warning', 'info', 'success', 'error'
	 * @return string Rendered HTML
	 */
	public static function noticeBanner(string $message, string $type = 'warning'): string
	{
		return self::render('notice-banner', [
			'message' => $message,
			'type' => $type,
		]);
	}

	/**
	 * Render a step row component.
	 *
	 * @param array $args Step row arguments
	 * @return string Rendered HTML
	 */
	public static function stepRow(array $args): string
	{
		return self::render('step-row', $args);
	}

	/**
	 * Render a progress bar component.
	 *
	 * @param int    $completed Total completed steps
	 * @param int    $total     Total steps
	 * @param string $label     Optional label text
	 * @return string Rendered HTML
	 */
	public static function progressBar(int $completed, int $total, string $label = ''): string
	{
		return self::render('progress-bar', [
			'completed' => $completed,
			'total' => $total,
			'label' => $label,
		]);
	}

	/**
	 * Render a status card component.
	 *
	 * @param array  $items Array of ['label' => string, 'value' => string, 'type' => string]
	 * @param string $type  Card type: 'default', 'test', 'trial', 'expired'
	 * @return string Rendered HTML
	 */
	public static function statusCard(array $items, string $type = 'default'): string
	{
		return self::render('status-card', [
			'items' => $items,
			'type' => $type,
		]);
	}

	/**
	 * Render a feature list component.
	 *
	 * @param array  $features Array of feature strings
	 * @param bool   $compact  Whether to use compact styling
	 * @return string Rendered HTML
	 */
	public static function featureList(array $features, bool $compact = false): string
	{
		return self::render('feature-list', [
			'features' => $features,
			'compact' => $compact,
		]);
	}

	/**
	 * Render a custom select dropdown component.
	 *
	 * @param string $name        Input name attribute
	 * @param array  $options     Associative array of value => label options
	 * @param string $selected    Currently selected value
	 * @param string $placeholder Optional placeholder text
	 * @return string Rendered HTML
	 */
	public static function customSelect(string $name, array $options, string $selected = '', string $placeholder = ''): string
	{
		return self::render('custom-select', [
			'name' => $name,
			'options' => $options,
			'selected' => $selected,
			'placeholder' => $placeholder,
		]);
	}

	/**
	 * Render CSS custom properties based on admin color scheme.
	 *
	 * @param string $skin WordPress admin color scheme name
	 * @return string Rendered style tag
	 */
	public static function styles(string $skin = 'fresh'): string
	{
		return self::render('styles', [
			'skin' => $skin,
		]);
	}

	/**
	 * Render a validated input field component with error display.
	 *
	 * @param array $args {
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
	 * @return string Rendered HTML
	 */
	public static function validatedInputField(array $args): string
	{
		return self::render('validated-input-field', $args);
	}

	/**
	 * Render a form section box component.
	 *
	 * @param string $title         Section title
	 * @param string $content       Inner HTML content
	 * @param array  $options {
	 *     @type string $description   Optional description text.
	 *     @type bool   $divider       Whether to show top divider. Default false.
	 *     @type bool   $submit_button Whether to include submit button. Default true.
	 *     @type string $submit_text   Custom submit button text.
	 * }
	 * @return string Rendered HTML
	 */
	public static function formSectionBox(string $title, string $content, array $options = []): string
	{
		return self::render('form-section-box', array_merge([
			'title' => $title,
			'content' => $content,
		], $options));
	}

	/**
	 * Render a conditional field group component.
	 *
	 * @param string $id           Unique identifier for the group
	 * @param string $trigger_name Name attribute of the trigger checkbox
	 * @param string $content      Inner HTML content (fields to conditionally show)
	 * @param bool   $invert       Invert the logic (hide when checked). Default false.
	 * @return string Rendered HTML
	 */
	public static function conditionalFieldGroup(string $id, string $trigger_name, string $content, bool $invert = false): string
	{
		return self::render('conditional-field-group', [
			'id' => $id,
			'trigger_name' => $trigger_name,
			'content' => $content,
			'invert' => $invert,
		]);
	}
}
