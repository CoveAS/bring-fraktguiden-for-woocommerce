<?php

namespace BringFraktguiden\Admin;

use WC_Product;
use WC_Shipping_Method_Bring;
use WC_Shipping_Zone;

/**
 * Asks Bring for the rates of one product, the way the checkout would.
 *
 * The setup page tests a parcel made in memory. The product screen tests the
 * product the shop owner has open. Both send the same request and read the
 * same answer.
 */
final class ShippingTest
{
	public const ACTION = 'bfg_shipping_test';

	/** Set once a test gives back a rate. Step 5 of the setup page reads it. */
	private const OPTION = 'bring_fraktguiden_shipping_tested';

	public static function init(): void
	{
		add_action('wp_ajax_' . self::ACTION, [self::class, 'ajax']);
	}

	/** Has a test ever given back a rate? */
	public static function passed(): bool
	{
		return (bool) get_option(self::OPTION);
	}

	public static function ajax(): void
	{
		check_ajax_referer(self::ACTION);

		if (! current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('You may not test Bring shipping.', 'bring-fraktguiden-for-woocommerce'), 403);
		}

		$product_id = (int) ($_POST['product_id'] ?? 0);
		$product = $product_id ? wc_get_product($product_id) : self::sample_product();

		if (! $product instanceof WC_Product) {
			$result = ShippingTestResult::problem(
				__('This product could not be read.', 'bring-fraktguiden-for-woocommerce')
			);
		} else {
			$result = self::run(
				$product,
				strtoupper(sanitize_text_field(wp_unslash($_POST['country'] ?? ''))),
				sanitize_text_field(wp_unslash($_POST['postcode'] ?? ''))
			);
		}

		self::render($result);
		wp_die();
	}

	/** A parcel of one kilo, 20 by 15 by 10 centimetres, never saved. */
	public static function sample_product(): WC_Product
	{
		$product = new WC_Product();
		$product->set_name(__('Sample parcel', 'bring-fraktguiden-for-woocommerce'));
		$product->set_weight((string) wc_get_weight(1, get_option('woocommerce_weight_unit'), 'kg'));
		$product->set_length((string) wc_get_dimension(20, get_option('woocommerce_dimension_unit'), 'cm'));
		$product->set_width((string) wc_get_dimension(15, get_option('woocommerce_dimension_unit'), 'cm'));
		$product->set_height((string) wc_get_dimension(10, get_option('woocommerce_dimension_unit'), 'cm'));

		return $product;
	}

	/** Ask Bring what the checkout would show for this product and address. */
	public static function run(WC_Product $product, string $country, string $postcode): ShippingTestResult
	{
		if (! $product->get_weight() && ! $product->has_dimensions()) {
			return ShippingTestResult::problem(
				__('This product has no weight and no dimensions. Bring needs one of them.', 'bring-fraktguiden-for-woocommerce')
			);
		}

		// A rate query reads the session and the customer, and neither exists
		// on an admin screen.
		WC()->frontend_includes();
		WC()->initialize_session();
		WC()->initialize_cart();

		$package = self::package($product, $country ?: WC()->countries->get_base_country(), $postcode);
		$zone = wc_get_shipping_zone($package);

		if (! $zone) {
			return ShippingTestResult::problem(
				__('No shipping zone covers this address.', 'bring-fraktguiden-for-woocommerce')
			);
		}

		$bring = self::bring_method($zone);

		if (! $bring) {
			return ShippingTestResult::problem(
				sprintf(
					/* translators: %s: the name of the shipping zone. */
					__('Bring is not active in the shipping zone %s.', 'bring-fraktguiden-for-woocommerce'),
					$zone->get_zone_name()
				)
			);
		}

		$rates = $bring->get_rates_for_package($package);
		$case = $bring->get_fallback_case();

		// A rate pushed after a fallback case is the price of the shop, not a
		// price from Bring. The test passes only on a price from Bring.
		if ($case) {
			return ShippingTestResult::problem($case->reason(), $bring->get_trace_messages());
		}

		return ShippingTestResult::rates($rates);
	}

	/** @return array<string, mixed> */
	private static function package(WC_Product $product, string $country, string $postcode): array
	{
		return [
			'destination' => [
				'country' => $country,
				'state' => '',
				'postcode' => $postcode,
			],
			'contents' => [
				[
					'key' => 'bfg-shipping-test',
					'product_id' => $product->get_id(),
					'variation_id' => null,
					'variation' => null,
					'quantity' => 1,
					'data' => $product,
				],
			],
			'contents_cost' => (float) $product->get_price(),
			'applied_coupons' => [],
			'user' => ['ID' => get_current_user_id()],
		];
	}

	/** The Bring method of a zone, free or pro, when it is turned on. */
	private static function bring_method(WC_Shipping_Zone $zone): ?WC_Shipping_Method_Bring
	{
		foreach ($zone->get_shipping_methods(true) as $method) {
			if ($method instanceof WC_Shipping_Method_Bring) {
				return $method;
			}
		}

		return null;
	}

	/** Draw the answer, and remember a test that found rates. */
	private static function render(ShippingTestResult $result): void
	{
		if ($result->passed()) {
			update_option(self::OPTION, 'yes');
		}

		require dirname(__DIR__, 3) . '/build/templates/admin/parts/shipping-test-result.php';
	}
}
