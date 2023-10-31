<?php

namespace BringFraktguidenPro\PickUpPoint;

class PickUpPointsModalComponent {

	public function __construct() {
	}

	public function render(): string {
		return <<<HTML

<div class="bring-fraktguiden-pick-up-points-modal" style="display:block">

	<div class="bfg-pupm__template">
		<div class="bfg-pupm__name"></div>
		<div class="bfg-pupm__address"></div>
	</div>
	<div class="bfg-pupm__list">
	</div>
</div>
HTML;
	}
}
