<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Consignment\Bring_Consignment;
use BringFraktguidenPro\Booking\Consignment_Request\Bring_Booking_Consignment_Request;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Labels;
use BringFraktguidenPro\Booking\Views\Bring_Booking_My_Order_View;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Order_View;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Orders_View;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use Exception;
use WC_Admin_List_Table_Orders;
use WC_Logger;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Frontend views.
add_filter( 'woocommerce_order_shipping_to_display', [Bring_Booking_My_Order_View::class,'order_display_tracking_info'], 5, 2 );

// Register awaiting shipment status.
add_action( 'init', [Bring_Booking::class, 'register_awaiting_shipment_order_status'] );

// Add awaiting shipping to existing order statuses.
add_filter( 'wc_order_statuses', [Bring_Booking::class, 'add_awaiting_shipment_status'] );

/**
 * Bring_Booking class
 */
class Bring_Booking {

	const ID          = Fraktguiden_Helper::ID;
	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! self::is_valid_for_use() ) {
			return;
		}

		Bring_Booking_Orders_View::init();
		Bring_Booking_Order_View::init();

		// Update status on printed orders
		add_action( 'init', __CLASS__ . '::update_printed_orders' );

		// Create a menu item for PDF download.
		add_action( 'woocommerce_after_register_post_type', [ Bring_Booking_Labels::class, 'open_pdfs' ] );
	}

	/**
	 * Check if API UID and key are valid
	 *
	 * @return bool
	 */
	public static function is_valid_for_use() {
		$api_uid = self::get_api_uid();
		$api_key = self::get_api_key();

		return $api_uid && $api_key;
	}
	/**
	 * Change the status on printed orders
	 */
	public static function update_printed_orders() {
		// Create new status and order note.
		$status = Fraktguiden_Helper::get_option( 'auto_set_status_after_print_label_success' );
		$printed_orders = Fraktguiden_Helper::get_option( 'printed_orders' );

		if ( empty( $printed_orders ) ) {
			return;
		}
		if ( 'none' === $status || empty( $status ) ) {
			return;
		}
		foreach ($printed_orders as $order_id) {
			$order = wc_get_order( $order_id );
			if ( ! $order || is_wp_error( $order ) ) {
				continue;
			}
			if ( $status === $order->get_status() ) {
				continue;
			}
			// Do not change status if the order does not use fraktguiden shipping.
			$adapter = new Bring_WC_Order_Adapter( $order );
			if ( ! $adapter->has_bring_shipping_methods() ) {
				continue;
			}
			// Update status.
			$order->update_status(
				$status,
				__( 'Changing status because the label was downloaded.', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL
			);
		}
		Fraktguiden_Helper::update_option( 'printed_orders', [] );
	}

	/**
	 * Register awaiting shipment order status.
	 */
	public static function register_awaiting_shipment_order_status() {
		// Be careful changing the post status name.
		// If orders has this status they will not be available in admin.
		register_post_status(
			'wc-bring-shipment',
			array(
				'label'                     => __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Number of awaiting shipments */
				'label_count'               => _n_noop( __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' ) . ' <span class="count">(%s)</span>', __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' ) . ' <span class="count">(%s)</span>' ),
			)
		);
	}

	/**
	 * Add awaiting shipment to order statuses.
	 *
	 * @param array $order_statuses Order statuses.
	 * @return array
	 */
	public static function add_awaiting_shipment_status( $order_statuses ) {
		$new_order_statuses = [];

		// Add the order status after processing.
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;

			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-bring-shipment'] = __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' );
			}
		}

		return $new_order_statuses;
	}

	/**
	 * Send booking
	 *
	 * @param WC_Order|Bring_WC_Order_Adapter $wc_order WooCommerce order.
	 */
	public static function send_booking( $wc_order, $bulk_mode = false ) {
		$adapter = $wc_order;
		if ( $wc_order instanceof WC_Order ) {
			$adapter = new Bring_WC_Order_Adapter( $adapter );
		} else {
			$wc_order = $adapter->order;
		}
		// Get booking count
		$count    = get_option( 'bring_fraktguiden_booking_count', [] );
		$date_utc = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$date_now = (int) $date_utc->format( 'Ymd' );

		if ( ! is_array( $count ) ) {
			$count = [];
		}

		// Bring_WC_Order_Adapter.
		$customer_number = (string) filter_input( Fraktguiden_Helper::get_input_request_method(), '_bring-customer-number' );
		if (! $customer_number) {
			$customer_number = Fraktguiden_Helper::get_option( 'mybring_customer_number' );
		}

		// One booking request per order shipping item (WC_Order_Item_Shipping).
		foreach ( $adapter->get_fraktguiden_shipping_items() as $shipping_item ) {
			// Create the consignment.
			$consignment_request = Bring_Booking_Consignment_Request::create( $shipping_item );
			$args = [
				'shipping_date_time' => self::get_shipping_date_time(),
				'customer_number'    => $customer_number,
			];
			if ( in_array( $adapter->bring_product, [5600, 'PA_DOREN'] ) ) {
				// Alternative delivery date.
				if ( $bulk_mode ) {
					$time_slot = $adapter->shipping_item->get_meta( 'bring_fraktguiden_time_slot' );
					if ( $time_slot ) {
						$args['customer_specified_delivery_date_time'] = $time_slot;
					}
				} else {
					$args['customer_specified_delivery_date_time'] = self::get_shipping_date_time( '_bring-delivery-date', false );
				}
			}
			$consignment_request->fill( $args );

			$original_order_status = $wc_order->get_status();

			// Set order status to awaiting shipping.
			$wc_order->update_status( 'wc-bring-shipment' );

			// Send the booking.
			$response = $consignment_request->post();

			if ( 'yes' === Fraktguiden_Helper::get_option( 'debug' ) ) {
				$log = new WC_Logger();
				$log->add( Fraktguiden_Helper::ID, '[BOOKING] Request data: ' . wp_json_encode( $consignment_request->create_data(), JSON_PRETTY_PRINT ) );
				$log->add( Fraktguiden_Helper::ID, '[BOOKING] Response: ' . wp_json_encode( $response->to_array(), JSON_PRETTY_PRINT ) );
			}

			if ( ! in_array( $response->get_status_code(), [ 200, 201, 202, 203, 204 ], true ) ) {
				// @TODO: Error message
				// wp_send_json( json_decode('['.$response->get_status_code().','.$request_data['body'].','.$response->get_body().']',1) );die;
			}

			if ( empty( $count[ $date_now ] ) ) {
				$count[ $date_now ] = 0;
			}

			// Save the response json to the order.
			// @TODO: Save per shipping item instead. See issue #48
			$adapter->update_booking_response( $response );

			// Download labels pdf.
			if ( $adapter->has_booking_errors() ) {
				// If there are errors, set the status back to the original status.
				$status      = $original_order_status;
				$status_note = __( 'Booking errors. See the Bring Booking box for details.', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL;
				$wc_order->update_status( $status, $status_note );

				continue;
			}

			$count[ $date_now ]++;

			// Download the labels.
			$consigments = Bring_Consignment::create_from_response( $response, $wc_order->get_id() );
			foreach ( $consigments as $consignment ) {
				$consignment->download_label();
			}

			// Create new status and order note.
			$status = Fraktguiden_Helper::get_option( 'auto_set_status_after_booking_success' );
			if ( 'none' === $status ) {
				// Set status back to the previous status.
				$status = $original_order_status;
			}

			$status_note = __( 'Booked with Bring', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL;

			// Update status.
			$wc_order->update_status( $status, $status_note );
		}

		update_option( 'bring_fraktguiden_booking_count', $count, false );
	}

	/**
	 * Create a shipping date
	 *
	 * @return array
	 */
	public static function create_shipping_date() {
		return array(
			'date'   => date_i18n( 'Y-m-d' ),
			'hour'   => date_i18n( 'H', strtotime( '+1 hour', current_time( 'timestamp' ) ) ),
			'minute' => date_i18n( 'i' ),
		);
	}

	/**
	 * Get a shipping date time
	 *
	 * @return string
	 */
	public static function get_shipping_date_time( $name = '_bring-shipping-date', $default_to_now = true ) {
		$input_request = Fraktguiden_Helper::get_input_request_method();

		$date         = filter_input( $input_request, $name . '' );
		$date_hour    = filter_input( $input_request, $name . '-hour' );
		$date_minutes = filter_input( $input_request, $name . '-minutes' );

		// Get the shipping date.
		if ( $date && $date_hour && $date_minutes ) {
			return $date . 'T' . $date_hour . ':' . $date_minutes . ':00';
		}

		if ( ! $default_to_now ) {
			return false;
		}

		$shipping_date = self::create_shipping_date();

		return $shipping_date['date'] . 'T' . $shipping_date['hour'] . ':' . $shipping_date['minute'] . ':00';
	}

	/**
	 * Bulk booking requests
	 *
	 * @param array $post_ids Array of WC_Order IDs.
	 */
	public static function bulk_send_booking( $post_ids ) {
		$report = [];
		foreach ( $post_ids as $post_id ) {
			$adapter = new Bring_WC_Order_Adapter( new WC_Order( $post_id ) );
			try {
				if ( ! $adapter->has_booking_consignments() ) {
					self::send_booking( $adapter->order, true );
				}
			} catch ( Exception $e ) {
				$report[ $post_id ] = [
					'status'       => 'error',
					'order_id'     => $post_id,
					'message'      => $e->getMessage(),
					'order_status' => self::get_status( $post_id ),
					'url'          => get_edit_post_link( $post_id ),
				];
				continue;
			}
			$status = 'ok';
			$message = '';
			if ($adapter->has_booking_errors()) {
				$status = 'error';
				$message = esc_attr__('Error: Could not book the order!', 'bring-fraktguiden-for-woocommerce');
			}
			$report[ $post_id ] = [
				'status'       => $status,
				'order_id'     => $post_id,
				'message'      => $message,
				'order_status' => self::get_status( $post_id ),
				'url'          => get_edit_post_link( $post_id, 'edit' ),
			];
		}

		return $report;
	}

	/**
	 * Bulk booking requests
	 *
	 * @param array $post_ids Array of WC_Order IDs.
	 */
	public static function get_status( $post_id ) {
		$table_orders_file = WP_PLUGIN_DIR . '/woocommerce/includes/admin/list-tables/class-wc-admin-list-table-orders.php';
		if ( ! file_exists( $table_orders_file ) ) {
			return false;
		}
		include_once $table_orders_file;
		$wc_list_table = new WC_Admin_List_Table_Orders();
		ob_start();
		$wc_list_table->render_columns( 'order_status', $post_id );
		return ob_get_clean();
	}

	/**
	 * Check if the plugin works in a test mode
	 *
	 * @return boolean
	 */
	public static function is_test_mode() {
		return 'yes' === Fraktguiden_Helper::get_option( 'booking_test_mode_enabled' );
	}

	/**
	 * Get API UID
	 *
	 * @return bool|string
	 */
	public static function get_api_uid() {
		return Fraktguiden_Helper::get_option( 'mybring_api_uid' );
	}

	/**
	 * Get API key
	 *
	 * @return bool|string
	 */
	public static function get_api_key() {
		return Fraktguiden_Helper::get_option( 'mybring_api_key' );
	}
}
