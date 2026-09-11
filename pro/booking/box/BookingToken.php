<?php

namespace BringFraktguidenPro\Booking\Box;

use WC_Order;

/**
 * A one use token that stops an order from being booked twice by accident.
 *
 * The box renders the token into the form. A booking spends it. A second send
 * of the same form carries a spent token, so the route refuses it. A reload of
 * the box issues a new token, which is how a shop worker books again on
 * purpose.
 */
class BookingToken
{
	public const META_KEY = '_bring_booking_token';

	/**
	 * Return the token of the open form, making one when none is open.
	 */
	public static function current(WC_Order $order): string
	{
		$token = (string) $order->get_meta(self::META_KEY);

		if ($token) {
			return $token;
		}

		$token = wp_generate_uuid4();
		$order->update_meta_data(self::META_KEY, $token);
		$order->save();

		return $token;
	}

	/**
	 * Spend the token. A wrong or spent token returns false.
	 */
	public static function spend(WC_Order $order, string $token): bool
	{
		if (!$token || $token !== (string) $order->get_meta(self::META_KEY)) {
			return false;
		}

		$order->delete_meta_data(self::META_KEY);
		$order->save();

		return true;
	}
}
