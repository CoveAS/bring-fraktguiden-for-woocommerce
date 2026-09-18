<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WC_Product;

/**
 * Proves that Bring accepts the customer number of the shop.
 *
 * Bring answers some accounts with an error when a rate query carries their
 * customer number. The shop only finds out by asking, so the check sends the
 * same query twice, once with the number and once without it.
 */
final class PriceCustomerNumberCheck
{
	/** The setting the check needs turned on. */
	public const SETTING = 'use_customer_number_to_get_prices';

	/** The saved fields that make the answer of Bring change. */
	private const FIELDS = [self::SETTING, 'mybring_customer_number'];

	/**
	 * Ask Bring for rates, and ask again for list prices when the first fails.
	 *
	 * The result carries a note when only the list price query found rates.
	 * The check stores nothing here, so a test of one product may call it.
	 */
	public static function probe(WC_Product $product, string $country, string $postcode): ShippingTestResult
	{
		$result = ShippingTest::run($product, $country, $postcode);
		$number = Fraktguiden_Helper::price_customer_number();

		// A test that never reached Bring says nothing about the number.
		if ($result->passed() || ! $result->call || ! $number) {
			return $result;
		}

		$without = Fraktguiden_Helper::without_price_customer_number(
			fn () => ShippingTest::run($product, $country, $postcode)
		);

		if (! $without->passed()) {
			return $result;
		}

		return $without->with_note(PriceCustomerNumberRefusal::message($number));
	}

	/**
	 * Test the shop, and store what Bring says about the customer number.
	 *
	 * A test of the whole shop answers for the whole shop. A test of one
	 * product must not, so that test calls probe() instead.
	 */
	public static function test_shop(string $country, string $postcode): ShippingTestResult
	{
		// An old refusal gives the number no chance, so drop it before asking.
		PriceCustomerNumberRefusal::forget();

		$result = self::probe(ShippingTest::sample_product(), $country, $postcode);

		if ($result->note) {
			PriceCustomerNumberRefusal::remember((string) Fraktguiden_Helper::get_option('mybring_customer_number'));
		}

		return $result;
	}

	/**
	 * Run the check while the settings save runs.
	 *
	 * @param array<string, mixed> $value    The settings the form is about to save.
	 * @param string[]             $rendered The fields the page showed.
	 *
	 * @return array<string, mixed> The settings, unchanged.
	 */
	public static function apply(array $value, array $rendered): array
	{
		// A page that shows neither field is not worth an API call.
		if (! array_intersect(self::FIELDS, $rendered)) {
			return $value;
		}

		// The helper read the settings before the save, so it still holds the old ones.
		Fraktguiden_Helper::$options = $value;

		// The guard below reads the refusal, so drop the old one first.
		PriceCustomerNumberRefusal::forget();

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

		self::test_shop((string) Fraktguiden_Helper::get_option('from_country'), $postcode);

		return $value;
	}
}
