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
	 * Return the message a shop reads, with a link to the setting that fixes it.
	 */
	public function message(): string
	{
		return match ($this) {
			self::MissingConsent  => sprintf(
				/* translators: %1$s and %2$s are the open and close tags of a link to the booking settings. */
				__('The shop must confirm the customs data in the %1$sbooking settings%2$s.', 'bring-fraktguiden-for-woocommerce'),
				...self::settings_link()
			),
			self::MissingExporter => sprintf(
				/* translators: %1$s and %2$s are the open and close tags of a link to the booking settings. */
				__('The shop needs an %1$sexporter number%2$s.', 'bring-fraktguiden-for-woocommerce'),
				...self::settings_link()
			),
		};
	}

	/**
	 * Return the open and the close tag of a link to the booking settings.
	 *
	 * A shop worker without the rights sees no link, so both tags are empty.
	 *
	 * @return array<int, string>
	 */
	private static function settings_link(): array
	{
		if (!current_user_can('manage_options')) {
			return ['', ''];
		}

		return [
			'<a href="' . esc_url(admin_url('admin.php?page=bring_fraktguiden_booking')) . '" target="_blank" rel="noopener">',
			'</a>',
		];
	}
}
