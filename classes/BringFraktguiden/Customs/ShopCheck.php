<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Whether the shop holds the customs data that a booking needs.
 *
 * The check reads the shop settings. It says nothing about the order lines.
 * CustomsRoute decides which rule covers a booking.
 *
 * The cargo type is not here. The booking form asks for it per order, and the
 * select always holds a value. See NatureOfCargo.
 */
class ShopCheck
{
	/**
	 * Return the problems of the shop for one customs rule.
	 *
	 * A shop with no problems returns an empty array.
	 *
	 * @param string $route A CustomsRoute constant.
	 *
	 * @return array<int, ShopProblem>
	 */
	public static function problems(string $route): array
	{
		$problems = [];

		if (!CustomsConsent::given()) {
			$problems[] = ShopProblem::MissingConsent;
		}

		if (CustomsRoute::EXPORT === $route && '' === (string) Fraktguiden_Helper::get_option('customs_exporter_number')) {
			$problems[] = ShopProblem::MissingExporter;
		}

		return $problems;
	}
}
