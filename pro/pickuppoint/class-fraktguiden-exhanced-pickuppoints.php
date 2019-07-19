<?php
/**
 * This file contains Bring_Pick_Up_Point_Enhancements class
 *
 * @package Bring_Fraktguiden\Bring_Pick_Up_Point_Enhancements
 */

/**
 * Pull meta data from Bring shipping rates
 */
class Bring_Pick_Up_Point_Enhancements {

	/**
	 * Load meta into action in order to display
	 */
	public static function setup() {
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::pickup_point_meta_opening', 1, 2 );
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::opening_hours', 10, 2 );
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::address', 20, 2 );
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::googlemaps', 30, 2 );
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::distance_away', 40, 2 );
		add_action( 'woocommerce_after_shipping_rate', __CLASS__ . '::pickup_point_meta_closing', 100, 0 );

		// CSS Styles injected for testing purposes.
		add_action(
			'wp_head',
			function () {
				?>
				<style>
				.available_shipping_options ul {
					list-style: none;
					margin: 0;
					padding: 0;
				}

				.available_shipping_options ul li {
					padding: 1rem;
					border: 1px solid #d3d3d3;
					margin-bottom: 1rem;
					border-radius: 3px;
				}

				.available_shipping_options ul li label {
					margin-left: 0.5rem;
				}

				.bring_pickup_meta_block {
					display: block;
				}

				.bring_pickup_meta_label {
					padding: 0 1.1rem;
					display: block;
					opacity: 0.7;
				}
				</style>
				<?php
			},
			100
		);
	}

	/**
	 * [get_data parse the meta & return if empty]
	 *
	 * @param  object $method Shipping rate data.
	 * @return array meta-data from method as array
	 */
	public static function get_data( $method ) {
		$meta = $method->get_meta_data();

		if ( ! isset( $meta['pickup_point_data'] ) ) {
			return;
		}

		$data = $meta['pickup_point_data'];

		return $data;
	}


	/**
	 * [opening_hours from metadata]
	 *
	 * @param  object $method Shipping rate data.
	 * @param  int    $index  Index of the shipping rate.
	 * @return void
	 */
	public static function opening_hours( $method, $index ) {
		$data = self::get_data( $method, $index );

		if ( isset( $data['openingHoursNorwegian'] ) ) {
			echo apply_filters( 'bring_pick_up_point_opening_hours', '<span class="bring_pickup_meta_block bring_pick_up_point_opening_hours">' . $data['openingHoursNorwegian'] . '</span>' );
		}
	}

	/**
	 * [address  from metadata]
	 *
	 * @param  object $method Shipping rate data.
	 * @param  int    $index  Index of the shipping rate.
	 * @return void
	 */
	public static function address( $method, $index ) {
		$data = self::get_data( $method, $index );

		if ( isset( $data['visitingAddress'], $data['visitingPostalCode'], $data['visitingCity'] ) ) {
			$address = sprintf( '<span class="bring_pickup_meta_block bring_pick_up_point_address">%1$s, %2$s, %3$s</span>', $data['visitingAddress'], $data['visitingPostalCode'], $data['visitingCity'] );
			echo apply_filters( 'bring_pick_up_point_address', $address );
		}
	}

	/**
	 * [googlemaps  from metadata]
	 *
	 * @param  object $method Shipping rate data.
	 * @param  int    $index  Index of the shipping rate.
	 * @return void
	 */
	public static function googlemaps( $method, $index ) {
		$data = self::get_data( $method, $index );

		if ( isset( $data['googleMapsLink'] ) ) {
			$google_maps_link = sprintf( '<a class="bring_pickup_meta_block bring_pick_up_point_googlemap_link" ref="noopener" target="_blank" href="%1$s">%2$s</a>', $data['googleMapsLink'], __( 'View on map', 'bring_fraktguiden_pro' ) );
			echo apply_filters( 'bring_pick_up_point_googlemap_link', $google_maps_link );
		}
	}

	/**
	 * [distance_away  from metadata]
	 *
	 * @param  object $method Shipping rate data.
	 * @param  int    $index  Index of the shipping rate.
	 * @return void
	 */
	public static function distance_away( $method, $index ) {
		$data = self::get_data( $method, $index );
		if ( isset( $data['distanceInKm'] ) ) {
			echo apply_filters( 'bring_pick_up_point_distance_away', '<span class="bring_pickup_meta_block bring_pick_up_point_distance_away">' . $data['distanceInKm'] . 'km</span>' );
		}
	}

	/**
	 * [Wrap HTML in Label for pickup_point_meta_opening to allow clicking]
	 *
	 * @param  object $method Shipping rate data.
	 * @param  int    $index  Index of the shipping rate.
	 * @return void
	 */
	public static function pickup_point_meta_opening( $method, $index ) {
		printf( '<label class="bring_pickup_meta_label" for="shipping_method_%1$d_%2$s">', $index, sanitize_title( $method->id ) );
	}

	/**
	 * [pickup_point_meta_closing description]
	 *
	 * @return void
	 */
	public static function pickup_point_meta_closing() {
		echo '</label>';
	}
}

Bring_Pick_Up_Point_Enhancements::setup();
