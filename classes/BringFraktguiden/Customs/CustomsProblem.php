<?php

namespace BringFraktguiden\Customs;

/**
 * A reason why an order line cannot carry a customs declaration.
 *
 * Bring refuses a booking that lacks any of these. The shop must see the
 * reason before it books, not after Bring answers. See doc/customs.md.
 */
enum CustomsProblem
{
	/**
	 * The product has no HS code, and neither has its parent.
	 */
	case MissingHsCode;

	/**
	 * The product has no weight, so both customs weights are 0.
	 */
	case MissingWeight;

	/**
	 * The line is worth nothing. Customs asks for the value of the goods.
	 */
	case NoValue;

	/**
	 * Return the message a shop reads.
	 */
	public function message(): string
	{
		return match ($this) {
			self::MissingHsCode  => __('The product needs an HS code.', 'bring-fraktguiden-for-woocommerce'),
			self::MissingWeight  => __('The product needs a weight.', 'bring-fraktguiden-for-woocommerce'),
			self::NoValue        => __('The line needs a value above zero.', 'bring-fraktguiden-for-woocommerce'),
		};
	}
}
