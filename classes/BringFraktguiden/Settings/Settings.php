<?php

namespace BringFraktguiden\Settings;

use BringFraktguiden\Settings\Exceptions\SettingsPropertyException;
use BringFraktguiden\Traits\HasInstance;
use BringFraktguiden\Utility\Config;
use BringFraktguiden\Settings\Attributes\Checkbox;
use ReflectionClass;

class Settings
{
	use HasInstance;

	public Setting $plugin_settings;
	#[Checkbox]
	public Setting $pro_enabled;
	#[Checkbox]
	public Setting $test_mode;
	public Setting $language;
	#[Checkbox]
	public Setting $post_office;
	public Setting $from_zip;
	public Setting $from_country;
	public Setting $handling_fee;
	public Setting $general_options_title;
	#[Checkbox]
	public Setting $calculate_by_weight;
	#[Checkbox]
	public Setting $enable_multipack;
	#[Checkbox]
	public Setting $shipping_options_full_width;
	#[Checkbox]
	public Setting $display_desc;
	#[Checkbox]
	public Setting $use_customer_number_to_get_prices;
	public Setting $price_to_use;
	public Setting $service_sorting;
	public Setting $mybring_title;
	public Setting $mybring_api_uid;
	public Setting $mybring_api_key;
	public Setting $mybring_customer_number;
	public Setting $no_connection_title;
	public Setting $no_connection_handling;
	public Setting $no_connection_flat_rate_label;
	public Setting $no_connection_flat_rate;
	public Setting $no_connection_rate_id;
	public Setting $exceptions_title;
	public Setting $exception_handling;
	public Setting $exception_flat_rate_label;
	public Setting $exception_flat_rate;
	public Setting $exception_rate_id;
	public Setting $max_products_title;
	public Setting $alt_handling;
	public Setting $max_products;
	public Setting $alt_flat_rate_label;
	public Setting $alt_flat_rate;
	public Setting $alt_flat_rate_id;
	public Setting $advanced_settings;
	#[Checkbox]
	public Setting $debug;
	#[Checkbox]
	public Setting $disable_stylesheet;
	public Setting $lead_time;
	public Setting $lead_time_cutoff;
	public Setting $display_eta;
	public Setting $pickup_point_section;
	public Setting $pickup_point_types;
	public Setting $pickup_point_style;
	public Setting $pickup_point_map;
	public Setting $system_information;
	public Setting $fallback_options;
	public Setting $minimum_sizing_params;
	public Setting $minimum_length;
	public Setting $minimum_width;
	public Setting $minimum_height;
	public Setting $minimum_weight;

	/**
	 * @throws SettingsPropertyException
	 */
	protected function __construct()
	{
		$repository = SettingsRepository::instance('bring_fraktguiden_for_woocommerce_settings');
		$settings = $repository->all();

		$admin_settings = Config::get('admin-settings');

		$all_settings = [];
		foreach ($admin_settings as $section) {
			$all_settings = [
				...$all_settings,
				...array_keys($section['fields'])
			];
		}
		$reflection = new ReflectionClass($this);
		$properties = $reflection->getProperties();
		foreach ($properties as $property) {
			if ($property->getType()->getName() !== Setting::class) {
				continue;
			}
			$name = $property->name;
			if (! isset($settings[$name])) {
				$value = false;
				foreach ($admin_settings as $section) {
					if (isset($section['fields'][$name]['default'])) {
						$value = $section['fields'][$name]['default'];
						break;
					}
				}
				foreach ($property->getAttributes() as $attribute) {
					$instance = $attribute->newInstance();
					if ($instance instanceof Checkbox) {
						$value = $instance->process($value);
					}
				}
				$settings[$name] = new Setting($name, $value);
			}
			$this->{$name} = $settings[$name];
		}
//		echo '<pre>';
//		foreach ($all_settings as $name) {
//			echo "\tpublic Setting \$$name;\n";
//		}
//		echo '</pre>';
		foreach ($all_settings as $name) {
			if (! property_exists($this, $name)) {
				throw SettingsPropertyException::missing($name);
			}
		}

	}



}
