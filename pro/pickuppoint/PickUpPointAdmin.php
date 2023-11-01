<?php

namespace BringFraktguidenPro\PickUpPoint;

use Bring_Fraktguiden;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use WC_Order;

class PickUpPointAdmin {

	public static function init(): void {
		// Enqueue admin Javascript.
		add_action('admin_enqueue_scripts', [ __CLASS__, 'admin_load_javascript' ] );
		// Admin save order items.
		add_action('woocommerce_saved_order_items', [ __CLASS__, 'admin_saved_order_items' ], 1, 2);
	}

	/**
	 * Load admin javascript
	 */
	public static function admin_load_javascript()
	{
		$screen = get_current_screen();

		// Only for order edit screen.
		if ('shop_order' !== $screen->id && 'shop_subscription' !== $screen->id) {
			return;
		}

		global $post;

		$order = new Bring_WC_Order_Adapter(new WC_Order($post->ID));

		$make_items_editable = !$order->order->is_editable();

		if (!is_null(filter_input(INPUT_GET, 'booking_step'))) {
			$make_items_editable = false;
		}

		if ($order->is_booked()) {
			$make_items_editable = false;
		}

		wp_register_script('fraktguiden-common', plugins_url('assets/js/pickup-point-common.js', dirname(__FILE__)), [ 'jquery' ], Bring_Fraktguiden::VERSION, true);
		wp_register_script('fraktguiden-pickup-point-admin', plugins_url('assets/js/pickup-point-admin.js', dirname(__FILE__)), [ 'jquery' ], Bring_Fraktguiden::VERSION, true);
		wp_localize_script(
			'fraktguiden-pickup-point-admin',
			'_fraktguiden_data',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'services' => Fraktguiden_Helper::get_all_services(),
				'i18n' => PickUpPoint::get_i18n(),
				'make_items_editable' => $make_items_editable,
			]
		);

		wp_enqueue_script('fraktguiden-common');
		wp_enqueue_script('fraktguiden-pickup-point-admin');
	}

	/**
	 * Updates pickup points from admin/order items.
	 *
	 * @param int|string $order_id Order ID.
	 * @param array $shipping_items Shipping items.
	 */
	public static function admin_saved_order_items($order_id, $shipping_items)
	{
		$order = new Bring_WC_Order_Adapter(new WC_Order($order_id));
		$order->admin_update_pickup_point($shipping_items);
	}
}
