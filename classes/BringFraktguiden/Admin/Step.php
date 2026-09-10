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
		/** The action changes data, so it needs a form button, not a link. */
		public readonly bool $actionIsWrite = false,
	)
	{
	}
}
