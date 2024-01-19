<?php

namespace BringFraktguiden\Settings\Attributes;

use Attribute;

#[Attribute] class Checkbox
{
	public function process(mixed $value)
	{
		return filter_var($value, FILTER_VALIDATE_BOOL);
	}
}
