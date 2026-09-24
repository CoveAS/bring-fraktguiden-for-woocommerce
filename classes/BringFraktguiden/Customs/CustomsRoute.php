<?php

namespace BringFraktguiden\Customs;

use BringFraktguiden\Booking\SenderAddress;
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
		$address       = SenderAddress::get();
		$from_postcode = $address['booking_address_postcode'];
		$from_country  = $address['booking_address_country'];

		if (ExportRule::requires_customs_data($from_country, $order->get_shipping_country(), $product)) {
			return self::EXPORT;
		}

		$is_nvit = NvitRule::requires_transit_data(
			from_country: $from_country,
			from_postcode: $from_postcode,
			to_country: $order->get_shipping_country(),
			to_postcode: $order->get_shipping_postcode(),
			product: $product,
		);

		if ($is_nvit) {
			return self::NVIT;
		}

		return '';
	}
}
