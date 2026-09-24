<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking;

use BringFraktguiden\Booking\BulkBookingGroups;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_License;
use BringFraktguidenPro\Booking\Box\BookingBox;
use BringFraktguidenPro\Booking\Box\BookingDraft;
use BringFraktguidenPro\Booking\Box\BookingForm;
use BringFraktguidenPro\Booking\Box\BookingRecord;
use BringFraktguidenPro\Booking\Box\BookingSender;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Labels;
use BringFraktguidenPro\Booking\Views\Bring_Booking_My_Order_View;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Orders_View;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use Exception;
use WC_Admin_List_Table_Orders;
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
		// The box shows even without Mybring keys, because it then names the
		// reason booking is blocked and links to the page that fixes it.
		BookingBox::init();

		if ( ! self::is_valid_for_use() ) {
			return;
		}

		Bring_Booking_Orders_View::init();

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
	 * Book the order outside the booking box.
	 *
	 * The form holds the saved draft of the box, or else what the order
	 * holds. A value in $overrides wins over both, because the shop worker
	 * picked it in the bulk dialog for the whole selection.
	 *
	 * @param array $overrides Form fields, such as customer_number, shipping_date and shipping_time.
	 *
	 * @throws Exception When the shop or the order cannot be booked at all.
	 */
	public static function book( WC_Order $order, array $overrides = [] ): BookingRecord {
		$adapter        = new Bring_WC_Order_Adapter( $order );
		$shipping_items = $adapter->get_fraktguiden_shipping_items();
		$form           = BookingDraft::read( $order ) ?? BookingForm::from_order( $order, reset( $shipping_items ) ?: null );
		$payload        = array_merge( $form->to_array(), $overrides );

		return BookingSender::send( $order, BookingForm::from_payload( $payload ) );
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
	 * Bulk booking requests
	 *
	 * The report names one status per order. `ok` means the order booked now,
	 * `booked` means it already held a booking, `status` means the order status
	 * allows no booking, and `skipped` means it carries no Bring shipping line. The caller prints the labels of the first two.
	 *
	 * @param array $post_ids  Array of WC_Order IDs.
	 * @param array $overrides Form fields that win for every order. See book().
	 */
	public static function bulk_send_booking( $post_ids, array $overrides = [] ) {
		$report = [];
		foreach ( $post_ids as $post_id ) {
			$adapter = new Bring_WC_Order_Adapter( new WC_Order( $post_id ) );
			$status  = BulkBookingGroups::of_order( $adapter->order );
			$message = '';

			if ( BulkBookingGroups::BOOK !== $status ) {
				$report[ $post_id ] = [
					'status'       => $status,
					'order_id'     => $post_id,
					'message'      => $message,
					'order_status' => self::get_status( $post_id ),
					'url'          => get_edit_post_link( $post_id, 'edit' ),
				];
				continue;
			}

			try {
				$record = self::book( $adapter->order, $overrides );
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
			if ( $record->failed() ) {
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
		// A shop whose license belongs to another domain runs on a copy of that
		// shop. A copy may never book a real shipment.
		if ( Fraktguiden_License::licensed_elsewhere() ) {
			return true;
		}

		// A testing license may never book a real shipment, whatever the setting says.
		if ( Fraktguiden_License::is_testing() ) {
			return true;
		}

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
