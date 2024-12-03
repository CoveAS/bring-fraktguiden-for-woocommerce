<?php

namespace BringFraktguiden\Settings;

class InvalidSetting extends \Exception
{
    public function __construct($key)
    {
		parent::__construct("Invalid setting: " . $key);
    }
}
