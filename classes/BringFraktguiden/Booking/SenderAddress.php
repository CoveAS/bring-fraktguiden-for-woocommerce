<?php

namespace BringFraktguiden\Booking;

use BringFraktguiden\Settings\Settings;

/**
 * The address a booking ships from.
 *
 * A shop ships from its WooCommerce store address. A shop that ships from
 * somewhere else turns on booking_use_custom_address and fills in the address
 * boxes of the booking settings.
 *
 * The boxes keep their value while the setting is off, so no reader may take
 * the boxes on their own.
 */
class SenderAddress
{
	/**
	 * Return the address, keyed by the setting names.
	 *
	 * @return array<string, string>
	 */
	public static function get(): array
	{
		$settings = Settings::instance();

		if (! $settings->booking_use_custom_address->value) {
			return self::store_address();
		}

		return [
			'booking_address_store_name' => (string) $settings->booking_address_store_name->value,
			'booking_address_street1'    => (string) $settings->booking_address_street1->value,
			'booking_address_street2'    => (string) $settings->booking_address_street2->value,
			'booking_address_postcode'   => (string) $settings->booking_address_postcode->value,
			'booking_address_city'       => (string) $settings->booking_address_city->value,
			'booking_address_country'    => (string) $settings->booking_address_country->value,
		];
	}

	/**
	 * Return the store address of WooCommerce.
	 *
	 * @return array<string, string>
	 */
	private static function store_address(): array
	{
		return [
			'booking_address_store_name' => (string) get_bloginfo('name'),
			'booking_address_street1'    => (string) get_option('woocommerce_store_address', ''),
			'booking_address_street2'    => (string) get_option('woocommerce_store_address_2', ''),
			'booking_address_postcode'   => (string) get_option('woocommerce_store_postcode', ''),
			'booking_address_city'       => (string) get_option('woocommerce_store_city', ''),
			// The store country holds the state after a colon, for example NO:03.
			'booking_address_country'    => explode(':', (string) get_option('woocommerce_default_country', ''))[0],
		];
	}
}
