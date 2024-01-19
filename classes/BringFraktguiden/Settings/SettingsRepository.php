<?php

namespace BringFraktguiden\Settings;

use BringFraktguiden\Traits\HasUniqueInstances;

class SettingsRepository
{
	use HasUniqueInstances;

	private array $settings;

	private function __construct(public readonly string $option)
	{
		$this->load();
	}

	public function load(): SettingsRepository
	{
		$settings = get_option($this->option, []);
		$this->settings = [];
		foreach ($settings as $settingName => $value) {
			$this->settings[$settingName] = new Setting($settingName, $value);
		}
		return $this;
	}

	public function get(string $key): ?Setting
	{
		if (!isset($this->settings[$key])) {
			return null;
		}
		return $this->settings[$key];
	}

	public function set(string $key, Setting $setting): self
	{
		$this->settings[$key] = $setting;
		return $this;
	}

	public function all(): array
	{
		return $this->settings;
	}

	public function save(): bool
	{
		$options = [];
		foreach ($this->settings as $setting) {
			$options[$setting->key] = $setting->value;
		}
		return update_option($this->option, $options);
	}
}
