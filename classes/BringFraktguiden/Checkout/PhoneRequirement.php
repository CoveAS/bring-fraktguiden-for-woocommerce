<?php

namespace BringFraktguiden\Checkout;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WP_Error;

/**
 * The phone number a service needs from the recipient.
 *
 * Bring reaches the recipient by phone on some services. Pakkeboks sends the
 * locker code by SMS, and the Pakke hjem pluss driver rings the recipient
 * before arrival. The service rows carry the trait as 'requires_phone' => true
 * in config/services.php.
 *
 * A service without the flag needs no number, so an unknown service stops no
 * order.
 *
 * The booking sends the billing phone, so the billing phone is the field this
 * class checks. See Bring_Booking_Consignment_Request::get_recipient_address().
 */
class PhoneRequirement
{
	/**
	 * The fewest digits a usable number holds.
	 *
	 * ponytail: a naive length check. It passes a number with the right count
	 * of wrong digits. A per country pattern is the upgrade path, and
	 * config/phone-i18n.php holds the country codes it would need.
	 */
	private const MIN_DIGITS = 8;

	public static function init(): void
	{
		add_action('woocommerce_after_checkout_validation', [self::class, 'validate_classic'], 10, 2);
		add_action('woocommerce_blocks_validate_location_address_fields', [self::class, 'validate_block'], 10, 3);
	}

	/**
	 * Add the error on the classic checkout.
	 *
	 * @param array    $data   The posted checkout fields.
	 * @param WP_Error $errors The errors so far.
	 */
	public static function validate_classic(array $data, WP_Error $errors): void
	{
		$service = self::service_without_phone($data['billing_phone'] ?? '');

		if ($service) {
			$errors->add('billing_phone', self::message($service));
		}
	}

	/**
	 * Add the error on the block checkout.
	 *
	 * The block checkout validates the billing address and the shipping address
	 * apart. Only the billing address carries the number the booking sends.
	 *
	 * @param WP_Error $errors The errors so far.
	 * @param array    $fields The posted address fields.
	 * @param string   $group  'billing' or 'shipping'.
	 */
	public static function validate_block(WP_Error $errors, array $fields, string $group): void
	{
		if ('billing' !== $group) {
			return;
		}

		$service = self::service_without_phone($fields['phone'] ?? '');

		if ($service) {
			$errors->add('phone', self::message($service));
		}
	}

	/**
	 * The name of the chosen service that the number fails, or an empty string.
	 *
	 * @param string $phone The number the customer gave.
	 */
	private static function service_without_phone(string $phone): string
	{
		if (self::is_usable($phone)) {
			return '';
		}

		foreach (self::chosen_products() as $product) {
			$service = Fraktguiden_Helper::get_service_data_for_key($product);

			if ($service['requires_phone'] ?? false) {
				return $service['productName'] ?? $product;
			}
		}

		return '';
	}

	/**
	 * Return whether Bring can send a message to the number.
	 *
	 * @param string $phone The number the customer gave.
	 */
	private static function is_usable(string $phone): bool
	{
		return strlen(preg_replace('/\D/', '', $phone)) >= self::MIN_DIGITS;
	}

	/**
	 * Every Bring product the customer chose, one per package.
	 *
	 * The rate carries its product in meta, which push_rate() always sets. The
	 * rate id is no help, because a pickup point rate adds the point id to it.
	 *
	 * @return string[]
	 */
	private static function chosen_products(): array
	{
		$chosen   = WC()->session?->get('chosen_shipping_methods') ?? [];
		$products = [];

		foreach (WC()->shipping()->get_packages() as $index => $package) {
			$rate = $package['rates'][$chosen[$index] ?? ''] ?? null;

			if ($rate) {
				$products[] = $rate->get_meta_data()['bring_product'] ?? '';
			}
		}

		return array_filter($products);
	}

	/**
	 * The error the customer reads.
	 *
	 * @param string $service The name of the service that needs the number.
	 */
	private static function message(string $service): string
	{
		return sprintf(
			/* translators: %s: the name of a shipping service, for example Pakkeboks. */
			__('%s needs a phone number. Bring sends the recipient a message about the parcel.', 'bring-fraktguiden-for-woocommerce'),
			$service
		);
	}
}
