<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\PickUpPoint;

defined( 'ABSPATH' ) || exit;

/**
 * Pull meta data from Bring shipping rates
 */
class LegacyPickUpPointEnhancement {

	/**
	 * Load meta into action in order to display
	 *
	 * @return void
	 */
	public static function setup() {
		add_action( 'woocommerce_after_shipping_rate', [ __CLASS__, 'add_opening_tag' ], 10, 2 );
		add_action( 'woocommerce_after_shipping_rate', [ __CLASS__, 'add_opening_hours' ], 20, 1 );
		add_action( 'woocommerce_after_shipping_rate', [ __CLASS__, 'add_address' ], 30, 1 );
		add_action( 'woocommerce_after_shipping_rate', [ __CLASS__, 'add_location_on_map' ], 40, 1 );
		add_action( 'woocommerce_after_shipping_rate', [ __CLASS__, 'add_distance_away' ], 50, 1 );
		add_action( 'woocommerce_after_shipping_rate', [ __CLASS__, 'add_closing_tag' ], 60, 0 );
	}

	/**
	 * Find the best Bring language match for the current locale
	 *
	 * @return string
	 */
	protected static function get_language() {

		$locale = get_locale();

		$languages = [
			'nb_NO' => 'Norwegian',
			'nn_NO' => 'Norwegian',
			'fi_FI' => 'Finnish',
			'da_DK' => 'Danish',
			'sv_SE' => 'Swedish',
		];

		if ( ! array_key_exists( $locale, $languages ) ) {
			return 'English';
		}

		return $languages[ $locale ];
	}

	/**
	 * Get pick-up point data from WC_Shipping_Rate object
	 *
	 * @param object $method WC_Shipping_Rate.
	 *
	 * @return array Pick-up point data from meta data.
	 */
	public static function get_pickup_point_data( $method ) {

		$meta_data = $method->get_meta_data();

		if ( ! isset( $meta_data['pickup_point_data'] ) ) {
			return null;
		}

		$pickup_point_data = $meta_data['pickup_point_data'];

		return $pickup_point_data;
	}

	/**
	 * Add opening hours to a full label
	 *
	 * @param object $method WC_Shipping_Rate.
	 *
	 * @return void
	 */
	public static function add_opening_hours( $method ) {

		$data = self::get_pickup_point_data( $method );

		if ( ! isset( $data[ 'openingHours' . self::get_language() ] ) ) {
			return;
		}

		$opening_hours = sprintf( '<span class="bring_pickup_point bring_pickup_point_opening_hours">%1$s</span>', esc_html( $data[ 'openingHours' . self::get_language() ] ) );

		echo apply_filters( 'bring_pickup_point_opening_hours', $opening_hours ); // phpcs:ignore
	}

	/**
	 * Add address to a full label
	 *
	 * @param object $method WC_Shipping_Rate.
	 *
	 * @return void
	 */
	public static function add_address( $method ) {

		$data = self::get_pickup_point_data( $method );

		if ( ! isset( $data['visitingAddress'], $data['visitingPostalCode'], $data['visitingCity'] ) ) {
			return;
		}

		$address = sprintf( '<span class="bring_pickup_point bring_pickup_point_address">%1$s, %2$s, %3$s</span>', esc_html( $data['visitingAddress'] ), esc_html( $data['visitingPostalCode'] ), esc_html( $data['visitingCity'] ) );

		echo apply_filters( 'bring_pickup_point_address', $address ); // phpcs:ignore
	}

	/**
	 * Add location of the pick-up point on the map to a full label
	 *
	 * @param object $method WC_Shipping_Rate.
	 *
	 * @return void
	 */
	public static function add_location_on_map( $method ) {

		$data = self::get_pickup_point_data( $method );

		if ( ! isset( $data['googleMapsLink'] ) ) {
			return;
		}

		$map_url    = $data['googleMapsLink'];
		$map_label  = __( 'View on map', 'bring-fraktguiden-for-woocommerce' );
		$map_markup = sprintf( '<span class="bring_pickup_point bring_pickup_point_location_on_map"><a href="%1$s" target="_blank" ref="noopener">%2$s</a></span>', esc_attr( $map_url ), esc_html( $map_label ) );

		echo apply_filters( 'bring_pickup_point_location_on_map', $map_markup, $map_url, $map_label ); // phpcs:ignore
	}

	/**
	 * Add distance away from the pick-up point to a full label
	 *
	 * @param object $method WC_Shipping_Rate.
	 *
	 * @return string
	 */
	public static function add_distance_away( $method ) {

		$data = self::get_pickup_point_data( $method );

		if ( ! isset( $data['distanceInKm'] ) ) {
			return;
		}

		echo apply_filters( 'bring_pickup_point_distance_away', '<span class="bring_pickup_point bring_pickup_point_distance_away">' . esc_html( $data['distanceInKm'] ) . 'km</span>' ); // phpcs:ignore
	}

	/**
	 * Add opening label tag
	 *
	 * @param object $method WC_Shipping_Rate.
	 * @param string $index  Index.
	 *
	 * @return void
	 */
	public static function add_opening_tag( $method, $index ) {

		printf( '<label for="shipping_method_%1$s_%2$s">', $index, esc_attr( sanitize_title( $method->id ) ) ); // phpcs:ignore
	}

	/**
	 * Add closing label tag
	 *
	 * @return void
	 */
	public static function add_closing_tag() {

		echo '</label>';
	}
}
