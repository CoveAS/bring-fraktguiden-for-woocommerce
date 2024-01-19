<?php

namespace BringFraktguiden\Traits;

trait HasInstance
{
	private static self $instance;

	public static function instance(): self
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
