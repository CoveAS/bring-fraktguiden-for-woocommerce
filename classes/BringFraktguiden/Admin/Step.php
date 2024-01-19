<?php

namespace BringFraktguiden\Admin;

class Step
{
	public function __construct(
		public readonly string $label,
		public readonly string $description,
		public readonly string $action,
		public readonly string $actionText,
		public readonly bool $completed,
	)
	{
	}
}
