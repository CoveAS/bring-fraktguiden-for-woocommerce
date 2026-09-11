<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * What a shop must fix before Bring accepts a customs booking.
 *
 * The warning holds the problems. It never stops a booking. Bring holds the
 * real guard, and answers with the reason when it refuses.
 *
 * CustomsRoute says which rule applies.
 */
class CustomsWarning
{
	/**
	 * @param string                                      $reason        A CustomsRoute constant.
	 * @param array<int, array{name: string, messages: array<int, string>}> $lines
	 * @param array<int, string>                          $shop_messages
	 */
	private function __construct(
		public readonly string $reason,
		public readonly array $lines,
		public readonly array $shop_messages,
	) {
	}

	/**
	 * Return the warning of an order, or null when the shop has nothing to fix.
	 *
	 * @param WC_Order $order   The order the booking ships.
	 * @param string   $product The Bring product, for example BUSINESS_PARCEL.
	 */
	public static function for_order(WC_Order $order, string $product): ?self
	{
		$reason = CustomsRoute::for_order($order, $product);

		if (!$reason) {
			return null;
		}

		$lines         = self::lines($order);
		$shop_messages = self::shop_messages($reason);

		if (!$lines && !$shop_messages) {
			return null;
		}

		return new self($reason, $lines, $shop_messages);
	}

	/**
	 * Return the problems of the order lines, with the name of each line.
	 *
	 * @return array<int, array{name: string, messages: array<int, string>}>
	 */
	private static function lines(WC_Order $order): array
	{
		$items = CustomsDeclaration::items($order);
		$lines = [];

		foreach (CustomsCheck::problems($order) as $id => $problems) {
			$lines[] = [
				'name'     => $items[$id]->get_name(),
				'messages' => array_map(fn(CustomsProblem $problem) => $problem->message(), $problems),
			];
		}

		return $lines;
	}

	/**
	 * Return the problems of the shop settings.
	 *
	 * @param string $route A CustomsRoute constant.
	 *
	 * @return array<int, string>
	 */
	private static function shop_messages(string $route): array
	{
		return array_map(
			fn(ShopProblem $problem) => $problem->message(),
			ShopCheck::problems($route)
		);
	}
}
