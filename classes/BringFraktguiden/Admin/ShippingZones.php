<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use WC_Shipping_Method_Bring;
use WC_Shipping_Zone;
use WC_Shipping_Zones;

/**
 * The shipping zones of the shop, and whether the Bring method sits in them.
 */
class ShippingZones
{
	/**
	 * Return one row per zone, as id, name and added.
	 *
	 * The last row is zone 0, the rest of the world. WooCommerce leaves it out
	 * of get_zones(), but a shop can add a method to it.
	 *
	 * @return array<int, array{id: int, name: string, added: bool}>
	 */
	public static function all(): array
	{
		if (! class_exists('WC_Shipping_Zones')) {
			return [];
		}

		$ids = array_column(WC_Shipping_Zones::get_zones(), 'id');
		$ids[] = 0;

		return array_map(
			fn($id) => self::row(WC_Shipping_Zones::get_zone($id)),
			$ids
		);
	}

	/** Is the Bring method in at least one zone? */
	public static function any_added(): bool
	{
		foreach (self::all() as $zone) {
			if ($zone['added']) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add the Bring method to a zone, unless the zone already holds it.
	 *
	 * @return bool True when the method was added.
	 */
	public static function add(int $id): bool
	{
		$zone = WC_Shipping_Zones::get_zone($id);

		if (! $zone || self::row($zone)['added']) {
			return false;
		}

		$zone->add_shipping_method(Fraktguiden_Helper::ID);
		$zone->save();

		return true;
	}

	/** @return array{id: int, name: string, added: bool} */
	private static function row(WC_Shipping_Zone $zone): array
	{
		$added = false;
		foreach ($zone->get_shipping_methods() as $method) {
			if ($method instanceof WC_Shipping_Method_Bring) {
				$added = true;
				break;
			}
		}

		return [
			'id'    => (int) $zone->get_id(),
			'name'  => $zone->get_zone_name(),
			'added' => $added,
		];
	}
}
