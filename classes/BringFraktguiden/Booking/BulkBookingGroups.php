<?php

namespace BringFraktguiden\Booking;

use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use WC_Order;

/**
 * The four groups a bulk booking selection falls into.
 *
 * The booking leaves an order that already holds a booking alone, and books
 * nothing for an order in a status that allows no booking, or an order
 * without a Bring shipping line. Both keep their place in
 * the label print, so the modal names every group before the worker sends the
 * request.
 */
class BulkBookingGroups
{
	/** The order gets a booking now. */
	public const BOOK = 'book';

	/** The order already holds a booking. */
	public const BOOKED = 'booked';

	/** The order status allows no booking. */
	public const STATUS = 'status';

	/** The order gets no booking. */
	public const SKIPPED = 'skipped';

	/**
	 * Sort every order of the selection into its group.
	 *
	 * @param int[] $order_ids
	 *
	 * @return array<string, WC_Order[]>
	 */
	public static function of(array $order_ids): array
	{
		$groups = [self::BOOK => [], self::BOOKED => [], self::STATUS => [], self::SKIPPED => []];

		foreach ($order_ids as $order_id) {
			$order = wc_get_order((int) $order_id);

			if (!$order instanceof WC_Order) {
				continue;
			}

			$groups[self::of_order($order)][] = $order;
		}

		return $groups;
	}

	/**
	 * Return the group of one order.
	 */
	public static function of_order(WC_Order $order): string
	{
		$adapter = new Bring_WC_Order_Adapter($order);

		if ($adapter->has_booking_consignments()) {
			return self::BOOKED;
		}

		if (!BookableStatus::allows($order)) {
			return self::STATUS;
		}

		if (!$adapter->has_bring_shipping_methods()) {
			return self::SKIPPED;
		}

		return self::BOOK;
	}
}
