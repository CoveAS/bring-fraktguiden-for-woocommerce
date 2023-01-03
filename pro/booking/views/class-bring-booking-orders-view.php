<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Views;

use Bring_Fraktguiden;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Bring_Booking;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Orders_View class
 */
class Bring_Booking_Orders_View {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_footer-edit.php', [ __CLASS__, 'add_bulk_admin_footer' ] );
		add_filter( 'manage_edit-shop_order_columns', [ __CLASS__, 'booking_status_column' ], 15 );
		add_action( 'manage_shop_order_posts_custom_column', [ __CLASS__, 'booking_column_value' ], 10, 2 );
		add_action( 'admin_action_bring_bulk_book', [ __CLASS__, 'bulk_send_booking' ] );
		add_filter( 'bulk_actions-edit-shop_order', [ __CLASS__, 'add_bring_bulk_actions' ] );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_load_javascript' );
	}

	/**
	 * Add bulk booking and printing to actions selector
	 *
	 * @param  array $actions Actions.
	 * @return array
	 */
	public static function add_bring_bulk_actions( $actions ) {
		$actions['bring_bulk_book']  = Bring_Booking_Common_View::booking_label( true );
		$actions['bring_bulk_print'] = __( 'Bring - Print labels', 'bring-fraktguiden-for-woocommerce' );

		return $actions;
	}

	/**
	 * Get booking status column
	 *
	 * @param array $columns Columns.
	 *
	 * @return mixed
	 */
	public static function booking_status_column( $columns ) {
		$columns['bring_booking_status'] = __( 'Booking', 'bring-fraktguiden-for-woocommerce' );

		return $columns;
	}

	/**
	 * Get booking column value
	 *
	 * @param string $column Column.
	 */
	public static function booking_column_value( $column ) {
		global $the_order;

		if ( 'bring_booking_status' === $column ) {
			$order = new Bring_WC_Order_Adapter( $the_order );
			$info  = Bring_Booking_Common_View::get_booking_status_info( $order );
			?>

			<div class="bring-area-icon">
				<?php echo Bring_Booking_Common_View::create_status_icon( $info, 16 ); ?>
			</div>

			<div class="bring-area-info">
				<?php echo $info['text']; ?>
			</div>

			<?php
		}
	}

	/**
	 * Add bulk admin footer
	 */
	public static function add_bulk_admin_footer() {
		global $post_type;

		if ( 'shop_order' === $post_type ) {
			require_once dirname( __DIR__ ) . '/templates/modal-templates.php';
		}
	}

	/**
	 * Load admin javascript
	 */
	public static function admin_load_javascript() {
		$screen = get_current_screen();
		// Only for order edit screen.
		if ( 'edit-shop_order' !== $screen->id ) {
			return;
		}

		wp_register_script( 'fraktguiden-booking-admin', plugins_url( 'assets/js/booking-admin.js', dirname( __DIR__ ) ), [ 'jquery' ], Bring_Fraktguiden::VERSION, true );
		wp_localize_script(
			'fraktguiden-booking-admin',
			'_booking_data',
			[
				'downloadurl' => Bring_Booking_Labels::create_download_url( '' ),
			]
		);

		wp_enqueue_script( 'fraktguiden-booking-admin' );
	}

	/**
	 * Send booking in bulk
	 *
	 * @return void
	 */
	public static function bulk_send_booking() {
		$json     = filter_input( Fraktguiden_Helper::get_input_request_method(), 'json' );
		$post_ids = filter_input( Fraktguiden_Helper::get_input_request_method(), 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $post_ids ) ) {
			return;
		}

		$report = Bring_Booking::bulk_send_booking( $post_ids );

		$column_data = [];
		foreach ( $post_ids as $post_id ) {
			$wc_order                = wc_get_order( $post_id );
			$adapter                 = new Bring_WC_Order_Adapter( $wc_order );
			$column_data[ $post_id ] = Bring_Booking_Common_View::get_booking_status_info( $adapter );
		}
		if ( $json ) {
			wp_send_json( [
				'bring_column' => $column_data,
				'report'       => $report,
			] );
		}
	}
}
