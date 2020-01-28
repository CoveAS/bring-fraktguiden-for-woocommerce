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
	 * Key
	 *
	 * @var string
	 */
	public $enabled = false;

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
	 * Service options
	 *
	 * @var string
	 */
	public $settings = [];

	/**
	 * Value Added Services
	 *
	 * @var Fraktguiden_VAS
	 */
	public $vas = null;

	/**
	 * Construct
	 *
	 * @param string $service_key    Service key.
	 * @param string $bring_product  Bring product.
	 * @param array  $service_data   Service data.
	 * @param array  $service_option Service option.
	 */
	public function __construct( $service_key, $bring_product, $service_data, $service_option ) {
		$this->option_key    = "{$service_key}_options";
		$this->bring_product = $bring_product;
		$this->service_data  = $service_data;
		$selected            = Fraktguiden_Helper::get_option( 'services' );
		$this->enabled       = ! empty( $selected ) ? in_array( $bring_product, $selected, true ) : false;
		$this->vas           = Bring_Fraktguiden\VAS::create_collection( $bring_product, $service_option );

		if ( $service_data['pickuppoint'] ) {
			$this->settings['pickup_point']    = esc_html( $service_option['pickup_point'] ?? '' );
			$this->settings['pickup_point_cb'] = esc_html( $service_option['pickup_point_cb'] ?? '' );
		}

		$this->settings['custom_name']        = esc_html( $service_option['custom_name'] ?? '' );
		$this->settings['custom_price']       = esc_html( $service_option['custom_price'] ?? '' );
		$this->settings['custom_price_cb']    = esc_html( $service_option['custom_price_cb'] ?? '' );
		$this->settings['customer_number']    = esc_html( $service_option['customer_number'] ?? '' );
		$this->settings['customer_number_cb'] = esc_html( $service_option['customer_number_cb'] ?? '' );
		$this->settings['free_shipping']      = esc_html( $service_option['free_shipping'] ?? '' );
		$this->settings['free_shipping_cb']   = esc_html( $service_option['free_shipping_cb'] ?? '' );
		$this->settings['additional_fee']     = esc_html( $service_option['additional_fee'] ?? '' );
		$this->settings['additional_fee_cb']  = esc_html( $service_option['additional_fee_cb'] ?? '' );
	}

	/**
	 * Apply when converting this object to a string.
	 *
	 * @return string
	 */
	public function __toString() {
		if ( ! empty( $this->settings['customer_number_cb'] ) && ! empty( $this->settings['customer_number'] ) ) {
			return "{$this->bring_product}:{$this->customer_number}";
		}

		return "{$this->bring_product}";
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
		if ( ! is_array( $selected ) ) {
			$selected = [];
		}
		if ( ! $services_options ) {
			$services_options = self::update_services_options( $service_key );
		}
		foreach ( $services_data as $service_group ) {
			foreach ( $service_group['services'] as $bring_product => $service_data ) {
				$bring_product = (string) $bring_product;
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
		$custom_names             = get_option( $service_key . '_custom_names' ) ?: [];
		$customer_numbers         = get_option( $service_key . '_customer_numbers' ) ?: [];
		$custom_prices            = get_option( $service_key . '_custom_prices' ) ?: [];
		$free_shipping_checks     = get_option( $service_key . '_free_shipping_checks' ) ?: [];
		$free_shipping_thresholds = get_option( $service_key . '_free_shipping_thresholds' ) ?: [];

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
		$post_fields = $this->get_setting_fields();
		$post_data = filter_input( INPUT_POST, $this->option_key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		foreach ( $post_fields as $post_field ) {
			if ( ! isset( $post_data[ $this->bring_product ][ $post_field ] ) ) {
				if ( preg_match( '/_cb$/', $post_field ) && ! empty( $post_data[ $this->bring_product ] ) ) {
					// Checkboxes are not set when they are empty.
					unset( $this->settings[ $post_field ] );
					continue;
				}
				if ( ! empty( $this->settings[ $post_field ] ) ) {
					// Keep existing data.
					$result[ $post_field ] = $this->settings[ $post_field ];
				}
				continue;
			}
			$this->settings[ $post_field ] = $post_data[ $this->bring_product ][ $post_field ];
		}

		return $this;
	}

	public function get_setting_fields() {
		$post_fields = [
			'pickup_point',
			'pickup_point_cb',
			'custom_name',
			'custom_price',
			'custom_price_cb',
			'customer_number',
			'customer_number_cb',
			'free_shipping',
			'free_shipping_cb',
			'additional_fee',
			'additional_fee_cb',
		];
		foreach ( $this->vas as $vas_service ) {
			$post_fields[] = "vas_{$vas_service->code}";
		}
		return $post_fields;
	}

	/**
	 * Get name by index
	 *
	 * @param string|int $index Index.
	 *
	 * @return string
	 */
	public function get_settings_array() {
		$result      = [];
		$post_fields = $this->get_setting_fields();
		foreach ( $post_fields as $post_field ) {
			if ( ! empty( $this->settings[ $post_field ] ) ) {
				$result[ $post_field ] = $this->settings[ $post_field ];
			}
		}
		return $result;
	}

	public function getUrlParam() {
		if ( ! empty( $this->settings['customer_number_cb'] ) && ! empty( $this->settings['customer_number'] ) ) {
			return "&product={$this->bring_product}:{$this->settings['customer_number']}";
		}
		if ( '3584' == $this->bring_product || '3570' == $this->bring_product ) {
			// Special mailbox rule.
			$customer_number = Fraktguiden_Helper::get_option( 'mybring_customer_number' );
			$customer_number = preg_replace( '/^[A-Z_\-0]+/', '', $customer_number );
			return "&product={$this->bring_product}:{$customer_number}";
		}

		return "&product={$this->bring_product}";
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
