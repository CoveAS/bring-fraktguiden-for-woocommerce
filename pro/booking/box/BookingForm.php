<?php

namespace BringFraktguidenPro\Booking\Box;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguiden\Customs\NatureOfCargo;
use BringFraktguidenPro\Booking\Actions\Get_First_Enabled_Bring_Product;
use BringFraktguidenPro\Booking\Consignment_Request\Bring_Booking_Consignment_Request;
use DateTime;
use WC_Order;
use WC_Order_Item_Shipping;

/**
 * Everything a shop worker fills in before a booking.
 *
 * The object is the whole form. The box renders it, the draft stores it, and
 * the sender reads it. A field lives in one place.
 */
class BookingForm
{
	/**
	 * @param string          $customer_number     The Mybring customer number.
	 * @param string          $service             The Bring product, for example 5800.
	 * @param string          $shipping_date       Date as Y-m-d.
	 * @param string          $shipping_time       Time as H:i.
	 * @param string          $delivery_date       Requested delivery date as Y-m-d, or empty.
	 * @param string          $delivery_time       Requested delivery time as H:i, or empty.
	 * @param string          $nature_of_cargo     Why the goods move.
	 * @param string          $info_sender         Extra address line for the sender.
	 * @param string          $info_recipient      Extra address line for the recipient.
	 * @param string[]        $additional_services The value added service codes to book.
	 * @param PackageLine[]   $packages            The parcels to book.
	 */
	public function __construct(
		public string $customer_number = '',
		public string $service = '',
		public string $shipping_date = '',
		public string $shipping_time = '',
		public string $delivery_date = '',
		public string $delivery_time = '',
		public string $nature_of_cargo = '',
		public string $info_sender = '',
		public string $info_recipient = '',
		public array $additional_services = [],
		public array $packages = [],
	) {
	}

	/**
	 * Build the form a shop worker sees when no draft exists.
	 */
	public static function from_order(WC_Order $order, ?WC_Order_Item_Shipping $shipping_item): self
	{
		$service = $shipping_item?->get_meta('bring_product') ?: '';

		if (!$service) {
			$service = (string) (new Get_First_Enabled_Bring_Product())();
		}

		$ship = new DateTime('+1 hour', wp_timezone());

		$form = new self(
			customer_number: (string) Fraktguiden_Helper::get_option('mybring_customer_number'),
			service: (string) $service,
			shipping_date: $ship->format('Y-m-d'),
			shipping_time: $ship->format('H:i'),
			nature_of_cargo: NatureOfCargo::SaleOfGoods->value,
			packages: self::packages_of($shipping_item),
		);

		$slot = $shipping_item?->get_meta('bring_fraktguiden_time_slot');

		if ($slot) {
			$requested           = new DateTime($slot);
			$form->delivery_date = $requested->format('Y-m-d');
			$form->delivery_time = $requested->format('H:i');
		}

		foreach ($form->service()?->vas ?? [] as $vas) {
			if ($vas->value) {
				$form->additional_services[] = $vas->code;
			}
		}

		return $form;
	}

	/**
	 * Build the form from what the browser sent.
	 *
	 * Every value arrives as untrusted text, so every field is cleaned here.
	 *
	 * @param array<string, mixed> $payload
	 */
	public static function from_payload(array $payload): self
	{
		$packages = [];

		foreach ((array) ($payload['packages'] ?? []) as $row) {
			if (is_array($row)) {
				$packages[] = PackageLine::from_payload($row);
			}
		}

		$services = [];

		foreach ((array) ($payload['additional_services'] ?? []) as $code) {
			$services[] = sanitize_text_field((string) $code);
		}

		return new self(
			customer_number: sanitize_text_field((string) ($payload['customer_number'] ?? '')),
			service: sanitize_text_field((string) ($payload['service'] ?? '')),
			shipping_date: self::clean_date((string) ($payload['shipping_date'] ?? '')),
			shipping_time: self::clean_time((string) ($payload['shipping_time'] ?? '')),
			delivery_date: self::clean_date((string) ($payload['delivery_date'] ?? '')),
			delivery_time: self::clean_time((string) ($payload['delivery_time'] ?? '')),
			nature_of_cargo: self::clean_cargo((string) ($payload['nature_of_cargo'] ?? '')),
			info_sender: sanitize_textarea_field((string) ($payload['info_sender'] ?? '')),
			info_recipient: sanitize_textarea_field((string) ($payload['info_recipient'] ?? '')),
			additional_services: $services,
			packages: $packages,
		);
	}

	/**
	 * Build the form from what the draft holds.
	 *
	 * @param array<string, mixed> $stored
	 */
	public static function from_array(array $stored): self
	{
		return self::from_payload($stored);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function to_array(): array
	{
		return [
			'customer_number'     => $this->customer_number,
			'service'             => $this->service,
			'shipping_date'       => $this->shipping_date,
			'shipping_time'       => $this->shipping_time,
			'delivery_date'       => $this->delivery_date,
			'delivery_time'       => $this->delivery_time,
			'nature_of_cargo'     => $this->nature_of_cargo,
			'info_sender'         => $this->info_sender,
			'info_recipient'      => $this->info_recipient,
			'additional_services' => $this->additional_services,
			'packages'            => array_map(fn(PackageLine $line) => $line->to_array(), $this->packages),
		];
	}

	/**
	 * Return the service the form books, or null when the id names none.
	 */
	public function service(): ?Fraktguiden_Service
	{
		if (!$this->service) {
			return null;
		}

		return Fraktguiden_Service::find('woocommerce_bring_fraktguiden_services', $this->service);
	}

	/**
	 * Return the moment the parcel leaves the shop, in the shape Bring wants.
	 */
	public function shipping_date_time(): string
	{
		return $this->shipping_date . 'T' . ($this->shipping_time ?: '00:00') . ':00';
	}

	/**
	 * Return the moment the customer asked for, or an empty string.
	 */
	public function delivery_date_time(): string
	{
		if (!$this->delivery_date) {
			return '';
		}

		return $this->delivery_date . 'T' . ($this->delivery_time ?: '00:00') . ':00';
	}

	/**
	 * Return whether the form books the given value added service.
	 */
	public function books(string $vas_code): bool
	{
		return in_array($vas_code, $this->additional_services, true);
	}

	/**
	 * Return the parcels of a shipping line, packing the order when it has none.
	 *
	 * @return PackageLine[]
	 */
	private static function packages_of(?WC_Order_Item_Shipping $shipping_item): array
	{
		if (!$shipping_item) {
			return [new PackageLine()];
		}

		$packages = $shipping_item->get_meta('_fraktguiden_packages_v2');

		if (!$packages) {
			$packages = Bring_Booking_Consignment_Request::create($shipping_item)->order_update_packages();
		}

		if (!is_array($packages) || !$packages) {
			return [new PackageLine()];
		}

		return array_values(array_map(
			fn(array $package) => PackageLine::from_meta($package),
			array_filter($packages, 'is_array')
		));
	}

	private static function clean_date(string $value): string
	{
		return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
	}

	private static function clean_time(string $value): string
	{
		return preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value) ? $value : '';
	}

	private static function clean_cargo(string $value): string
	{
		return NatureOfCargo::tryFrom($value)?->value ?? '';
	}
}
