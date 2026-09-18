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
	 * @param array{request: string, answer: string, status: int|string}|null $call The Bring call.
	 * @param string             $note     What the plugin changed to get these rates.
	 * @param bool               $without_customer_number True when only a query without the customer number found rates.
	 */
	private function __construct(
		public readonly array $rates = [],
		public readonly array $messages = [],
		public readonly string $problem = '',
		public readonly ?array $call = null,
		public readonly string $note = '',
		public readonly bool $without_customer_number = false,
	) {
	}

	/** The same result, with a line for the shop owner to read. */
	public function with_note(string $note): self
	{
		return new self($this->rates, $this->messages, $this->problem, $this->call, $note, $this->without_customer_number);
	}

	/**
	 * @param string[] $messages
	 * @param array{request: string, answer: string, status: int|string}|null $call
	 */
	public static function problem(string $problem, array $messages = [], ?array $call = null): self
	{
		return new self(problem: $problem, messages: $messages, call: $call);
	}

	/**
	 * @param WC_Shipping_Rate[] $rates
	 * @param array{request: string, answer: string, status: int|string}|null $call
	 */
	public static function rates(array $rates, ?array $call = null, bool $without_customer_number = false): self
	{
		return new self(rates: $rates, call: $call, without_customer_number: $without_customer_number);
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

	/** The HTTP status Bring answered with. Zero when no answer came back. */
	public function status(): int
	{
		return (int) ($this->call['status'] ?? 0);
	}

	public function passed(): bool
	{
		return (bool) $this->rates;
	}
}
