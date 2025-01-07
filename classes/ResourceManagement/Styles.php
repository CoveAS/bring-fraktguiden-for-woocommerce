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
		if ( ! is_cart() && ! is_checkout() && ! self::has_checkout_block()) {
			return;
		}
		$plugin_path = dirname( __DIR__, 2);
		wp_register_style( 'bring-fraktguiden-for-woocommerce', plugins_url( basename( $plugin_path ) . '/assets/css/bring-fraktguiden.css' ), array(), Bring_Fraktguiden::VERSION );
		wp_enqueue_style( 'bring-fraktguiden-for-woocommerce' );
	}

	/**
	 * Check if the current page contains the WooCommerce Checkout block.
	 *
	 * @return bool
	 */
	private static function has_checkout_block(): bool
	{
		global $post;
		// Ensure we are in the loop and can access post content.
		if ( ! function_exists( 'has_block' ) ) {
			return false;
		}

		// Check for the WooCommerce Checkout block in the current post content.
		ray( isset( $post->post_content ) , has_block( 'woocommerce/checkout', $post->post_content ));
		return isset( $post->post_content ) && has_block( 'woocommerce/checkout', $post->post_content );
	}
}
