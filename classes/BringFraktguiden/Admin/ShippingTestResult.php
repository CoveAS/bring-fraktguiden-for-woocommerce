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
	 * @param array{request: string, answer: string}|null $call The bodies of the Bring call.
	 */
	private function __construct(
		public readonly array $rates = [],
		public readonly array $messages = [],
		public readonly string $problem = '',
		public readonly ?array $call = null,
	) {
	}

	/**
	 * @param string[] $messages
	 * @param array{request: string, answer: string}|null $call
	 */
	public static function problem(string $problem, array $messages = [], ?array $call = null): self
	{
		return new self(problem: $problem, messages: $messages, call: $call);
	}

	/**
	 * @param WC_Shipping_Rate[] $rates
	 * @param array{request: string, answer: string}|null $call
	 */
	public static function rates(array $rates, ?array $call = null): self
	{
		return new self(rates: $rates, call: $call);
	}

	/**
	 * One body of the Bring call, laid out for a reader.
	 *
	 * A body that is not JSON comes back as it arrived. Bring answers an error
	 * with plain text.
	 *
	 * @param string $part 'request' or 'answer'.
	 */
	public function json(string $part): string
	{
		$raw = (string) ($this->call[$part] ?? '');
		$decoded = json_decode($raw, true);

		if (null === $decoded) {
			return $raw;
		}

		return (string) wp_json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	public function passed(): bool
	{
		return (bool) $this->rates;
	}
}
