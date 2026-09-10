<?php

namespace BringFraktguiden\Customs;

use Fraktguiden_Helper;

/**
 * The service part of the NVIT rule.
 *
 * Bring asks for transit data on every service, except letters and express
 * services that travel by air. Such a service carries 'nvit' => false in
 * config/services.php. See doc/nvit.md.
 */
class NvitServices
{
	/**
	 * The key that marks a service as outside the rule.
	 */
	private const FLAG = 'nvit';

	/**
	 * Return whether the NVIT rule covers a Bring service.
	 *
	 * A service the plugin does not know is covered, because the rule names
	 * every service and lists the exceptions.
	 *
	 * @param string $product The Bring product, for example 5800 or MAIL.
	 */
	public static function is_covered(string $product): bool
	{
		$product = strtoupper($product);

		foreach (Fraktguiden_Helper::get_services_data() as $group) {
			$service = $group['services'][$product] ?? null;

			if ($service) {
				return false !== ($service[self::FLAG] ?? true);
			}
		}

		return true;
	}
}
