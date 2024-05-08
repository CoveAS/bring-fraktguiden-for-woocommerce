<?php

namespace BringFraktguiden\Fields;

use BringFraktguiden\Settings\Settings;

class Field
{
	public function __construct(protected string $name, protected array $field)
	{
		$this->field = wp_parse_args(
			$field,
			[
				'title' => '',
				'type' => 'text',
				'label' => '',
				'desc_tip' => '',
				'description' => '',
				'default' => '',
				'placeholder' => '',
				'css' => '',
				'dependencies' => [],
				'custom_attributes' => [],
				'options' => []
			]
		);
		unset($this->field['name']);
		unset($this->field['template']);
		unset($this->field['field']);
	}

	public function render(): string
	{
		return sprintf(
			'<div class="%s">%s</div>%s',
			'bfg-input bfg-input--'. $this->field['type'],
			$this->field(),
			$this->description(),
		);
	}
	public function field(): string
	{
		extract($this->field);
		$name = $this->name;
		$value = Settings::instance()->{$name}->raw_value;
		$template = $this->get_template();
		ob_start();
		require dirname(__DIR__, 3) . '/templates/admin/fields/' . $template . '.php';
		return ob_get_clean();
	}
	public function title(): string
	{
		return esc_html($this->field['title']);
	}

	public function description(): string
	{
		$text = [
			$this->field['description'],
			$this->field['desc_tip'],
		];
		$content = trim(implode(' ', $text));
		if (! $content) {
			return '';
		}
		return sprintf(
			'<p class="%s">%s</p>',
			'bfg-description',
			wp_kses_post($content)
		);
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

	private function get_template(): string
	{
		return match ($this->field['type']) {
			'date',
			'time',
			'number',
			'text' => 'input',
			'checkbox' => 'checkbox',
			'select' => 'select',
			'info' => 'info',
		};
	}

	public function label(): string
	{
		return sprintf(
			'<label for="%s">%s</label>',
			$this->name,
			$this->title(),
		);
	}

	public function __toString(): string
	{
		return $this->render();
	}
}
