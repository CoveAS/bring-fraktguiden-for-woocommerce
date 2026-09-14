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
use BringFraktguidenPro\Booking\Bring_Booking_Customer;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use Exception;

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
	public static function init(): void
	{
		add_filter( 'script_loader_tag', [ __CLASS__, 'add_type_module' ], 10, 2 );

		// Legacy
		add_action( 'admin_footer-edit.php', [ __CLASS__, 'add_bulk_admin_footer' ] );
		add_filter( 'manage_edit-shop_order_columns', [ __CLASS__, 'booking_status_column' ], 15 );
		add_action( 'manage_shop_order_posts_custom_column', [ __CLASS__, 'booking_column_value' ], 10, 2 );
		add_filter( 'bulk_actions-edit-shop_order', [ __CLASS__, 'add_bring_bulk_actions' ] );

		// HPOS
		add_action( 'admin_footer-woocommerce_page_wc-orders', [ __CLASS__, 'add_bulk_admin_footer' ] );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', [ __CLASS__, 'booking_status_column' ], 15 );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ __CLASS__, 'booking_column_value' ], 10, 2 );
		add_filter( 'bulk_actions-woocommerce_page_wc-orders', [ __CLASS__, 'add_bring_bulk_actions' ] );

		// Js and ajax
		add_action( 'wp_ajax_bring_bulk_book', [ __CLASS__, 'bulk_send_booking' ] );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_load_javascript' );
	}

	/**
	 * Add bulk booking and printing to actions selector
	 *
	 * @param  array $actions Actions.
	 * @return array
	 */
	public static function add_bring_bulk_actions( $actions )
	{
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
	public static function booking_column_value( $column, $order = null ) {
		if ( is_int($order) ) {
			$order = wc_get_order($order);
		}
		if ( 'bring_booking_status' === $column ) {
			$order = new Bring_WC_Order_Adapter( $order );
			echo self::get_booking_status_html($order);
		}
	}

	public static function get_booking_status_html(Bring_WC_Order_Adapter $adapter)
	{
		$info  = Bring_Booking_Common_View::get_booking_status_info( $adapter );
		if ( ! $info ) {
			return;
		}
		ob_start();
		?>
		<div class="bring-booking-cell">
			<?php if ( $info['href'] ) : ?>
			<a
				<?php if (!empty($info['action'])): ?>
					data-action="<?php echo esc_attr( $info['action'] ); ?>"
				<?php endif; ?>
				<?php if (! empty($info['ids'])): ?>
					data-order-ids="<?php echo esc_attr( implode( ',', $info['ids'] ) ); ?>"
				<?php endif; ?>
				target="_blank"
				class="button "
				href="<?php echo esc_url( $info['href'] ); ?>">
				<?php endif; ?>

				<div class="bring-flex-box">
					<?php echo Bring_Booking_Common_View::create_status_icon( $info, 16 ); ?>
					<span class="bring-area-info">
						<?php echo $info['text']; ?>
					</span>

				</div>
				<?php if ( $info['href'] ) : ?>
			</a>
		<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Add bulk admin footer
	 */
	public static function add_bulk_admin_footer() {
		if ( ! self::is_orders_screen() ) {
			return;
		}

		$customers      = [];
		$customer_error = '';

		try {
			$customers = Bring_Booking_Customer::get_customer_numbers_formatted();
		} catch ( Exception $exception ) {
			$customer_error = $exception->getMessage();
		}

		$customer_number = (string) Fraktguiden_Helper::get_option( 'mybring_customer_number' );
		$shipping_date   = Bring_Booking::create_shipping_date();
		$book_label      = Bring_Booking_Common_View::booking_label( true );

		require_once dirname( __DIR__, 3 ) . '/build/templates/admin/booking/bulk-modal.php';
	}

	/**
	 * Tell whether the current screen lists orders.
	 */
	private static function is_orders_screen(): bool {
		$screen = get_current_screen();

		return 'edit-shop_order' === $screen->id || 'woocommerce_page_wc-orders' === $screen->id;
	}

	/**
	 * Load admin javascript
	 */
	public static function admin_load_javascript() {
		if ( ! self::is_orders_screen() ) {
			return;
		}

		$plugin_dir = dirname( __DIR__, 3 );

		// The bulk booking dialogs use the same shell and the same select as the
		// admin pages, so the orders list loads those two modules too.
		foreach ( [ 'dialog', 'custom-select' ] as $module ) {
			wp_enqueue_script(
				'bfg-' . $module,
				plugins_url( basename( $plugin_dir ) . '/build/js/' . $module . '.js' ),
				[],
				Bring_Fraktguiden::VERSION,
				true
			);
		}

		wp_register_script(
			'fraktguiden-booking-admin',
			plugins_url( 'assets/js/booking-admin.js',
			dirname( __DIR__ ) ),
			[ 'jquery' ],
			Bring_Fraktguiden::VERSION,
			true
		);
		wp_localize_script(
			'fraktguiden-booking-admin',
			'_booking_data',
			[
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'downloadurl' => Bring_Booking_Labels::create_download_url( '' ),
			]
		);

		wp_enqueue_script( 'fraktguiden-booking-admin' );
	}

	/**
	 * Mark the built modules as ES modules.
	 *
	 * WordPress 5.6 is the lowest supported version and has no
	 * wp_enqueue_script_module().
	 */
	public static function add_type_module( string $tag, string $handle ): string {
		if ( ! in_array( $handle, [ 'bfg-dialog', 'bfg-custom-select' ], true ) ) {
			return $tag;
		}

		return str_replace( '<script ', '<script type="module" ', $tag );
	}

	/**
	 * Send booking in bulk
	 *
	 * @return void
	 */
	public static function bulk_send_booking(): void
	{
		$json     = filter_input( Fraktguiden_Helper::get_input_request_method(), 'json' );
		$post_ids = filter_input( Fraktguiden_Helper::get_input_request_method(), 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$ids = filter_input( Fraktguiden_Helper::get_input_request_method(), 'id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty($post_ids) && ! empty( $ids ) ) {
			$post_ids = $ids;
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		$report = Bring_Booking::bulk_send_booking( $post_ids );

		$column_data = [];
		foreach ( $post_ids as $post_id ) {
			$wc_order                = wc_get_order( $post_id );
			$adapter                 = new Bring_WC_Order_Adapter( $wc_order );
			$column_data[ $post_id ] = self::get_booking_status_html( $adapter );
		}
		$printable = array_reduce(
			$report,
			function ($carry, $item) {
				if ( $item['status'] === 'ok' ) {
					$carry[] = $item['order_id'];
				}
				return $carry;
			},
			[],
		);
		if ( $json ) {
			wp_send_json( [
				'print_url'    => empty($printable) ? '' : Bring_Booking_Labels::create_download_url( $printable ),
				'bring_column' => $column_data,
				'report'       => $report,
			] );
		}
	}
}
