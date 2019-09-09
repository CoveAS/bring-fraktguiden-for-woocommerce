<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Fraktguiden_Service class
 */
class Fraktguiden_Service {

	/**
	 * Key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * ID
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Service data
	 *
	 * @var array
	 */
	public $service_data;

	/**
	 * Custom price ID
	 *
	 * @var string
	 */
	public $custom_price_id;

	/**
	 * Custom price
	 *
	 * @var string
	 */
	public $custom_price;

	/**
	 * Custom name ID
	 *
	 * @var string
	 */
	public $custom_name_id;

	/**
	 * Custom name
	 *
	 * @var string
	 */
	public $custom_name;

	/**
	 * Customer number ID
	 *
	 * @var string
	 */
	public $customer_number_id;

	/**
	 * Customer number
	 *
	 * @var string
	 */
	public $customer_number;

	/**
	 * Free shipping ID
	 *
	 * @var string
	 */
	public $free_shipping_id;

	/**
	 * Free shipping
	 *
	 * @var string
	 */
	public $free_shipping;

	/**
	 * Free shipping treshold ID
	 *
	 * @var string
	 */
	public $free_shipping_threshold_id;

	/**
	 * Free shipping treshold
	 *
	 * @var string
	 */
	public $free_shipping_threshold;

	/**
	 * Construct.
	 *
	 * @param string $key             Key.
	 * @param array  $service_data    Service data.
	 * @param array  $service_options Service options.
	 */
	public function __construct( $key, $service_data, $service_options ) {

		$this->key          = $key;
		$this->id           = $service_options['field_key'] . '_' . $key;
		$this->service_data = $service_data;
		$selected           = $service_options['selected'];
		$this->enabled      = ! empty( $selected ) ? in_array( $key, $selected ) : false;

		// Custom names.
		$this->custom_name_id = "{$service_options['field_key']}_custom_names[$key]";
		$this->custom_name    = esc_html( @$service_options['custom_names'][ $key ] );

		// Custom prices.
		$this->custom_price_id = "{$service_options['field_key']}_custom_prices[$key]";
		$this->custom_price    = esc_html( @$service_options['custom_prices'][ $key ] );

		// Customer numbers.
		$this->customer_number_id = "{$service_options['field_key']}_customer_numbers[$key]";
		$this->customer_number    = esc_html( @$service_options['customer_numbers'][ $key ] );

		// Free shippings.
		$this->free_shipping_id = "{$service_options['field_key']}_free_shipping_checks[$key]";
		$this->free_shipping    = esc_html( @$service_options['free_shipping_checks'][ $key ] );

		// Shipping thresholds.
		$this->free_shipping_threshold_id = "{$service_options['field_key']}_free_shipping_thresholds[$key]";
		$this->free_shipping_threshold    = esc_html( @$service_options['free_shipping_thresholds'][ $key ] );

	}

	/**
	 * Apply when converting this object to a string.
	 *
	 * @return string
	 */
	public function __toString() {
		if ( $this->customer_number ) {
			return "{$this->key}:{$this->customer_number}";
		}

		return "{$this->key}";
	}

	/**
	 * All
	 *
	 * @param string  $field_key Field key.
	 * @param boolean $selected  Selected.
	 *
	 * @return array
	 */
	public static function all( $field_key, $selected = false ) {
		$services_data   = \Fraktguiden_Helper::get_services_data();
		$services        = [];
		$service_options = [
			'field_key'                => $field_key,
			'selected'                 => Fraktguiden_Helper::get_option( 'services' ),
			'custom_names'             => get_option( $field_key . '_custom_names' ),
			'customer_numbers'         => get_option( $field_key . '_customer_numbers' ),
			'custom_prices'            => get_option( $field_key . '_custom_prices' ),
			'free_shipping_checks'     => get_option( $field_key . '_free_shipping_checks' ),
			'free_shipping_thresholds' => get_option( $field_key . '_free_shipping_thresholds' ),
		];

		foreach ( $services_data as $service_group ) {
			foreach ( $service_group['services'] as $key => $service_data ) {
				if ( $selected && ! in_array( $key, $service_options['selected'] ) ) {
					continue;
				}

				$services[ $key ] = new Fraktguiden_Service( $key, $service_data, $service_options );
			}
		}

		return $services;
	}

	/**
	 * Get name by index
	 *
	 * @param string|int $index Index.
	 *
	 * @return string
	 */
	public function get_name_by_index( $index = '' ) {
		if ( empty( $this->service_data[ $index ] ) ) {
			// Return default name as fallback.
			return $this->service_data['productName'];
		}

		return $this->service_data[ $index ];
	}
}
