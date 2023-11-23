<?php

namespace BringFraktguidenPro\PickUpPoint;

class SelectedPickUpPointComponent {

	public function __construct( public int $number, public bool $hidden = false ) {
	}

	public function render(): string {
		$change = esc_html__('Change', 'bring-fraktguiden-for-woocommerce');
		$map_label  = esc_html__( 'View on map', 'bring-fraktguiden-for-woocommerce' );
		$hidden = $this->hidden ? 'style="display:none"' : '';
		return <<<HTML
<div class="bring-fraktguiden-pick-up-point-picker" data-max="{$this->number}" {$hidden}>
	<div class="bfg-pup__change">{$change}</div>
	<div class="bfg-pup__name"></div>
	<div class="bfg-pup__address"></div>
	<div class="bfg-pup__opening-hours"></div>
	<div class="bfg-pup__description"></div>
	<a href="#" target="_blank" class="bfg-pup__map">{$map_label}</a>
</div>
HTML;
	}
}
