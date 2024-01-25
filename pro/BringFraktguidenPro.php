<?php

namespace BringFraktguidenPro;

/**
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden;
use BringFraktguidenPro\Booking\Actions\Get_Booking_Data_Action;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring Fraktguiden Pro
 */
class BringFraktguidenPro {

	public static function setup() {
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
	}

	public static function admin_enqueue_scripts( $hook ): void {
		if ( 'post.php' !== $hook && 'woocommerce_page_wc-orders' !== $hook) {
			return;
		}

		$order = wc_get_order();
		if (! $order) {
			return;
		}
		$adapter = new Bring_WC_Order_Adapter($order);

		wp_enqueue_script(
			'bring-fraktguiden-pro-booking',
			plugin_dir_url( __DIR__ ) . 'pro/assets/js/booking.js',
			[],
			Bring_Fraktguiden::VERSION,
			true
		);

		wp_localize_script(
			'bring-fraktguiden-pro-booking',
			'bring_fraktguiden_booking',
			(new Get_Booking_Data_Action())($adapter)
		);
	}
}
