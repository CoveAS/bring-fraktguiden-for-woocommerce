<?php

namespace Bring_Fraktguiden\ResourceManagement;

use Bring_Fraktguiden;

class Styles
{
	public static function setup(): void
	{
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_styles' );
	}
	/**
	 * Enqueue styles
	 */
	public static function enqueue_styles(): void
	{
		// Do not load styles on any page except cart and checkout.
		if ( ! is_cart() && ! is_checkout() ) {
			return;
		}
		$plugin_path = dirname( __DIR__, 2);
		wp_register_style( 'bring-fraktguiden-for-woocommerce', plugins_url( basename( $plugin_path ) . '/assets/css/bring-fraktguiden.css' ), array(), Bring_Fraktguiden::VERSION );
		wp_enqueue_style( 'bring-fraktguiden-for-woocommerce' );
	}
}
