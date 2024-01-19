<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Settings\Settings;
use BringFraktguiden\Utility\Config;

/**
 * @method static pro_enabled()
 * @method static test_mode()
 */
class FieldRenderer
{

	public static function __callStatic(string $name, array $arguments)
	{
		$admin_settings = Config::get('admin-settings');
		foreach ($admin_settings as $section) {
			if (!isset($section['fields'][$name])) {
				continue;
			}
			$field = $section['fields'][$name];
			self::render($name, $field);
			break;
		}
	}

	public static function render(string $name, array $field): void
	{
		$field = wp_parse_args(
			$field,
			[
				'title' => '',
				'type' => 'text',
				'label' => '',
				'desc_tip' => '',
				'default' => '',
				'placeholder' => '',
				'css' => '',
				'custom_attributes' => [],
				'options' => []
			]
		);
		switch ($field['type']) {
			case 'checkbox':
				self::template($name,'checkbox', $field);
				break;
			case 'select':
				self::template($name, 'select', $field);
				break;
			case 'info':
				self::template($name, 'info', $field);
				break;
			case 'text':
			case 'time':
			case 'number':
				self::template($name, 'input', $field);
				break;
		}
	}

	private static function template(string $name, string $template, array $field): void
	{
		unset($field['name']);
		unset($field['template']);
		unset($field['field']);
		extract($field);
		$value = Settings::instance()->{$name}->value;
		require dirname(__DIR__, 3) . '/templates/admin/fields/' . $template . '.php';
	}

	public static function attributes(array $attributes): void
	{
		foreach ($attributes as $attribute => $value) {
			printf(
				' %s="%s" ',
				esc_attr($attribute),
				esc_attr($value)
			);
		}
	}
}
