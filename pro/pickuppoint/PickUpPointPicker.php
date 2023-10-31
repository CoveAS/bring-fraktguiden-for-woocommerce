<?php

namespace BringFraktguidenPro\PickUpPoint;

class PickUpPointPicker {

	public function __construct( public int $number ) {
	}

	public function render(): string {
		$change = esc_html__('Change', 'bring-fraktguiden-for-woocommerce');
		return <<<HTML

<div class="bring-fraktguiden-pick-up-point-picker" data-max="{$this->number}">
	<div class="bfg-pup__name"></div>
	<div class="bfg-pup__change">{$change}</div>
	<div class="bfg-pup__address"></div>
	<div class="bfg-pup__opening-hours"></div>
	<div class="bfg-pup__description"></div>
</div>
HTML;
	}
}
