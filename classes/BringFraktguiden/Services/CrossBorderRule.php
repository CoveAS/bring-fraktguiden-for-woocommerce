<?php

namespace BringFraktguiden\Services;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Whether a service carries goods from one country to another.
 *
 * Bring sells some services inside one country only. A booking that leaves the
 * country on such a service fails. The service rows carry the trait as
 * 'cross_border' => false in config/services.php.
 *
 * A service without the flag crosses a border. An unknown service therefore
 * raises no warning, because a wrong warning stops a booking a shop can make.
 */
class CrossBorderRule
{
	/**
	 * Return whether the service carries the shipment.
	 *
	 * A shipment inside one country is always carried.
	 *
	 * @param string $from    The country code of the sender.
	 * @param string $to      The country code of the recipient.
	 * @param string $product The Bring product, for example BUSINESS_PARCEL.
	 */
	public static function allows(string $from, string $to, string $product): bool
	{
		$from = strtoupper($from);
		$to   = strtoupper($to);

		if ('' === $from || '' === $to || $from === $to) {
			return true;
		}

		$service = Fraktguiden_Helper::get_service_data_for_key($product);

		return $service['cross_border'] ?? true;
	}
}
