<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Whether the shop signs the customs declaration of its shipments.
 *
 * The shop confirms once, in the booking settings, that the customs data is
 * correct and that the goods are neither dangerous nor prohibited. Bring prints
 * the confirmation as a signature on the declaration. See doc/export.md.
 *
 * The confirmation says how the shop works, so it is a setting and not a tick
 * on each order.
 */
class CustomsConsent
{
	/**
	 * Return whether the shop has confirmed.
	 */
	public static function given(): bool
	{
		return 'yes' === Fraktguiden_Helper::get_option('customs_consent');
	}
}
