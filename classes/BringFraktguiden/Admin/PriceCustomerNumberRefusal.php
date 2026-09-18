<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * The customer number Bring refuses to price a rate query with.
 *
 * Bring answers some accounts with an error when a rate query carries their
 * customer number. The shop then asks for list prices instead.
 *
 * The refusal is stored on its own. The checkbox of the shop owner stays as
 * the owner left it, so the shop tries the number again on the next save.
 */
final class PriceCustomerNumberRefusal
{
	private const OPTION = 'bring_fraktguiden_price_customer_number_refused';

	/** Remember that Bring refuses this customer number. */
	public static function remember(string $customer_number): void
	{
		update_option(self::OPTION, $customer_number);
	}

	/** Forget the refusal, so the next rate query carries the number again. */
	public static function forget(): void
	{
		update_option(self::OPTION, '');
	}

	/** Does a stored refusal cover this customer number? */
	public static function blocks(string $customer_number): bool
	{
		return '' !== $customer_number && $customer_number === (string) get_option(self::OPTION, '');
	}

	/**
	 * What the settings page says under the checkbox, or an empty string.
	 *
	 * A number the shop no longer uses says nothing about the one it uses now.
	 */
	public static function notice(): string
	{
		$number = (string) Fraktguiden_Helper::get_option('mybring_customer_number');

		return self::blocks($number) ? self::message($number) : '';
	}

	/** The line a shop owner reads about a refused customer number. */
	public static function message(string $customer_number): string
	{
		return sprintf(
			/* translators: %s: the Mybring customer number. */
			__('Bring gives no price when the shop asks with customer number %s, so the shop asks for list prices instead. Ask your Bring contact for more information.', 'bring-fraktguiden-for-woocommerce'),
			$customer_number
		);
	}
}
