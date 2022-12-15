<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden;

use Bring_Fraktguiden;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Updater class
 */
class Updater {

	/**
	 * Setup.
	 */
	public static function setup( $service_key ) {
		$installed_version = Fraktguiden_Helper::get_option( 'version_updater' );

		if ( $installed_version == Bring_Fraktguiden::VERSION ) {
			return;
		}

		if ( 0 < version_compare( '1.6.8', $installed_version ) ) {
			// do something...
			if ( 'yes' === Fraktguiden_Helper::get_option( 'pickup_point_enabled' ) ) {
				$limit =  Fraktguiden_Helper::get_option( 'pickup_point_limit' );

				$services_data    = Fraktguiden_Helper::get_services_data();
				$services         = [];
				$services_options = get_option( $service_key . '_options' );

				if ( ! empty( $selected_post ) ) {
					$selected = $selected_post;
				}
				if ( ! $services_options ) {
					$services_options = \Fraktguiden_Service::update_services_options( $service_key );
				}
				foreach ( $services_data as $service_group ) {
					foreach ( $service_group['services'] as $bring_product => $service_data ) {
						if ( empty( $service_data['pickuppoint'] ) ) {
							continue;
						}
						// Only process options for enabled services.
						$service = new \Fraktguiden_Service(
							$service_key,
							$bring_product,
							$service_data,
							$services_options[ $bring_product ] ?? []
						);

						$services_options[ $bring_product ]                    = $service->get_settings_array();
						$services_options[ $bring_product ]['pickup_point_cb'] = 'yes';
						$services_options[ $bring_product ]['pickup_point']    = $limit;
					}
				}
				update_option( $service_key . '_options', $services_options );
			}
		}
		Fraktguiden_Helper::update_option( 'version_updater', \Bring_Fraktguiden::VERSION );
	}

	public static function updates_for_version( $version ) {
	}

}
