<?php

namespace BringFraktguiden\Customs;

use WC_Order;
use WC_Order_Item_Product;

/**
 * One entry of the `customsDeclarations` array of a Bring booking.
 *
 * Customs asks for one entry per item line of the order. Each entry holds the
 * value of the line, a description of the goods, the HS code and the two
 * weights. See doc/customs.md.
 */
class CustomsDeclaration
{
	/**
	 * Return one entry per item line of an order.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function for_order(WC_Order $order): array
	{
		$entries  = [];
		$currency = $order->get_currency();

		foreach ($order->get_items() as $item) {
			if (!$item instanceof WC_Order_Item_Product) {
				continue;
			}

			$entries[] = self::for_order_item($item, $currency);
		}

		return $entries;
	}

	/**
	 * Return the entry of one item line.
	 *
	 * @param string $currency The currency code of the order, for the amount.
	 *
	 * @return array<string, mixed>
	 */
	public static function for_order_item(WC_Order_Item_Product $item, string $currency): array
	{
		$entry = [
			'amount'           => self::amount($item),
			'currency'         => $currency,
			'goodsDescription' => GoodsDescription::for_order_item($item),
			'grossWeight'      => NetWeight::gross_for_order_item($item),
			'netWeight'        => NetWeight::net_for_order_item($item),
			'numberOfPieces'   => max(1, (int) $item->get_quantity()),
		];

		$code = HsCode::for_order_item($item);

		if ($code) {
			$entry['customsArticleNumber'] = $code;
		}

		return $entry;
	}

	/**
	 * Return the value of an item line, with VAT.
	 *
	 * WooCommerce holds the line value without VAT, and the tax of the line
	 * next to it. Customs asks for the value the customer paid, so the two add
	 * up.
	 *
	 * ponytail: a refund does not change the line, so a partly refunded order
	 * declares the value before the refund. Subtract the refunded amount per
	 * line when a shop books after a refund.
	 */
	private static function amount(WC_Order_Item_Product $item): float
	{
		return round((float) $item->get_total() + (float) $item->get_total_tax(), 2);
	}
}
