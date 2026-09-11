<?php

namespace BringFraktguiden\Customs;

use WC_Order;

/**
 * The HS codes of the products of one order.
 *
 * The booking box shows one row per product, so a shop worker fills in the
 * missing codes on the order screen. A code is a trait of the product, so a row
 * writes the product meta, and every later order carries the code as well.
 *
 * A variation without the override reads the code of its parent. Such a
 * variation shares the row of the parent. See HsCode::owner().
 */
class OrderHsCodes
{
	/**
	 * Return one row per product of an order, keyed by the id of the product.
	 *
	 * @return array<int, array{name: string, image: string, code: string}>
	 */
	public static function rows(WC_Order $order): array
	{
		$rows = [];

		foreach (CustomsDeclaration::items($order) as $item) {
			$product = $item->get_product();

			if (!$product) {
				continue;
			}

			$owner = HsCode::owner($product);
			$id    = $owner->get_id();

			$rows[$id] ??= [
				'name'  => $owner->get_name(),
				'image' => $owner->get_image('thumbnail'),
				'code'  => (string) $owner->get_meta(HsCode::META),
			];
		}

		return $rows;
	}

	/**
	 * Write the codes a shop worker typed.
	 *
	 * The caller passes what the browser sent, so every value is untrusted. A
	 * product that is not part of the order is skipped.
	 *
	 * @param array<int|string, mixed> $codes The code of each product, keyed by product id.
	 */
	public static function save(WC_Order $order, array $codes): void
	{
		$rows    = self::rows($order);
		$written = false;

		foreach ($codes as $id => $code) {
			$id = (int) $id;

			if (!isset($rows[$id])) {
				continue;
			}

			$code = HsCode::strip(sanitize_text_field((string) $code));

			if ($code === $rows[$id]['code']) {
				continue;
			}

			$product = wc_get_product($id);

			if (!$product) {
				continue;
			}

			$product->update_meta_data(HsCode::META, $code);
			$product->save();

			$written = true;
		}

		if ($written) {
			HsCode::forget_used();
		}
	}
}
