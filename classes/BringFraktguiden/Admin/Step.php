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
		/** The id of a dialog the action button opens, instead of a link. */
		public readonly ?string $dialog = null,
		/** The id of a form the step shows in the card, instead of a button. */
		public readonly ?string $form = null,
	)
	{
	}
}
