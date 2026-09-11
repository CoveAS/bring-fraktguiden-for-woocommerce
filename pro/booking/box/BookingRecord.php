<?php

namespace BringFraktguidenPro\Booking\Box;

use BringFraktguidenPro\Booking\Consignment\Bring_Consignment;

/**
 * One attempt to book an order with Bring.
 *
 * The record holds what was sent and what Bring answered. A failed attempt is a
 * record too, so a shop worker can read why it failed after a reload.
 */
class BookingRecord
{
	/**
	 * @param array<string, mixed> $response The Bring response as an array.
	 * @param array<string, mixed> $form     The form that was sent.
	 */
	public function __construct(
		public array $response,
		public array $form = [],
		public string $booked_at = '',
		public string $booked_by = '',
		public int $order_id = 0,
	) {
	}

	/**
	 * @param array<string, mixed> $entry
	 */
	public static function from_array(array $entry, int $order_id): self
	{
		return new self(
			response: (array) ($entry['response'] ?? []),
			form: (array) ($entry['form'] ?? []),
			booked_at: (string) ($entry['booked_at'] ?? ''),
			booked_by: (string) ($entry['booked_by'] ?? ''),
			order_id: $order_id,
		);
	}

	/**
	 * Return the consignments Bring confirmed. A failed attempt returns none.
	 *
	 * @return Bring_Consignment[]
	 */
	public function consignments(): array
	{
		return Bring_Consignment::create_from_response($this->response, $this->order_id);
	}

	/**
	 * Return every message that explains a failure.
	 *
	 * @return string[]
	 */
	public function errors(): array
	{
		$result = [];
		$body   = json_decode((string) ($this->response['body'] ?? ''));

		foreach ($body->consignments ?? [] as $consignment) {
			foreach ($consignment->errors ?? [] as $error) {
				foreach ($error->messages ?? [] as $message) {
					$result[] = $error->code . ': ' . $message->message;
				}
			}
		}

		foreach ((array) ($this->response['errors'] ?? []) as $error) {
			$result[] = (string) $error;
		}

		// A non ok body carries the explanation, for example an authentication
		// failure, so it belongs in the list.
		if (!$this->accepted() && !$result) {
			$result[] = (string) ($this->response['body'] ?? '');
		}

		return $result;
	}

	public function failed(): bool
	{
		return (bool) $this->errors();
	}

	/**
	 * Return the time the shop worker reads, in the time zone of the shop.
	 */
	public function booked_at_local(): string
	{
		if (!$this->booked_at) {
			return '';
		}

		return wp_date(
			get_option('date_format') . ' ' . get_option('time_format'),
			(int) strtotime($this->booked_at . ' UTC')
		);
	}

	private function accepted(): bool
	{
		return in_array((int) ($this->response['status_code'] ?? 0), [200, 201, 202, 203, 204], true);
	}
}
