<?php

namespace BringFraktguiden\Settings;

class Setting
{
	public function __construct(
		readonly public string $key,
		readonly public bool|string|array $value,
	)
	{
	}
}
