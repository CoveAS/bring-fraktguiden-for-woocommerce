<?php

namespace BringFraktguidenPro\Booking\Box;

use WC_Order;

/**
 * The booking form a shop worker has not sent yet.
 *
 * The draft holds what the worker last typed. It never goes stale on its own,
 * because only the worker knows whether the edit still applies. The Reset
 * button clears it and fills the form from the order again.
 */
class BookingDraft
{
	public const META_KEY = '_bring_booking_draft';

	public static function read(WC_Order $order): ?BookingForm
	{
		$stored = $order->get_meta(self::META_KEY);

		if (!is_array($stored) || !$stored) {
			return null;
		}

		return BookingForm::from_array($stored);
	}

	public static function write(WC_Order $order, BookingForm $form): void
	{
		$order->update_meta_data(self::META_KEY, $form->to_array());
		$order->save();
	}

	public static function clear(WC_Order $order): void
	{
		$order->delete_meta_data(self::META_KEY);
		$order->save();
	}
}
