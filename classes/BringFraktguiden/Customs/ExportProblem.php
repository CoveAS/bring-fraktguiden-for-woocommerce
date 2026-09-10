<?php

namespace BringFraktguiden\Customs;

/**
 * A reason why an export booking lacks customs data.
 *
 * These come from the shop, not from the order. An export needs them on top of
 * the item line fields that NVIT also needs. See doc/export.md.
 *
 * No settings screen writes these values yet, so an export order reports all
 * three. The warning is a warning only. Bring holds the real guard.
 */
enum ExportProblem
{
	/**
	 * The shop has not confirmed that the customs data is correct.
	 */
	case MissingConsent;

	/**
	 * The shop has no exporter number, such as a VAT or an EORI number.
	 */
	case MissingExporter;

	/**
	 * The shop has not said why the goods move.
	 */
	case MissingCargoType;

	/**
	 * Return the message a shop reads.
	 */
	public function message(): string
	{
		return match ($this) {
			self::MissingConsent   => __('The shop must give customs consent.', 'bring-fraktguiden-for-woocommerce'),
			self::MissingExporter  => __('The shop needs an exporter number.', 'bring-fraktguiden-for-woocommerce'),
			self::MissingCargoType => __('The shop must say why the goods move.', 'bring-fraktguiden-for-woocommerce'),
		};
	}
}
