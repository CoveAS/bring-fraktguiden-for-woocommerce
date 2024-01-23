<?php

namespace BringFraktguiden\Fields;

use BringFraktguiden\Traits\HasInstance;
use BringFraktguiden\Utility\Config;

class Fields
{
	use HasInstance;
	public readonly Field $plugin_settings;
	public readonly Field $pro_enabled;
	public readonly Field $test_mode;
	public readonly Field $language;
	public readonly Field $post_office;
	public readonly Field $from_zip;
	public readonly Field $from_country;
	public readonly Field $handling_fee;
	public readonly Field $general_options_title;
	public readonly Field $calculate_by_weight;
	public readonly Field $enable_multipack;
	public readonly Field $shipping_options_full_width;
	public readonly Field $display_desc;
	public readonly Field $use_customer_number_to_get_prices;
	public readonly Field $price_to_use;
	public readonly Field $service_sorting;
	public readonly Field $mybring_title;
	public readonly Field $mybring_api_uid;
	public readonly Field $mybring_api_key;
	public readonly Field $mybring_customer_number;
	public readonly Field $no_connection_title;
	public readonly Field $no_connection_handling;
	public readonly Field $no_connection_flat_rate_label;
	public readonly Field $no_connection_flat_rate;
	public readonly Field $no_connection_rate_id;
	public readonly Field $exceptions_title;
	public readonly Field $exception_handling;
	public readonly Field $exception_flat_rate_label;
	public readonly Field $exception_flat_rate;
	public readonly Field $exception_rate_id;
	public readonly Field $max_products_title;
	public readonly Field $alt_handling;
	public readonly Field $max_products;
	public readonly Field $alt_flat_rate_label;
	public readonly Field $alt_flat_rate;
	public readonly Field $alt_flat_rate_id;
	public readonly Field $advanced_settings;
	public readonly Field $debug;
	public readonly Field $disable_stylesheet;
	public readonly Field $lead_time;
	public readonly Field $lead_time_cutoff;
	public readonly Field $display_eta;
	public readonly Field $pickup_point_section;
	public readonly Field $pickup_point_types;
	public readonly Field $pickup_point_style;
	public readonly Field $pickup_point_map;
	public readonly Field $system_information;
	public readonly Field $fallback_options;
	public readonly Field $minimum_sizing_params;
	public readonly Field $minimum_length;
	public readonly Field $minimum_width;
	public readonly Field $minimum_height;
	public readonly Field $minimum_weight;

	protected function __construct()
	{
		$admin_settings = Config::get('admin-settings');
		foreach ($admin_settings as $section) {
			foreach ($section['fields'] as $name => $field) {
				$this->{$name} = (new Field($name, $field));
			}
		}
	}
}
