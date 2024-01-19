<?php

namespace BringFraktguiden\Settings;

use Attribute;

#[Attribute] class PropertyDefaultValue
{
	public function __construct(public readonly bool|string|array $value = false)
	{
	}
}
