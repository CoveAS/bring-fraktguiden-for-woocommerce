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

		echo '<div class="bfg-grid">';
		foreach ($section['fields'] as $key => $field) {
			printf('<span>%s</span>',$field['title']);
			FieldRenderer::{$key}();
		}
		echo '</div>';
	}

}
