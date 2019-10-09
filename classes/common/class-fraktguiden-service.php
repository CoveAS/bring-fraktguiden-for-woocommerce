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
	 * Custom price
	 *
	 * @var string
	 */
	public $custom_price;

	/**
	 * Custom price
	 *
	 * @var string
	 */
	public $custom_price_cb;

	/**
	 * Custom name
	 *
	 * @var string
	 */
	public $custom_name;

	/**
	 * Customer number
	 *
	 * @var string
	 */
	public $customer_number;

	/**
	 * Customer number
	 *
	 * @var string
	 */
	public $customer_number_cb;

	/**
	 * Free shipping
	 *
	 * @var string
	 */
	public $free_shipping;

	/**
	 * Free shipping treshold
	 *
	 * @var string
	 */
	public $free_shipping_cb;

	/**
	 * Construct
	 *
	 * @param string $service_key    Service key.
	 * @param string $bring_product  Bring product.
	 * @param array  $service_data   Service data.
	 * @param array  $service_option Service option.
	 */
	public function __construct( $service_key, $bring_product, $service_data, $service_option ) {
		$this->option_key         = "{$service_key}_options";
		$this->bring_product      = $bring_product;
		$this->service_data       = $service_data;
		$selected                 = Fraktguiden_Helper::get_option( 'services' );
		$this->enabled            = ! empty( $selected ) ? in_array( $bring_product, $selected, true ) : false;
		$this->custom_name        = esc_html( $service_option['custom_name'] ?? '' );
		$this->custom_price       = esc_html( $service_option['custom_price'] ?? '' );
		$this->custom_price_cb    = esc_html( $service_option['custom_price_cb'] ?? '' );
		$this->customer_number    = esc_html( $service_option['customer_number'] ?? '' );
		$this->customer_number_cb = esc_html( $service_option['customer_number_cb'] ?? '' );
		$this->free_shipping      = esc_html( $service_option['free_shipping'] ?? '' );
		$this->free_shipping_cb   = esc_html( $service_option['free_shipping_cb'] ?? '' );
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
	 * @param string  $service_key      Field key.
	 * @param boolean $only_selected  Only get selected services.
	 *
	 * @return array
	 */
	public static function all( $service_key, $only_selected = false ) {
		$selected         = \Fraktguiden_Helper::get_option( 'services' );
		$selected_post    = filter_input( INPUT_POST, $service_key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$services_data    = \Fraktguiden_Helper::get_services_data();
		$services         = [];
		$services_options = get_option( $service_key . '_options' );
		if ( ! empty( $selected_post ) ) {
			$selected = $selected_post;
		}
		if ( ! $services_options ) {
			$services_options = self::update_services_options( $service_key );
		}
		foreach ( $services_data as $service_group ) {
			foreach ( $service_group['services'] as $bring_product => $service_data ) {
				if ( $only_selected && ! in_array( $bring_product, $selected, true ) ) {
					continue;
				}
				$services[ $bring_product ] = new Fraktguiden_Service(
					$service_key,
					$bring_product,
					$service_data,
					$services_options[ $bring_product ] ?? []
				);
			}
		}

		return $services;
	}

	/**
	 * Update services options
	 *
	 * @param  string $service_key Field key.
	 * @return array             Services options.
	 */
	public static function update_services_options( $service_key ) {
		$service_name             = Fraktguiden_Helper::get_option( 'service_name' );
		$custom_names             = get_option( $service_key . '_custom_names' );
		$customer_numbers         = get_option( $service_key . '_customer_numbers' );
		$custom_prices            = get_option( $service_key . '_custom_prices' );
		$free_shipping_checks     = get_option( $service_key . '_free_shipping_checks' );
		$free_shipping_thresholds = get_option( $service_key . '_free_shipping_thresholds' );

		$updated_options = [];

		// Convert custom names.
		foreach ( $custom_names as $bring_product => $value ) {
			if ( ! trim( $value ) ) {
				continue;
			}
			if ( empty( $updated_options[ $bring_product ] ) ) {
				$updated_options[ $bring_product ] = [];
			}
			$updated_options[ $bring_product ]['custom_name'] = $value;
			if ( 'customname' !== strtolower( $service_name ) ) {
				continue;
			}
			$updated_options[ $bring_product ]['custom_name_cb'] = 'on';
		}

		// Convert custom prices.
		foreach ( $custom_prices as $bring_product => $value ) {
			if ( ! trim( $value ) ) {
				continue;
			}
			if ( empty( $updated_options[ $bring_product ] ) ) {
				$updated_options[ $bring_product ] = [];
			}
			$updated_options[ $bring_product ]['custom_price']    = $value;
			$updated_options[ $bring_product ]['custom_price_cb'] = 'on';
		}

		// Convert free shipping tresholds.
		foreach ( $free_shipping_thresholds as $bring_product => $value ) {
			if ( ! trim( $value ) ) {
				continue;
			}
			if ( empty( $updated_options[ $bring_product ] ) ) {
				$updated_options[ $bring_product ] = [];
			}
			$updated_options[ $bring_product ]['free_shipping'] = $value;
			if ( empty( $free_shipping_checks[ $bring_product ] ) ) {
				continue;
			}
			$updated_options[ $bring_product ]['free_shipping_cb'] = $free_shipping_checks[ $bring_product ];
		}

		return $updated_options;
	}

	/**
	 * Get name by index
	 *
	 * @param string|int $index Index.
	 *
	 * @return string
	 */
	public function process_post_data() {
		$result      = [];
		$post_fields = [
			'custom_name',
			'custom_price',
			'custom_price_cb',
			'customer_number',
			'customer_number_cb',
			'free_shipping',
			'free_shipping_cb',
		];
		foreach ( $post_fields as $post_field ) {
			$post_data = filter_input( INPUT_POST, $this->option_key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( ! isset( $post_data[ $this->bring_product ][ $post_field ] ) ) {
				if ( preg_match( '/_cb$/', $post_field ) && ! empty( $post_data[ $this->bring_product ] ) ) {
					// Checkboxes are not set when they are empty.
					continue;
				}
				if ( ! empty( $this->{$post_field} ) ) {
					// Keep existing data.
					$result[ $post_field ] = $this->{$post_field};
				}
				continue;
			}
			$this->{$post_field}   = $post_data[ $this->bring_product ][ $post_field ];
			$result[ $post_field ] = $this->{$post_field};
		}

		return $result;
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
