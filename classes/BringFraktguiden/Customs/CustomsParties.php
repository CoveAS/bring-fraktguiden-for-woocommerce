<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WC_Order;

/**
 * The export parties of a Bring booking.
 *
 * Bring requires an exporter and an importer on a shipment that leaves Norway.
 * Bring accepts the sender data for the exporter, and the recipient data for
 * the importer. NVIT needs neither. See doc/export.md.
 *
 * The parties carry an address only. The contact, the reference and the
 * additional address info belong to the sender party and the recipient party.
 */
class CustomsParties
{
	/**
	 * Return the exporter and the importer, or an empty array.
	 *
	 * @param WC_Order $order   The order the booking ships.
	 * @param string   $product The Bring product, for example BUSINESS_PARCEL.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function for_order(WC_Order $order, string $product): array
	{
		if (CustomsRoute::EXPORT !== CustomsRoute::for_order($order, $product)) {
			return [];
		}

		return [
			'exporter' => self::exporter(),
			'importer' => self::importer($order),
		];
	}

	/**
	 * Return the exporter, built from the booking address of the shop.
	 *
	 * @return array<string, string>
	 */
	private static function exporter(): array
	{
		$exporter = [
			'name'        => (string) Fraktguiden_Helper::get_option('booking_address_store_name'),
			'addressLine' => (string) Fraktguiden_Helper::get_option('booking_address_street1'),
			'postalCode'  => (string) Fraktguiden_Helper::get_option('booking_address_postcode'),
			'city'        => (string) Fraktguiden_Helper::get_option('booking_address_city'),
			'countryCode' => (string) Fraktguiden_Helper::get_option('booking_address_country'),
		];

		// Bring takes at most 30 characters. An empty number is left out,
		// because the field holds no answer then.
		$number = trim((string) Fraktguiden_Helper::get_option('customs_exporter_number'));

		if ('' !== $number) {
			$exporter['vatNumber'] = substr($number, 0, 30);
		}

		return $exporter;
	}

	/**
	 * Return the importer, built from the shipping address of the order.
	 *
	 * @return array<string, string>
	 */
	private static function importer(WC_Order $order): array
	{
		$person = trim($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name());

		return [
			'name'        => $order->get_shipping_company() ?: $person,
			'addressLine' => $order->get_shipping_address_1(),
			'postalCode'  => $order->get_shipping_postcode(),
			'city'        => $order->get_shipping_city(),
			'countryCode' => $order->get_shipping_country(),
		];
	}
}
