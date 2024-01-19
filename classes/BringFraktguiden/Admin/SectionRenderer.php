<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Utility\Config;

class SectionRenderer
{

	public static function __callStatic(string $name, array $arguments)
	{
		$admin_settings = Config::get('admin-settings');
		if (! isset($admin_settings[$name])) {
			return;
		}


		$section = $admin_settings[$name];
		if (isset($section['description'])) {
			printf('<p>%s</p>', $section['description']);
		}

		foreach ($section['fields'] as $key => $field) {
			FieldRenderer::{$key}();
		}
	}

}
