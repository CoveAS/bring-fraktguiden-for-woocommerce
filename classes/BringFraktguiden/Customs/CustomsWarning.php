<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WC_Order;
use WC_Order_Item_Shipping;

/**
 * What a shop must fix before Bring accepts a customs booking.
 *
 * The warning holds the problems. It never stops a booking. Bring holds the
 * real guard, and answers with the reason when it refuses.
 *
 * The reason says which rule applies. NVIT covers goods that pass through
 * Sweden or Finland on the way to another place in Norway. An export covers
 * goods that leave Norway. See doc/nvit.md and doc/export.md.
 */
class CustomsWarning
{
	public const NVIT = 'nvit';

	public const EXPORT = 'export';

	/**
	 * @param string                                      $reason        NVIT or EXPORT.
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
	 * Return the warning of one shipping item, or null when the shop has
	 * nothing to fix.
	 *
	 * @param WC_Order_Item_Shipping $item    The Bring shipping line.
	 * @param string                 $product The Bring product, for example BUSINESS_PARCEL.
	 */
	public static function for_shipping_item(WC_Order_Item_Shipping $item, string $product): ?self
	{
		$order = $item->get_order();

		if (!$order instanceof WC_Order) {
			return null;
		}

		$reason = self::reason($order, $product);

		if (!$reason) {
			return null;
		}

		$lines         = self::lines($order);
		$shop_messages = self::EXPORT === $reason ? self::shop_messages() : [];

		if (!$lines && !$shop_messages) {
			return null;
		}

		return new self($reason, $lines, $shop_messages);
	}

	/**
	 * Return the rule that covers the route, or an empty string.
	 */
	private static function reason(WC_Order $order, string $product): string
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
	 * @return array<int, string>
	 */
	private static function shop_messages(): array
	{
		return array_map(
			fn(ExportProblem $problem) => $problem->message(),
			ExportCheck::problems()
		);
	}
}
