<?php

namespace BringFraktguidenPro\PickUpPoint;

class PickUpPointsModalComponent {

	public function __construct() {
	}

	public function render(): string {
		$instructions = esc_html__('Please choose a pick up point from the list below','bring-fraktguiden-for-woocommerce');
		return <<<HTML

<div class="bring-fraktguiden-pick-up-points-modal" style="display:none">
	<div
		class="bfg-pupm__template"
		tabindex="0"
	>
		<div class="bfg-pupm__name"></div>
		<div class="bfg-pupm__address"></div>
	</div>
	<div class="bfg-pupm__wrap">
		<div class="bfg-pupm__inner">
			<div class="bfg-pupm__header">
				<div class="bfg-pupm__instruction">$instructions</div>
				<div class="bfg-pupm__close" tabindex="0">&times;</div>
			</div>
			<div class="bfg-pupm__list"></div>
		</div>
	</div>
</div>
HTML;
	}
}
