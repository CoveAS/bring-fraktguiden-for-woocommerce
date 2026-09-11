<?php

namespace BringFraktguiden\Admin;

use WC_Shipping_Rate;

/**
 * What one shipping test found.
 *
 * A result holds rates, or a problem, never both. The messages come from Bring
 * and explain an empty rate list.
 */
final class ShippingTestResult
{
	/**
	 * @param WC_Shipping_Rate[] $rates    The rates the checkout would show.
	 * @param string[]           $messages What Bring said about an empty answer.
	 * @param string             $problem  Why the test could not run at all.
	 */
	private function __construct(
		public readonly array $rates = [],
		public readonly array $messages = [],
		public readonly string $problem = '',
	) {
	}

	/** @param string[] $messages */
	public static function problem(string $problem, array $messages = []): self
	{
		return new self(problem: $problem, messages: $messages);
	}

	/** @param WC_Shipping_Rate[] $rates */
	public static function rates(array $rates): self
	{
		return new self(rates: $rates);
	}

	public function passed(): bool
	{
		return (bool) $this->rates;
	}
}
