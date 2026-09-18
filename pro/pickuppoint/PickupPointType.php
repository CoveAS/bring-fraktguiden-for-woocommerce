<?php

namespace BringFraktguidenPro\PickUpPoint;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguiden\Settings\Settings as BringSettings;

/**
 * The pickup point type each service offers.
 *
 * The Bring API marks every pickup point MANNED or LOCKER. A service names the
 * type it carries in config/services.php. Pakkeboks carries 'locker'.
 *
 * A locker service claims every locker point. A pickup point service without a
 * type of its own therefore offers manned points only while a locker service is
 * on. Without a locker service the shop setting decides.
 */
class PickupPointType {

	/**
	 * The prefix WooCommerce puts in front of a Bring product in a rate id.
	 */
	public const RATE_PREFIX = 'bring_fraktguiden:';

	/**
	 * The type one service offers.
	 *
	 * @param string $bring_product Bring product code.
	 *
	 * @return string 'manned', 'locker' or an empty string for both.
	 */
	public static function for_service( string $bring_product ): string {
		$services = self::services();
		$own      = $services[ strtoupper( $bring_product ) ]['pickuppoint_type'] ?? '';
		if ( $own ) {
			return $own;
		}

		foreach ( Fraktguiden_Helper::get_option( 'services' ) ?: [] as $enabled ) {
			if ( 'locker' === ( $services[ $enabled ]['pickuppoint_type'] ?? '' ) ) {
				return 'manned';
			}
		}

		$setting = BringSettings::instance()->pickup_point_types->value;

		return in_array( $setting, [ 'manned', 'locker' ], true ) ? $setting : '';
	}

	/**
	 * The type of every rate that shows a pickup point picker.
	 *
	 * @return array<string, string> Rate id to type.
	 */
	public static function for_rates(): array {
		$types = [];
		foreach ( Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services', true ) as $product => $service ) {
			if ( empty( $service->service_data['pickuppoint'] ) ) {
				continue;
			}
			if ( empty( $service->settings['pickup_point_cb'] ) ) {
				continue;
			}
			$types[ self::RATE_PREFIX . $product ] = self::for_service( (string) $product );
		}

		return $types;
	}

	/**
	 * The type the checkout asks the Bring API for.
	 *
	 * The checkout fetches one list for every rate. It asks for both types when
	 * the enabled services need both, and the browser then filters the list per
	 * rate.
	 */
	public static function fetch_type(): string {
		$types = array_unique( array_values( self::for_rates() ) );
		if ( 1 !== count( $types ) ) {
			return '';
		}

		return (string) reset( $types );
	}

	/**
	 * The session key that holds the point the customer chose for one type.
	 *
	 * @param string $type Pickup point type.
	 */
	public static function session_key( string $type ): string {
		// ponytail: the manned key keeps its old name, so a cart from before
		// Pakkeboks keeps the point it holds. Only the locker key is new.
		return 'locker' === $type
			? 'bring_fraktguiden_pick_up_point_locker'
			: 'bring_fraktguiden_pick_up_point';
	}

	/**
	 * Every service, by Bring product code.
	 *
	 * @return array<string, array>
	 */
	private static function services(): array {
		$flat = [];
		foreach ( Fraktguiden_Helper::get_services_data() as $group ) {
			foreach ( $group['services'] as $product => $service_data ) {
				$flat[ (string) $product ] = $service_data;
			}
		}

		return $flat;
	}
}
