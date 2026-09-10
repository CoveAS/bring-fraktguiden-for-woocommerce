<?php

namespace BringFraktguiden\Customs;

/**
 * Whether a booking must carry NVIT transit data.
 *
 * The rule has two parts. The postal codes decide whether the route leaves
 * Norway. The service decides whether the rule applies at all. See doc/nvit.md.
 */
class NvitRule
{
	/**
	 * Return whether a booking must carry transit data.
	 *
	 * @param string $from    The postal code of the sender.
	 * @param string $to      The postal code of the recipient.
	 * @param string $product The Bring product, for example 5800 or MAIL.
	 */
	public static function requires_transit_data(string $from, string $to, string $product): bool
	{
		return NvitServices::is_covered($product)
			&& NvitPostalCodes::is_transit_route($from, $to);
	}
}
