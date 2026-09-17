<?php

namespace BringFraktguiden\Admin;

use WC_Shipping_Free_Shipping;
use WC_Shipping_Zone;
use WC_Shipping_Zones;

/**
 * A shop that offers free shipping and nothing else.
 *
 * Such a shop has one zone, that zone holds only free shipping, and the rest
 * of the world holds no method at all. The setup page offers to delete the
 * free shipping, so a customer sees the Bring options.
 */
class FreeShippingOnly
{
	/** The form field that asks to delete the free shipping. */
	public const FIELD = 'bfg-delete-free-shipping';

	/** Does the shop offer free shipping and nothing else? */
	public static function exists(): bool
	{
		return self::zone() !== null;
	}

	/**
	 * Delete every free shipping method of the shop.
	 *
	 * The method does nothing unless the shop offers free shipping and nothing
	 * else. Call it before the Bring method is added. The Bring method is a
	 * second option, and a second option ends the rule.
	 */
	public static function delete(): void
	{
		$zone = self::zone();

		if (! $zone) {
			return;
		}

		foreach ($zone->get_shipping_methods() as $instance_id => $method) {
			if ($method instanceof WC_Shipping_Free_Shipping) {
				$zone->delete_shipping_method((int) $instance_id);
			}
		}
	}

	/** The one zone that holds only free shipping, or null. */
	private static function zone(): ?WC_Shipping_Zone
	{
		if (! class_exists('WC_Shipping_Zones')) {
			return null;
		}

		$zones = WC_Shipping_Zones::get_zones();

		if (count($zones) !== 1) {
			return null;
		}

		if (WC_Shipping_Zones::get_zone(0)->get_shipping_methods()) {
			return null;
		}

		$zone = WC_Shipping_Zones::get_zone((int) reset($zones)['id']);
		$methods = $zone ? $zone->get_shipping_methods() : [];

		if (! $methods) {
			return null;
		}

		foreach ($methods as $method) {
			if (! $method instanceof WC_Shipping_Free_Shipping) {
				return null;
			}
		}

		return $zone;
	}
}
