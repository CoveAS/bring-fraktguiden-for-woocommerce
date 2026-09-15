<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Whether the shop signs the customs declaration of its shipments.
 *
 * The shop confirms once that the customs data is correct and that the goods
 * are not dangerous and not prohibited. Bring uses the confirmation as a
 * signature on the declaration. See doc/export.md.
 *
 * The confirmation says how the shop works, so it is a setting and not a tick
 * on each order. One signature covers every Bring booking of the shop.
 *
 * The booking settings page holds the same setting as a checkbox.
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

	/**
	 * Record the confirmation of the shop.
	 */
	public static function sign(): void
	{
		Fraktguiden_Helper::update_option('customs_consent', 'yes');
	}
}
