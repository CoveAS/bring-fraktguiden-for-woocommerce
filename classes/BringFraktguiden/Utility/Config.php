<?php

namespace BringFraktguiden\Utility;

class Config
{
	static array $config = [];

	public static function get($name): array
	{
		if (! isset(self::$config[$name])) {
			self::$config[$name]= require dirname(__DIR__, 3). '/config/' . $name . '.php';
		}
		return self::$config[$name];
	}
}
