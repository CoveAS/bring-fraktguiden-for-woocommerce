<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Fields\Field;
use BringFraktguiden\Settings\Attributes\Checkbox;
use BringFraktguiden\Settings\Setting;
use BringFraktguiden\Settings\Settings;
use BringFraktguiden\Utility\Config;

/**
 * @method static pro_enabled()
 * @method static test_url()
 * @method static language()
 * @method static post_office()
 * @method static from_zip()
 * @method static from_country()
 * @method static handling_fee()
 * @method static calculate_by_weight()
 * @method static enable_multipack()
 * @method static shipping_options_full_width()
 * @method static display_desc()
 * @method static use_customer_number_to_get_prices()
 * @method static price_to_use()
 * @method static service_sorting()
 * @method static mybring_api_uid()
 * @method static mybring_api_key()
 * @method static mybring_customer_number()
 * @method static no_connection_flat_rate_label()
 * @method static no_connection_flat_rate()
 * @method static no_connection_rate_id()
 * @method static exception_flat_rate_label()
 * @method static exception_flat_rate()
 * @method static exception_rate_id()
 * @method static max_products()
 * @method static alt_flat_rate_label()
 * @method static alt_flat_rate()
 * @method static alt_flat_rate_id()
 * @method static debug()
 * @method static disable_stylesheet()
 * @method static lead_time()
 * @method static lead_time_cutoff()
 * @method static display_eta()
 * @method static pickup_point_types()
 * @method static pickup_point_style()
 * @method static pickup_point_map()
 * @method static system_information()
 * @method static minimum_length()
 * @method static minimum_width()
 * @method static minimum_height()
 * @method static minimum_weight()
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
			echo (new Field($name, $field))->render();
			break;
		}
	}

}
