<?php

namespace BringFraktguiden\Utility;

class Config
{
	static array $config = [];

	public static function get($name): array
	{
		if (! isset(self::$config[$name])) {
			$data = require dirname(__DIR__, 3). '/config/' . $name . '.php';
			if (! WC()->countries) {
				return $data;
			}
			self::$config[$name] = $data;
		}
		return self::$config[$name];
	}
}
