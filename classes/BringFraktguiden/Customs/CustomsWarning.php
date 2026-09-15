<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * What a shop must fix before Bring accepts a customs booking.
 *
 * The warning holds the problems. It never stops a booking. Bring holds the
 * real guard, and answers with the reason when it refuses.
 *
 * The missing consent is the one exception. It carries its own flag, because
 * the warning shows it as a callout with a Sign button, and the booking waits
 * until the shop signs. See CustomsConsent.
 *
 * CustomsRoute says which rule applies.
 */
class CustomsWarning
{
	/**
	 * @param array<int, array{route: string, lines: array<int, array{name: string, url: ?string, messages: array<int, string>}>, shop_messages: array<int, string>}> $groups
	 */
	private function __construct(
		public readonly array $groups,
		public readonly bool $needs_consent,
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
		$route = CustomsRoute::for_order($order, $product);

		if (!$route) {
			return null;
		}

		$lines         = self::lines($order, $route);
		$shop_messages = self::shop_messages($route);
		$needs_consent = !CustomsConsent::given();

		if (!$lines && !$shop_messages && !$needs_consent) {
			return null;
		}

		return new self(
			[[
				'route'         => $route,
				'lines'         => $lines,
				'shop_messages' => $shop_messages,
			]],
			$needs_consent
		);
	}

	/**
	 * Return one warning for several orders.
	 *
	 * The bulk booking modal shows one banner for the whole selection. Each
	 * rule keeps its own group, because a rule asks for its own data, and the
	 * reader has to see which products and which settings it means.
	 *
	 * Two orders that hold the same product carry the same line problems, so a
	 * group names each product once. A shop settings problem is the same for
	 * every order of the group, so it also appears once.
	 *
	 * @param WC_Order[] $orders
	 * @param callable(WC_Order): string $service The Bring product of an order.
	 */
	public static function for_orders(array $orders, callable $service): ?self
	{
		$groups        = [];
		$needs_consent = false;

		foreach ($orders as $order) {
			$warning = self::for_order($order, $service($order));

			if (!$warning) {
				continue;
			}

			$needs_consent = $needs_consent || $warning->needs_consent;

			foreach ($warning->groups as $group) {
				$groups[$group['route']] = self::merge($groups[$group['route']] ?? null, $group);
			}
		}

		if (!$groups) {
			return null;
		}

		$sorted = [];

		// The export rule reads first, because it asks for the most.
		foreach ([CustomsRoute::EXPORT, CustomsRoute::NVIT] as $route) {
			if (isset($groups[$route])) {
				$sorted[] = $groups[$route];
			}
		}

		return new self($sorted, $needs_consent);
	}

	/**
	 * Fold a group into the group of the same rule.
	 *
	 * @param array{route: string, lines: array<int, array{name: string, url: ?string, messages: array<int, string>}>, shop_messages: array<int, string>}|null $into
	 * @param array{route: string, lines: array<int, array{name: string, url: ?string, messages: array<int, string>}>, shop_messages: array<int, string>}      $group
	 *
	 * @return array{route: string, lines: array<int, array{name: string, url: ?string, messages: array<int, string>}>, shop_messages: array<int, string>}
	 */
	private static function merge(?array $into, array $group): array
	{
		if (!$into) {
			return $group;
		}

		$lines = [];

		foreach (array_merge($into['lines'], $group['lines']) as $line) {
			$lines[$line['name']]['name']     = $line['name'];
			$lines[$line['name']]['url']      = $line['url'] ?? $lines[$line['name']]['url'] ?? null;
			$lines[$line['name']]['messages'] = array_unique(
				array_merge($lines[$line['name']]['messages'] ?? [], $line['messages'])
			);
		}

		return [
			'route'         => $group['route'],
			'lines'         => array_values($lines),
			'shop_messages' => array_values(array_unique(
				array_merge($into['shop_messages'], $group['shop_messages'])
			)),
		];
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
