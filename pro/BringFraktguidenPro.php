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
		add_filter( 'script_loader_tag', __CLASS__ . '::add_type_module', 10, 3 );
	}

	/**
	 * Add type="module" to Vue scripts
	 */
	public static function add_type_module( string $tag, string $handle, string $src ): string {
		// Add type="module" to our Vite-built scripts
		if ( in_array( $handle, [ 'bring-vue-runtime', 'bring-fraktguiden-admin' ], true ) ) {
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}
		return $tag;
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

		// Enqueue Vue runtime chunks (required for admin script)
		wp_enqueue_script(
			'bring-vue-runtime',
			plugin_dir_url( __DIR__ ) . 'build/js/shared/vue-runtime.js',
			[],
			Bring_Fraktguiden::VERSION,
			true
		);

		wp_enqueue_script(
			'bring-fraktguiden-admin',
			plugin_dir_url( __DIR__ ) . 'build/js/admin.js',
			['bring-vue-runtime'],
			Bring_Fraktguiden::VERSION,
			true
		);

		wp_localize_script(
			'bring-fraktguiden-admin',
			'bring_fraktguiden_booking',
			(new Get_Booking_Data_Action())($adapter)
		);
	}
}
