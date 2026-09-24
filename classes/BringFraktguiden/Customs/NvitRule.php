<?php

namespace BringFraktguiden\Customs;

/**
 * Whether a booking must carry NVIT transit data.
 *
 * The rule has three parts. Both ends of the shipment must be in Norway. The
 * postal codes decide whether the route leaves Norway. The service decides
 * whether the rule applies at all. See doc/nvit.md.
 */
class NvitRule
{
	/**
	 * Return whether a booking must carry transit data.
	 *
	 * Other countries also use 4 digit postal codes, so the postal codes alone
	 * do not prove a Norwegian route.
	 *
	 * @param string $from_country  The country code of the sender.
	 * @param string $from_postcode The postal code of the sender.
	 * @param string $to_country    The country code of the recipient.
	 * @param string $to_postcode   The postal code of the recipient.
	 * @param string $product       The Bring product, for example 5800 or MAIL.
	 */
	public static function requires_transit_data(
		string $from_country,
		string $from_postcode,
		string $to_country,
		string $to_postcode,
		string $product
	): bool {
		return 'NO' === strtoupper($from_country)
			&& 'NO' === strtoupper($to_country)
			&& NvitServices::is_covered($product)
			&& NvitPostalCodes::is_transit_route($from_postcode, $to_postcode);
	}
}
