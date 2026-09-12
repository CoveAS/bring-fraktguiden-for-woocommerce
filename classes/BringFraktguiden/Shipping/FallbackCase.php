<?php

namespace BringFraktguiden\Shipping;

/**
 * Why the checkout got no shipping rate from Bring.
 *
 * Each case names the settings that hold its fallback rate. The fallback
 * options page holds one block of settings per case.
 */
enum FallbackCase
{
	/** The cart holds more product lines than the shop allows. */
	case TooManyProducts;

	/** No package fits the Bring size and weight limits. */
	case GoodsDoNotFit;

	/** Bring did not answer, or answered in a way the plugin cannot read. */
	case NoAnswer;

	/**
	 * The shop holds no Mybring credentials, so the plugin asks Bring nothing.
	 *
	 * Bring answers every rate query with 401 without them, so the shop gets
	 * the same empty checkout as a broken connection.
	 */
	case NoCredentials;

	/**
	 * Bring answered and sells no service for this order.
	 *
	 * A price here promises a delivery nobody can make, so the checkout shows
	 * no shipping at all.
	 */
	case NoService;

	/** The customer has given no postal code yet. */
	case NoAddress;

	/**
	 * The settings that hold the fallback rate of this case.
	 *
	 * Returns null for a case that shows no rate at all.
	 *
	 * @return array{rate_id: string, price: string, label: string}|null
	 */
	public function settings(): ?array
	{
		return match ($this) {
			self::TooManyProducts => [
				'rate_id' => 'alt_flat_rate_id',
				'price'   => 'alt_flat_rate',
				'label'   => 'alt_flat_rate_label',
			],
			self::GoodsDoNotFit => [
				'rate_id' => 'exception_rate_id',
				'price'   => 'exception_flat_rate',
				'label'   => 'exception_flat_rate_label',
			],
			self::NoAnswer, self::NoCredentials => [
				'rate_id' => 'no_connection_rate_id',
				'price'   => 'no_connection_flat_rate',
				'label'   => 'no_connection_flat_rate_label',
			],
			self::NoService, self::NoAddress => null,
		};
	}

	/** The line the shipping test shows the shop owner. */
	public function reason(): string
	{
		return match ($this) {
			self::TooManyProducts => __('The cart holds more product lines than the plugin sends to Bring.', 'bring-fraktguiden-for-woocommerce'),
			self::GoodsDoNotFit   => __('The goods do not fit the Bring size and weight limits.', 'bring-fraktguiden-for-woocommerce'),
			self::NoAnswer        => __('Bring did not answer.', 'bring-fraktguiden-for-woocommerce'),
			self::NoCredentials   => __('The shop has no Bring API credentials.', 'bring-fraktguiden-for-woocommerce'),
			self::NoService       => __('Bring sells no service for this address.', 'bring-fraktguiden-for-woocommerce'),
			self::NoAddress       => __('Fill in a post code.', 'bring-fraktguiden-for-woocommerce'),
		};
	}

	/** The line the log writes when this case ends the calculation. */
	public function trace(): string
	{
		return match ($this) {
			self::TooManyProducts => 'The cart holds more products than the maximum product limit.',
			self::GoodsDoNotFit   => 'No package fits the Bring size and weight limits.',
			self::NoAnswer        => 'Bring did not answer.',
			self::NoCredentials   => 'The shop has no Bring API credentials.',
			self::NoService       => 'Bring sells no service for this order.',
			self::NoAddress       => 'The customer has given no postal code.',
		};
	}
}
