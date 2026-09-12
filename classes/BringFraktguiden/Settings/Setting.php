<?php

namespace BringFraktguiden\Settings;

use BringFraktguiden\Utility\Config;

class Setting
{
	readonly public array  $data;
	readonly public string $type;
	readonly public mixed  $value;
	// The text the input box shows and the option keeps. An empty number field holds an empty
	// string, so the placeholder tells the shop owner what an empty box means.
	readonly public mixed  $entered;

	public function __construct(
		readonly public string $key,
		readonly public mixed  $raw_value,
	)
	{
		$admin_settings = Config::get('admin-settings');
		$data           = [];
		foreach ($admin_settings as $page) {
			foreach ($page['fields'] as $key => $fieldData) {
				if ($key !== $this->key) {
					continue;
				}
				$data = $fieldData;
				break 2;
			}
		}
		if (empty($data)) {
			throw new InvalidSetting($this->key);
		}
		$this->data = $data;
		$this->type = $data['type'];
		// An empty number box means the field default, which the placeholder prints.
		$empty_number = $this->type === 'number' && (string) $raw_value === '';
		$this->value = $this->sanitize($empty_number ? ($data['default'] ?? '') : $raw_value);
		$this->entered = $empty_number ? '' : $this->value;
	}

	public function validate(mixed $param): array
	{
		$errors   = [];
		$type     = $this->data['type'];
		$required = !empty($this->data['required']);
		if ($required && !$param) {
			$errors[] = __(
				'Value is required!',
				'bring-fraktguiden-for-woocommerce'
			);
		}
		if ($type === 'select' && !key_exists($param, $this->data['options'])) {
			$errors[] = __(
				'Selected value must be one of the available options!',
				'bring-fraktguiden-for-woocommerce'
			);
		}
		if ($type === 'number' && !(ctype_digit($param) || is_numeric($param))) {
			$errors[] = __(
				'Value must be a number',
				'bring-fraktguiden-for-woocommerce'
			);
		}
		if ($type === 'time' && !preg_match('/^\d{2}:\d{2}$/', $param)) {
			$errors[] = __(
				'Value must follow the format ##:##',
				'bring-fraktguiden-for-woocommerce'
			);
		}
		return $errors;
	}

	// The step of the box says how fine the number is. A whole step means a whole number.
	private function number(mixed $param): int|float
	{
		$value = floatval($param);
		return ($this->data['custom_attributes']['step'] ?? '') === '1'
			? (int) round($value)
			: $value;
	}

	/**
	 * @throws \Exception
	 */
	public function sanitize(mixed $param): mixed
	{
		return match ($this->type) {
			'time',
			'select' => $param,
			'info',
			'url' => esc_url($param),
			'email' => sanitize_email($param),
			'tel',
			// A text setting holds plain text, so it keeps no HTML.
			'text' => sanitize_text_field($param),
			'checkbox' => filter_var($param, FILTER_VALIDATE_BOOL),
			'number' => $this->number($param),
			default => throw new \Exception("Unknown data type: " . $this->data['type']),
		};
	}
}
