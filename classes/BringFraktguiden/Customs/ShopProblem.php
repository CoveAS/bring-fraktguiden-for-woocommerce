<?php

namespace BringFraktguiden\Customs;

/**
 * A reason why a customs booking lacks data that the shop holds.
 *
 * These come from the shop settings, not from the order. Both customs rules
 * need the consent. Only an export needs the exporter number. See doc/export.md
 * and doc/nvit.md.
 *
 * The warning is a warning only. Bring holds the real guard.
 */
enum ShopProblem
{
	/**
	 * The shop has not confirmed that the customs data is correct.
	 */
	case MissingConsent;

	/**
	 * The shop has no exporter number, such as a VAT or an EORI number.
	 *
	 * No settings screen writes this value yet, so every export order reports it.
	 */
	case MissingExporter;

	/**
	 * Return the message a shop reads.
	 */
	public function message(): string
	{
		return match ($this) {
			self::MissingConsent  => __('The shop must confirm the customs data in the booking settings.', 'bring-fraktguiden-for-woocommerce'),
			self::MissingExporter => __('The shop needs an exporter number.', 'bring-fraktguiden-for-woocommerce'),
		};
	}
}
