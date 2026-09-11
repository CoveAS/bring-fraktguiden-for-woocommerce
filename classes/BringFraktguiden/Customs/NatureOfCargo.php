<?php

namespace BringFraktguiden\Customs;

/**
 * Why the goods of a booking move.
 *
 * Bring carries the value in `customsInformation.natureOfCargo.type`. The value
 * decides how customs treats the shipment, and `ioss` is only valid for a sale.
 * See doc/export.md.
 *
 * ponytail: Bring also offers `OTHER`, which needs a free text
 * `natureOfCargo.detail`. The booking form holds a select and no text field, so
 * `OTHER` is left out. Add both together.
 */
enum NatureOfCargo: string
{
	case SaleOfGoods = 'SALE_OF_GOODS';

	case ReturnedGoods = 'RETURNED_GOODS';

	case Gift = 'GIFT';

	case CommercialSample = 'COMMERCIAL_SAMPLE';

	case Documents = 'DOCUMENTS';

	/**
	 * The name of the booking form field.
	 */
	public const FIELD = '_bring_nature_of_cargo';

	/**
	 * Return the value the booking form holds.
	 *
	 * A bulk booking sends no form, so it returns the sale of goods. A shop
	 * sells goods, and a return or a gift is the case a shop picks by hand.
	 */
	public static function from_request(): self
	{
		$posted = filter_input(INPUT_POST, self::FIELD, FILTER_UNSAFE_RAW);

		return self::tryFrom((string) $posted) ?? self::SaleOfGoods;
	}

	/**
	 * Return the label a shop reads.
	 */
	public function label(): string
	{
		return match ($this) {
			self::SaleOfGoods      => __('Sale of goods', 'bring-fraktguiden-for-woocommerce'),
			self::ReturnedGoods    => __('Returned goods', 'bring-fraktguiden-for-woocommerce'),
			self::Gift             => __('Gift', 'bring-fraktguiden-for-woocommerce'),
			self::CommercialSample => __('Commercial sample', 'bring-fraktguiden-for-woocommerce'),
			self::Documents        => __('Documents', 'bring-fraktguiden-for-woocommerce'),
		};
	}
}
