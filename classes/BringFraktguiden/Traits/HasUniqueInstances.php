<?php

namespace BringFraktguiden\Traits;

trait HasUniqueInstances
{
	private static array $instances;

	public static function instance(string $key): self
	{
		if (!isset(self::$instances[$key])) {
			self::$instances[$key] = new self($key);
		}
		return self::$instances[$key];

	}
}
