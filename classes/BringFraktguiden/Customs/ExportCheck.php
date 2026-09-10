<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Whether the shop holds the export data that a booking needs.
 *
 * The check reads the shop settings. It says nothing about the route.
 * ExportRule decides whether an order needs export data at all.
 *
 * @see ExportProblem for the settings, and for why they are all empty today.
 */
class ExportCheck
{
	/**
	 * Return the export problems of the shop.
	 *
	 * A shop with no problems returns an empty array.
	 *
	 * @return array<int, ExportProblem>
	 */
	public static function problems(): array
	{
		$problems = [];

		if ('yes' !== Fraktguiden_Helper::get_option('customs_consent')) {
			$problems[] = ExportProblem::MissingConsent;
		}

		if ('' === (string) Fraktguiden_Helper::get_option('customs_exporter_number')) {
			$problems[] = ExportProblem::MissingExporter;
		}

		if ('' === (string) Fraktguiden_Helper::get_option('customs_nature_of_cargo')) {
			$problems[] = ExportProblem::MissingCargoType;
		}

		return $problems;
	}
}
