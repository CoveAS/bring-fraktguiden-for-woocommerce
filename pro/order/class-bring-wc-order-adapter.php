<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Order;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Consignment\Bring_Consignment;
use Exception;
use WC_Order;
use WC_Shipping_Zones;
use WP_Bring_Request;
use WP_Bring_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_WC_Order_Adapter class
 *
 * Wraps an WC_Order and adds Bring related methods
 */
class Bring_WC_Order_Adapter {

	/**
	 * Order
	 *
	 * @var WC_Order|null
	 */
	public        $order = null;
	public        $shipping_method = null;
	public        $bring_product = null;
	public mixed $shipping_item = null;

	/**
	 * Construct
	 *
	 * @param WC_Order $order Order.
	 */
	public function __construct( $order ) {
		$this->order    = $order;
		$shipping_items = $this->get_fraktguiden_shipping_items();
		$shipping_item  = reset( $shipping_items );
		if ( $shipping_item ) {
			$instance_id           = $shipping_item->get_instance_id();
			$this->shipping_item   = $shipping_item;
			$this->shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );
			$this->bring_product   = $shipping_item->get_meta( 'bring_product' );
		}
	}

	/**
	 * Returns true if the order is booked.
	 *
	 * @return bool
	 */
	public function is_booked() {
		return $this->has_booking_consignments();
	}

	/**
	 * Saves the booking response to the order.
	 *
	 * @param WP_Bring_Response $response Bring response.
	 */
	public function update_booking_response( $response ) {
		// Create an array of the response for post meta.
		$response_as_array = $response->to_array();
		update_post_meta( $this->order->get_id(), '_bring_booking_response', $response_as_array );
	}

	/**
	 * Returns the saved booking response array.
	 *
	 * @return array
	 */
	public function get_booking_response() {
		if ( empty( $this->order ) ) {
			return false;
		}

		return get_post_meta( $this->order->get_id(), '_bring_booking_response', true );
	}

	/**
	 * Returns the consignments json decoded from the stored Mybring response.
	 * If the saved response has errors, return empty array.
	 *
	 * @return array
	 */
	public function get_booking_consignments() {
		$response = $this->get_booking_response();

		if ( empty( $response ) ) {
			return [];
		}

		return Bring_Consignment::create_from_response( $response, $this->order->get_id() );
	}

	/**
	 * Returns the consignments json decoded from the stored Mybring response.
	 * If the saved response has errors, return empty array.
	 *
	 * @return array
	 */
	public function get_mailbox_consignments() {
		$response = $this->get_booking_response();

		if ( ! $response || $this->has_booking_errors() ) {
			return [];
		}

		$body = json_decode( $response['body'] );

		return $body->data;
	}

	/**
	 * Returns the consignments json decoded from the stored Mybring response.
	 * If the saved response has errors, return empty array.
	 *
	 * @return array
	 */
	public function get_consignment_type() {
		$response = $this->get_booking_response();

		if ( ! $response || $this->has_booking_errors() ) {
			return '';
		}

		$body = json_decode( $response['body'] );

		return property_exists( $body, 'data' ) ? 'mailbox' : 'booking';
	}

	/**
	 * Creates an array of all errors from a response.
	 *
	 * @return array
	 */
	public function get_booking_errors() {
		$result = [];

		$response = $this->get_booking_response();

		// Add bring specific errors.
		$body = json_decode( $response['body'] );

		if ( $body && property_exists( $body, 'consignments' ) ) {
			foreach ( $body->consignments as $consignment ) {
				if ( ! property_exists( $consignment, 'errors' ) ) {
					continue;
				}

				foreach ( $consignment->errors as $error ) {
					$code = $error->code;

					foreach ( $error->messages as $message ) {
						$result[] = $code . ': ' . $message->message;
					}
				}
			}
		}

		// Add errors from the response errors array.
		foreach ( $response['errors'] as $error ) {
			$result[] = $error;
		}

		// Add any non-ok body to the error array because it contains the explanation
		// eg. status_code = 400 has [ 'body' => string 'Authentication failed...' ].
		if ( 200 != $response['status_code'] ) {
			$result[] = $response['body'];
		}

		return $result;
	}

	/**
	 * Returns true if the order has booking consignments.
	 *
	 * @return bool
	 */
	public function has_booking_consignments() {
		if ( 'mailbox' === $this->get_consignment_type() ) {
			return ! empty( $this->get_mailbox_consignments() );
		}

		return ! empty( $this->get_booking_consignments() );
	}

	/**
	 * Returns true if there are any errors in the booking response.
	 *
	 * @return bool
	 */
	public function has_booking_errors() {
		$response = $this->get_booking_response();

		if ( ! $response ) {
			return false;
		}

		if ( ! in_array( $response['status_code'], [ 200, 201, 202, 203, 204 ] ) ) {
			return true;
		}

		$body = json_decode( $response['body'] );

		if ( property_exists( $body, 'consignments' ) ) {
			foreach ( $body->consignments as $consignment ) {
				if ( ! empty( $consignment->errors ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns true if the order has Bring shipping methods.
	 *
	 * @return bool
	 */
	public function has_bring_shipping_methods() {
		return ! empty( $this->get_fraktguiden_shipping_items() );
	}

	/**
	 * Check meta key
	 *
	 * @param array $array Array.
	 * @param string|int $key Key.
	 *
	 * @return bool
	 */
	public static function check_meta_key( $array, $key ) {
		if ( empty( $array[ $key ] ) ) {
			return false;
		}

		if ( ! $array[ $key ][0] ) {
			return false;
		}

		return true;
	}

	/**
	 * Admin update pickup point
	 *
	 * @param array $shipping_items Order items to save.
	 */
	public function admin_update_pickup_point( $shipping_items ) {
		$shipping_methods = $shipping_items['shipping_method'];

		if ( ! $shipping_methods ) {
			return;
		}

		foreach ( $shipping_methods as $item_id => $shipping_method ) {
			// Get the shipping item.
			$items         = $this->order->get_items( 'shipping' );
			$shipping_item = false;

			foreach ( $items as $shipping_item_id => $item ) {
				if ( $item_id == $shipping_item_id ) {
					$shipping_item = $item;
				}
			}

			if ( ! $shipping_item ) {
				continue;
			}

			if ( strpos( $shipping_method, Fraktguiden_Helper::ID ) === false ) {
				$shipping_item->delete_meta_data( '_fraktguiden_pickup_point_postcode' );
				$shipping_item->delete_meta_data( '_fraktguiden_pickup_point_id' );
				$shipping_item->delete_meta_data( '_fraktguiden_pickup_point_info_cached' );
				$shipping_item->save_meta_data();

				continue;
			}

			$pickup_point_id = [];

			if ( ! empty( $shipping_items['_fraktguiden_services'][ $item_id ] ) ) {
				$shipping_item->update_meta_data( 'bring_product', $shipping_items['_fraktguiden_services'][ $item_id ] );
			}

			if ( isset( $shipping_items['_fraktguiden_pickup_point_id'] ) ) {
				$pickup_point_id = $shipping_items['_fraktguiden_pickup_point_id'][ $item_id ];
			}

			if ( isset( $shipping_items['_fraktguiden_packages'] ) ) {
				$shipping_items['_fraktguiden_packages'][ $item_id ];
			}

			if ( ! empty( $pickup_point_id ) ) {
				$pickup_point_postcode = $shipping_items['_fraktguiden_pickup_point_postcode'][ $item_id ];
				$pickup_point_info     = $shipping_items['_fraktguiden_pickup_point_info_cached'][ $item_id ];
				$shipping_item->update_meta_data( 'pickup_point_id', $pickup_point_id );
				$shipping_item->update_meta_data( '_fraktguiden_pickup_point_id', $pickup_point_id );
				$shipping_item->update_meta_data( '_fraktguiden_pickup_point_postcode', $pickup_point_postcode );
				$shipping_item->update_meta_data( '_fraktguiden_pickup_point_info_cached', $pickup_point_info );
			} else {
				$shipping_item->delete_meta_data( '_fraktguiden_pickup_point_postcode' );
				$shipping_item->delete_meta_data( '_fraktguiden_pickup_point_id' );
				$shipping_item->delete_meta_data( '_fraktguiden_pickup_point_info_cached' );
			}

			$shipping_item->save_meta_data();
		}
	}

	/**
	 * Get shipping data
	 *
	 * @return array
	 */
	public function get_shipping_data() {
		$data = [];

		foreach ( $this->get_fraktguiden_shipping_items() as $item_id => $method ) {
			$pickup_point_id = $method->get_meta( 'pickup_point_id' );
			$pickup_point    = null;

			if ( $pickup_point_id ) {
				$shipping_address = $this->order->get_address( 'shipping' );

				$country = filter_input( INPUT_GET, 'country' );

				if ( empty( $country ) ) {
					$country = $shipping_address['country'];
				}

				$request      = new WP_Bring_Request();
				$response     = $request->get( 'https://api.bring.com/pickuppoint/api/pickuppoint/' . $country . '/id/' . $pickup_point_id . '.json' );
				$pickup_point = $response->has_errors() ? null : json_decode( $response->get_body() )->pickupPoint[0];
			}

			$data[] = [
				'item_id'      => $item_id,
				'pickup_point' => $pickup_point,
				'packages'     => wp_json_encode( $method->get_meta( '_fraktguiden_packages' ) ),
			];
		}

		return $data;
	}

	/**
	 * Returns Fraktguiden shipping method items.
	 *
	 * Same as wc_order->get_shipping_methods() except that non-bring methods are filtered away.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_fraktguiden_shipping_items() {
		$result = [];

		$shipping_methods = $this->order->get_shipping_methods();

		if (
			filter_var(
				Fraktguiden_Helper::get_option( 'booking_without_bring' ),
				FILTER_VALIDATE_BOOLEAN
			)
		) {
			return $shipping_methods;
		}

		foreach ( $shipping_methods as $item_id => $shipping_item ) {
			$method_id = wc_get_order_item_meta( $item_id, 'method_id', true );

			if (str_contains($method_id, Fraktguiden_Helper::ID)) {
				$result[ $item_id ] = $shipping_item;
			}
		}

		return $result;
	}

}
