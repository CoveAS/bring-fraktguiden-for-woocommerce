<?php

namespace BringFraktguidenPro\Booking\Box;

use WC_Order;
use WP_Bring_Response;

/**
 * Every booking attempt an order has made, oldest first.
 *
 * An order can be booked more than once, because a consignment can fail, go to
 * the wrong Mybring account, or be cancelled in Mybring. The plugin cancels
 * nothing at Bring. A shop worker does that in Mybring.
 */
class BookingHistory
{
	public const META_KEY = '_bring_booking_responses';

	/**
	 * The key the plugin wrote before it kept a history.
	 *
	 * The newest attempt still writes here, so the label download, the orders
	 * list column and the debug screen keep reading one response.
	 */
	public const LEGACY_META_KEY = '_bring_booking_response';

	/**
	 * @return BookingRecord[]
	 */
	public static function all(WC_Order $order): array
	{
		$stored = $order->get_meta(self::META_KEY);

		if (is_array($stored) && $stored) {
			return array_values(array_map(
				fn(array $entry) => BookingRecord::from_array($entry, $order->get_id()),
				array_filter($stored, 'is_array')
			));
		}

		$legacy = $order->get_meta(self::LEGACY_META_KEY);

		if (!is_array($legacy) || !$legacy) {
			return [];
		}

		// An order booked before the history existed has one attempt, and
		// nothing recorded when or who.
		return [new BookingRecord(response: $legacy, order_id: $order->get_id())];
	}

	/**
	 * @return BookingRecord[] Newest first.
	 */
	public static function newest_first(WC_Order $order): array
	{
		return array_reverse(self::all($order));
	}

	public static function latest(WC_Order $order): ?BookingRecord
	{
		$records = self::all($order);

		return $records ? end($records) : null;
	}

	public static function append(WC_Order $order, WP_Bring_Response $response, BookingForm $form): BookingRecord
	{
		$stored = $order->get_meta(self::META_KEY);
		$stored = is_array($stored) ? $stored : [];

		$entry = [
			'response'  => $response->to_array(),
			'form'      => $form->to_array(),
			'booked_at' => gmdate('Y-m-d H:i:s'),
			'booked_by' => wp_get_current_user()->display_name,
		];

		$stored[] = $entry;

		$order->update_meta_data(self::META_KEY, $stored);
		$order->update_meta_data(self::LEGACY_META_KEY, $entry['response']);
		$order->save();

		return BookingRecord::from_array($entry, $order->get_id());
	}
}
