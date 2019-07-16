<?php

class Fraktguiden_Service {
	public $key;
	public $id;
	public $service_data;

	public $custom_price_id;
	public $custom_price;
	public $custom_name_id;
	public $custom_name;
	public $customer_number_id;
	public $customer_number;
	public $free_shipping_id;
	public $free_shipping;
	public $free_shipping_threshold_id;
	public $free_shipping_threshold;

	function __construct( $key, $service_data, $service_options ) {

		$this->key          = $key;
		$this->id           = $service_options['field_key'] . '_' . $key;
		$this->service_data = $service_data;
		$selected           = $service_options['selected'];
		$this->enabled      = ! empty( $selected ) ? in_array( $key, $selected ) : false;

		// Custom names
		$this->custom_name_id = "{$service_options['field_key']}_custom_names[$key]";
		$this->custom_name    = esc_html( @$service_options['custom_names'][ $key ] );

		// Custom prices
		$this->custom_price_id = "{$service_options['field_key']}_custom_prices[$key]";
		$this->custom_price    = esc_html( @$service_options['custom_prices'][ $key ] );

		// Customer No.
		$this->customer_number_id = "{$service_options['field_key']}_customer_numbers[$key]";
		$this->customer_number    = esc_html( @$service_options['customer_numbers'][ $key ] );

		// Free shipping
		$this->free_shipping_id = "{$service_options['field_key']}_free_shipping_checks[$key]";
		$this->free_shipping    = esc_html( @$service_options['free_shipping_checks'][ $key ] );

		// Shipping threshold
		$this->free_shipping_threshold_id = "{$service_options['field_key']}_free_shipping_thresholds[$key]";
		$this->free_shipping_threshold    = esc_html( @$service_options['free_shipping_thresholds'][ $key ] );

	}

	public function __toString() {
		if ( $this->customer_number ) {
			return $this->key . ':' . $this->customer_number;
		}
		return $this->key;
	}

	public static function all( $field_key ) {
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
		foreach ( $services_data as $group => $service_group )  {
			foreach ( $service_group['services'] as $key => $service_data ) {
				$services[$key] = new Fraktguiden_Service( $key, $service_data, $service_options );
			}
		}
		// echo "<pre>";var_dump( $services);die;
		return $services;
	}
}
