<?php

namespace BringFraktguiden\Customs;

use WC_Product;

/**
 * The switch that lets a variation carry its own customs data.
 *
 * A variation inherits the customs data of its parent. A shop that sells
 * variations of different goods turns the switch on, and then the variation
 * fields apply.
 */
class Override
{
	/**
	 * The switch, on a variation.
	 */
	public const META = '_bring_customs_override';

	/**
	 * Return whether the variation carries its own customs data.
	 */
	public static function is_on(WC_Product $variation): bool
	{
		return 'yes' === $variation->get_meta(self::META);
	}
}
