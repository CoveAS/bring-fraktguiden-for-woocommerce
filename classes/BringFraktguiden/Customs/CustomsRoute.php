<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WC_Order;

/**
 * Which customs rule covers a booking.
 *
 * NVIT covers goods that pass through Sweden or Finland on the way to another
 * place in Norway. An export covers goods that leave Norway. A booking answers
 * to one rule at most. See doc/nvit.md and doc/export.md.
 */
class CustomsRoute
{
	public const NVIT = 'nvit';

	public const EXPORT = 'export';

	/**
	 * Return the rule that covers the booking, or an empty string.
	 *
	 * @param WC_Order $order   The order the booking ships.
	 * @param string   $product The Bring product, for example 5800 or BUSINESS_PARCEL.
	 */
	public static function for_order(WC_Order $order, string $product): string
	{
		$from_postcode = (string) Fraktguiden_Helper::get_option('booking_address_postcode');
		$from_country  = (string) Fraktguiden_Helper::get_option('booking_address_country');

		if (ExportRule::requires_customs_data($from_country, $order->get_shipping_country(), $product)) {
			return self::EXPORT;
		}

		if (NvitRule::requires_transit_data($from_postcode, $order->get_shipping_postcode(), $product)) {
			return self::NVIT;
		}

		return '';
	}
}
