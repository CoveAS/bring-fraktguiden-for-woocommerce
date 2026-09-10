<?php

namespace BringFraktguiden\Customs;

/**
 * The rule that says whether a domestic Norwegian shipment is in transit.
 *
 * A shipment is NVIT when it goes from one place in Norway to another place in
 * Norway, but passes through Sweden or Finland. The postal codes decide it, in
 * both directions. See doc/nvit.md.
 *
 * Bring publishes the ranges as prose on one page and offers no endpoint for
 * them, so they are written out here.
 *
 * @see https://www.bring.no/en/services/customs/norwegian-goods-in-transit-changes
 */
class Nvit
{
	/**
	 * The postal code ranges that pair with each other, in both directions.
	 *
	 * Each entry holds one range, then the ranges it pairs with.
	 */
	private const PAIRS = [
		[[1, 6999], [[8300, 8599], [9300, 9499], [9000, 9159], [9170, 9181], [9188, 9299]]],
		[[1, 7999], [[9160, 9169], [9182, 9187], [9500, 9999]]],
		[[8000, 9769], [[9770, 9991]]],
	];

	/**
	 * Return whether a shipment between two postal codes is in transit.
	 *
	 * The rule covers a Norwegian shipment only. A shipment that leaves Norway
	 * needs an export declaration instead.
	 *
	 * ponytail: the rule leaves out letters and air transported express
	 * services. The caller must skip those services itself.
	 */
	public static function covers(string $from, string $to): bool
	{
		$from = self::code($from);
		$to   = self::code($to);

		if (!$from || !$to) {
			return false;
		}

		foreach (self::PAIRS as [$range, $partners]) {
			foreach ($partners as $partner) {
				if (self::in($from, $range) && self::in($to, $partner)) {
					return true;
				}

				if (self::in($to, $range) && self::in($from, $partner)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Return a Norwegian postal code as a number, or 0 when it is not one.
	 *
	 * A shop may hold the code with a space or with an NO prefix.
	 */
	private static function code(string $value): int
	{
		$digits = preg_replace('/\D/', '', $value);

		return 4 === strlen($digits) ? (int) $digits : 0;
	}

	/**
	 * @param int[] $range The lowest and the highest code of the range.
	 */
	private static function in(int $code, array $range): bool
	{
		return $code >= $range[0] && $code <= $range[1];
	}
}
