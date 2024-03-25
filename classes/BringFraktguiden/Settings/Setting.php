<?php

namespace BringFraktguiden\Settings;

use BringFraktguiden\Utility\Config;

class Setting
{
	readonly public array $data;
	readonly public string $type;

	public function __construct(
		readonly public string            $key,
		readonly public bool|string|array $value,
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
		$this->data = $data;
		if (!$data['type'])
			ray($this->key, $key, $this->value, $data);
		$this->type = $data['type'];
	}

	public function validate(mixed $param): array
	{
		$errors = [];
		$type   = $this->data['type'];
		$required   = ! empty($this->data['required']);
		if ($required && ! $param) {
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
		if ($type === 'time' && ! preg_match('/^\d{2}:\d{2}$/', $param)) {
			$errors[] = __(
				'Value must follow the format ##:##',
				'bring-fraktguiden-for-woocommerce'
			);
		}
		return $errors;
	}

	public function sanitize(mixed $param): mixed
	{
		return match ($this->type) {
			'time',
			'select' => $param,
			'text' => wp_kses_post($param),
			'checkbox' => !empty($param),
			'number' => $param === '' ? '' : floatval($param),
			default => throw new \Exception($this->data['type']),
		};
	}
}
