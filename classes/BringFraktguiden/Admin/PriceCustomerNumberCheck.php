<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Proves on a settings save that Bring accepts the customer number.
 *
 * Bring answers some accounts with an error when a rate query carries their
 * customer number. The shop only finds out by asking, so the check runs one
 * rate query from the postal code the shop ships from.
 */
final class PriceCustomerNumberCheck
{
	/** The setting the check turns off. */
	public const SETTING = 'use_customer_number_to_get_prices';

	/** The saved fields that make the answer of Bring change. */
	private const FIELDS = [self::SETTING, 'mybring_customer_number'];

	/** The customer number Bring refused, so the settings page can say which. */
	private const OPTION = 'bring_fraktguiden_price_customer_number_refused';

	/** Remember the number Bring refused. An empty string forgets the last one. */
	public static function refused(string $customer_number): void
	{
		update_option(self::OPTION, $customer_number);
	}

	/**
	 * What the settings page says under the checkbox, or an empty string.
	 *
	 * A number the shop no longer uses says nothing about the one it uses now.
	 */
	public static function message(): string
	{
		$number = (string) get_option(self::OPTION, '');

		if (! $number || $number !== (string) Fraktguiden_Helper::get_option('mybring_customer_number')) {
			return '';
		}

		return sprintf(
			/* translators: %s: the Mybring customer number. */
			__('Bring gives no price when the shop asks with customer number %s, so this setting stays off. Ask your Bring contact for more information.', 'bring-fraktguiden-for-woocommerce'),
			$number
		);
	}

	/**
	 * The settings to save, with the setting off when Bring refuses the number.
	 *
	 * @param array<string, mixed> $value    The settings the form is about to save.
	 * @param string[]             $rendered The fields the page showed.
	 *
	 * @return array<string, mixed>
	 */
	public static function apply(array $value, array $rendered): array
	{
		// A page that shows neither field is not worth an API call.
		if (! array_intersect(self::FIELDS, $rendered)) {
			return $value;
		}

		// The helper read the settings before the save, so it still holds the old ones.
		Fraktguiden_Helper::$options = $value;

		if (! Fraktguiden_Helper::price_customer_number()) {
			return $value;
		}

		// A blank from_zip falls back to the store address, the way a rate
		// query falls back. Without either the query fails for its own reason,
		// which says nothing about the customer number.
		$postcode = (string) (Fraktguiden_Helper::get_option('from_zip') ?: get_option('woocommerce_store_postcode', ''));

		if (! $postcode) {
			return $value;
		}

		$result = ShippingTest::run(
			ShippingTest::sample_product(),
			(string) Fraktguiden_Helper::get_option('from_country'),
			$postcode
		);

		self::refused($result->without_customer_number ? (string) Fraktguiden_Helper::get_option('mybring_customer_number') : '');

		if ($result->without_customer_number) {
			$value[self::SETTING] = 'no';
			Fraktguiden_Helper::$options = $value;
		}

		return $value;
	}
}
