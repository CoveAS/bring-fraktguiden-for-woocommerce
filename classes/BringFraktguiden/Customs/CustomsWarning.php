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
	 * @param array<int, string>                          $reasons       CustomsRoute constants.
	 * @param array<int, array{name: string, url: ?string, messages: array<int, string>}> $lines
	 * @param array<int, string>                          $shop_messages
	 */
	private function __construct(
		public readonly array $reasons,
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

		$lines         = self::lines($order, $reason);
		$shop_messages = self::shop_messages($reason);

		if (!$lines && !$shop_messages) {
			return null;
		}

		return new self([$reason], $lines, $shop_messages);
	}

	/**
	 * Return one warning for several orders.
	 *
	 * The bulk booking modal shows one banner for the whole selection. Two
	 * orders that hold the same product carry the same line problems, so the
	 * warning names each product once. A shop settings problem is the same for
	 * every order, so it also appears once.
	 *
	 * @param WC_Order[] $orders
	 * @param callable(WC_Order): string $service The Bring product of an order.
	 */
	public static function for_orders(array $orders, callable $service): ?self
	{
		$reasons       = [];
		$lines         = [];
		$shop_messages = [];

		foreach ($orders as $order) {
			$warning = self::for_order($order, $service($order));

			if (!$warning) {
				continue;
			}

			$reasons       = array_merge($reasons, $warning->reasons);
			$shop_messages = array_merge($shop_messages, $warning->shop_messages);

			foreach ($warning->lines as $line) {
				$lines[$line['name']]['name']     = $line['name'];
				$lines[$line['name']]['url']      = $line['url'] ?? $lines[$line['name']]['url'] ?? null;
				$lines[$line['name']]['messages'] = array_unique(
					array_merge($lines[$line['name']]['messages'] ?? [], $line['messages'])
				);
			}
		}

		if (!$lines && !$shop_messages) {
			return null;
		}

		return new self(
			array_values(array_unique($reasons)),
			array_values($lines),
			array_values(array_unique($shop_messages)),
		);
	}

	/**
	 * Return the problems of the order lines, with the name of each line.
	 *
	 * A missing HS code is left out. The booking box holds a field for every
	 * product, so the shop worker reads the missing code there.
	 *
	 * Each line carries the edit link of its product, so the shop worker
	 * reaches the screen that holds the weight and the country of origin. The
	 * link is null when the product is gone, or when the user may not edit it.
	 *
	 * @param string $route A CustomsRoute constant.
	 *
	 * @return array<int, array{name: string, url: ?string, messages: array<int, string>}>
	 */
	private static function lines(WC_Order $order, string $route): array
	{
		$items = CustomsDeclaration::items($order);
		$lines = [];

		foreach (CustomsCheck::problems($order, $route) as $id => $problems) {
			$problems = array_filter(
				$problems,
				fn(CustomsProblem $problem) => CustomsProblem::MissingHsCode !== $problem
			);

			if (!$problems) {
				continue;
			}

			$lines[] = [
				'name'     => $items[$id]->get_name(),
				'url'      => get_edit_post_link($items[$id]->get_product_id(), 'url'),
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
