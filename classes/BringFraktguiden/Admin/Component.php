<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Fields\Field;

/**
 * Component renderer for reusable admin UI components.
 *
 * Usage:
 *   echo Component::render('box-header', ['title' => 'My Title', 'description' => 'My description']);
 *   echo Component::boxHeader('My Title', 'My description');
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
	 * Render a box header component.
	 *
	 * @param string $title       Section title
	 * @param string $description Optional description text
	 * @param bool   $divider     Whether to show top divider
	 * @return string Rendered HTML
	 */
	public static function boxHeader(string $title, string $description = '', bool $divider = false): string
	{
		return self::render('box-header', [
			'title' => $title,
			'description' => $description,
			'divider' => $divider,
		]);
	}

	/**
	 * Render a box section wrapper component.
	 *
	 * @param string $content Inner HTML content
	 * @return string Rendered HTML
	 */
	public static function boxSection(string $content): string
	{
		return self::render('box-section', [
			'content' => $content,
		]);
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
	 * Render a field wrapper component.
	 *
	 * @param string $content Inner HTML content (label + field)
	 * @param string $class   Additional CSS classes
	 * @return string Rendered HTML
	 */
	public static function fieldWrapper(string $content, string $class = ''): string
	{
		return self::render('field-wrapper', [
			'content' => $content,
			'class' => $class,
		]);
	}

	/**
	 * Render a checkbox box component (checkbox in a card).
	 *
	 * @param Field $field The checkbox field object
	 * @return string Rendered HTML
	 */
	public static function checkboxBox(Field $field): string
	{
		return self::render('checkbox-box', [
			'field' => $field,
		]);
	}

	/**
	 * Render a badge component.
	 *
	 * @param string $text  Badge text
	 * @param string $type  Badge type: 'completed', 'in-progress', 'default'
	 * @return string Rendered HTML
	 */
	public static function badge(string $text, string $type = 'default'): string
	{
		return self::render('badge', [
			'text' => $text,
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
}
