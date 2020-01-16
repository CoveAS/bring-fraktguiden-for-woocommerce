<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Consignment_Request class
 */
abstract class Bring_Consignment_Request {

	/**
	 * Service ID
	 *
	 * @var string
	 */
	public $service_id;

	/**
	 * Shipping item
	 *
	 * @var string
	 */
	public $shipping_item;

	/**
	 * Shipping date time
	 *
	 * @var string
	 */
	public $shipping_date_time;

	/**
	 * Customer number
	 *
	 * @var string
	 */
	public $customer_number;

	/**
	 * Adapter
	 *
	 * @var Bring_WC_Order_Adapter
	 */
	public $adapter;

	/**
	 * Construct
	 *
	 * @param string $shipping_item Shipping item.
	 */
	public function __construct( $shipping_item ) {
		$this->shipping_item = $shipping_item;
		$this->adapter       = new Bring_WC_Order_Adapter( $shipping_item->get_order() );
		$this->service_id    = $this->get_service_id();
	}

	/**
	 * Get Service ID
	 * Includes a fallback for older versions of bring
	 *
	 * @param boolean $force Force.
	 *
	 * @return string
	 */
	public function get_service_id( $force = false ) {
		if ( $this->service_id && ! $force ) {
			return $this->service_id;
		}

		$bring_product = self::get_bring_product( $this->shipping_item );

		return $bring_product;
	}

	/**
	 * Get Bring product
	 *
	 * @param string $shipping_item Shipping item.
	 *
	 * @return string
	 */
	public static function get_bring_product( $shipping_item ) {
		$bring_product = $shipping_item->get_meta( 'bring_product' );

		if ( ! $bring_product ) {
			if ( ! empty( $bring_product ) && ! is_array( $bring_product ) ) {
				return strtolower( $bring_product );
			}

			$method_id = $shipping_item->get_method_id();

			if ( ! preg_match( '/^bring_fraktguiden:([a-z\d_]+)(?:\-(\d+))?$/', $method_id, $matches ) ) {
				return strtolower( $bring_product );
			}

			$bring_product   = $matches[1];
			$pickup_point_id = isset( $matches[2] ) ? $matches[2] : false;
			$shipping_item->update_meta_data( 'bring_product', $bring_product );

			if ( $pickup_point_id ) {
				$shipping_item->update_meta_data( 'pickup_point_id', $pickup_point_id );
			}

			$shipping_item->save_meta_data();
		}

		return strtolower( $bring_product );
	}

	/**
	 * Create
	 *
	 * @param array $shipping_item Shipping item.
	 *
	 * @throws Exception Exception.
	 *
	 * @return Bring_Booking_Consignment_Request
	 */
	public static function create( $shipping_item ) {
		$service_id = self::get_bring_product( $shipping_item );

		if ( ! $service_id ) {
			throw new Exception( 'No bring product was found on the shipping method' );
		}

		if ( preg_match( '/^PAKKE_I_POSTKASSEN/', strtoupper( $service_id ) ) ) {
			return new Bring_Mailbox_Consignment_Request( $shipping_item );
		}

		return new Bring_Booking_Consignment_Request( $shipping_item );
	}

	/**
	 * Fill
	 *
	 * @param array $args Arguments.
	 *
	 * @return $this
	 */
	public function fill( $args ) {
		$this->customer_number    = $args['customer_number'];
		$this->shipping_date_time = $args['shipping_date_time'];

		if ( '3584' == $this->service_id || '3570' == $this->service_id ) {
			// Special mailbox rule.
			$this->customer_number = preg_replace( '/^[A-Z_\-0]+/', '', $args['customer_number'] );
		}

		return $this;
	}

	/**
	 * Get reference
	 *
	 * @return string
	 */
	public function get_reference() {
		$order     = $this->shipping_item->get_order();
		$reference = Fraktguiden_Helper::get_option( 'booking_address_reference' );

		return self::parse_sender_address_reference( $reference, $order );
	}

	/**
	 * Get sender
	 *
	 * @return array
	 */
	public function get_sender() {
		$form_fields = [
			'booking_address_store_name',
			'booking_address_street1',
			'booking_address_street2',
			'booking_address_postcode',
			'booking_address_city',
			'booking_address_country',
			'booking_address_contact_person',
			'booking_address_phone',
			'booking_address_email',
			'booking_address_reference',
		];

		// Load sender address data from options.
		$result = [];

		foreach ( $form_fields as $field ) {
			$result[ $field ] = Fraktguiden_Helper::get_option( $field );
		}

		return $result;
	}

	/**
	 * Parses the sender address reference value.
	 * Supports simple template macros.
	 *
	 * Eg. "Order: {order_id}" will be replace {order_id} with the order's ID
	 *
	 * Available macros:
	 *
	 *   {order_id}
	 *
	 * @param string   $reference Reference.
	 * @param WC_Order $wc_order  WC Order.
	 *
	 * @return mixed
	 */
	public static function parse_sender_address_reference( $reference, $wc_order ) {
		$replacements = array(
			'{order_id}' => $wc_order->get_id(),
		);

		$result = $reference;

		foreach ( $replacements as $replacement => $value ) {
			$result = preg_replace( '/' . preg_quote( $replacement ) . '/', $value, $result );
		}

		return $result;
	}

	/**
	 * Get endpoint URL
	 */
	abstract public function get_endpoint_url();

	/**
	 * Create data
	 */
	abstract public function create_data();

	/**
	 * Post
	 *
	 * @return WP_Bring_Response
	 */
	public function post() {
		$request_data = [
			'headers' => [
				'Content-Type'       => 'application/json',
				'Accept'             => 'application/json',
				'X-MyBring-API-Uid'  => Fraktguiden_Helper::get_option( 'mybring_api_uid' ),
				'X-MyBring-API-Key'  => Fraktguiden_Helper::get_option( 'mybring_api_key' ),
				'X-Bring-Client-URL' => Fraktguiden_Helper::get_client_url(),
			],
			'body'    => wp_json_encode( $this->create_data() ),
		];

		$request = new WP_Bring_Request();

		return $request->post( $this->get_endpoint_url(), [], $request_data );
	}

	/**
	 * Order update packages
	 *
	 * @return mixed
	 */
	public function order_update_packages() {
		$wc_order = $this->shipping_item->get_order();
		$cart     = [];

		// Build a cart like array.
		foreach ( $wc_order->get_items() as $item_id => $item ) {
			if ( ! isset( $item['product_id'] ) ) {
				continue;
			}

			if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
				$product = wc_get_product( $item['variation_id'] );
			} else {
				$product = wc_get_product( $item['product_id'] );
			}

			$cart[] = [
				'data'     => $product,
				'quantity' => $item['qty'],
			];
		}

		$shipping_method = new WC_Shipping_Method_Bring();
		$packages        = $shipping_method->pack_order( $cart );

		if ( ! $packages ) {
			return;
		}

		$shipping_methods = $wc_order->get_shipping_methods();
		$this->shipping_item->update_meta_data( '_fraktguiden_packages', $packages, 1 );
		$this->shipping_item->save_meta_data();

		return $packages;
	}
}
