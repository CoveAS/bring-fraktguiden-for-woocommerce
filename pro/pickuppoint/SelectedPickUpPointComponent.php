<?php

namespace BringFraktguidenPro\PickUpPoint;

/**
 * The host tag of the pick up point picker.
 *
 * The custom element in pick-up-point-checkout.js builds the content in a
 * shadow root, so this writes an empty tag.
 */
class SelectedPickUpPointComponent {

	public function __construct( public int $number, public bool $hidden = false ) {
	}

	public function render(): string {
		$hidden = $this->hidden ? 'style="display:none"' : '';
		return <<<HTML
<bring-fraktguiden-pick-up-point-picker data-max="{$this->number}" {$hidden}></bring-fraktguiden-pick-up-point-picker>
HTML;
	}
}
