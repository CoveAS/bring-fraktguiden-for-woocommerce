<?php

namespace BringFraktguiden\Settings\Exceptions;

use Exception;

class SettingsPropertyException extends Exception
{

	public static function missing(string $name): SettingsPropertyException
	{
		return new self("Missing $name property");
	}
}
