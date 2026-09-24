<?php

namespace BringFraktguiden\Booking;

use BringFraktguiden\Settings\Settings;
use WC_Order;

/**
 * The order statuses that allow a booking.
 *
 * The shop owner chooses them on the booking page. The setting holds the keys
 * as WooCommerce lists them, with the wc- prefix, and has_status() reads them
 * without it.
 */
class BookableStatus
{
	public static function allows(WC_Order $order): bool
	{
		$statuses = (array) Settings::instance()->booking_order_statuses->value;

		return $order->has_status(preg_replace('/^wc-/', '', $statuses));
	}
}
