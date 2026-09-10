<?php

namespace BringFraktguiden\Customs;

use BringFraktguiden\Order\ShippedLine;
use WC_Order;
use WC_Order_Item_Product;

/**
 * One entry of the `customsDeclarations` array of a Bring booking.
 *
 * Customs asks for one entry per item line of the order. Each entry holds the
 * value of the line, a description of the goods, the HS code and the two
 * weights. See doc/customs.md.
 *
 * The declaration covers the goods the shop ships. A line the shop refunds in
 * full ships nothing, so it gets no entry.
 */
class CustomsDeclaration
{
	/**
	 * Return one entry per shipped item line of an order.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function for_order(WC_Order $order): array
	{
		$entries = [];

		foreach ($order->get_items() as $item) {
			if (!$item instanceof WC_Order_Item_Product) {
				continue;
			}

			if (ShippedLine::for_order_item($item, $order)->pieces < 1) {
				continue;
			}

			$entries[] = self::for_order_item($item, $order);
		}

		return $entries;
	}

	/**
	 * Return the entry of one item line.
	 *
	 * @return array<string, mixed>
	 */
	public static function for_order_item(WC_Order_Item_Product $item, WC_Order $order): array
	{
		$shipped = ShippedLine::for_order_item($item, $order);

		$entry = [
			'amount'           => $shipped->value,
			'currency'         => $order->get_currency(),
			'goodsDescription' => GoodsDescription::for_order_item($item),
			'grossWeight'      => NetWeight::gross_for_order_item($item),
			'netWeight'        => NetWeight::net_for_order_item($item),
			'numberOfPieces'   => max(1, $shipped->pieces),
		];

		$code = HsCode::for_order_item($item);

		if ($code) {
			$entry['customsArticleNumber'] = $code;
		}

		return $entry;
	}
}
