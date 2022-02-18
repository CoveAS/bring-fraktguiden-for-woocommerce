<?php
/**
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring Fraktguiden Pro
 */
class Bring_Fraktguiden_Pro {

	public static function setup() {
		spl_autoload_register( __CLASS__ . '::class_loader' );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
	}
	/**
	 * Class loader
	 *
	 * @param string $class_name Path to class file.
	 */
	public static function class_loader( string $class_name ) {
		if ( ! preg_match( '/^Bring_Fraktguiden_Pro(\\\.*)$/', $class_name, $matches ) ) {
			return;
		}
		$path      = substr( strtolower( $matches[1] ), 1 );
		$path      = preg_replace( '/_/', '-', $path );
		$parts     = explode( '\\', $path );
		$file_name = array_pop( $parts );
		$domain    = array_shift( $parts );
		$dir       = "$domain/classes/" . implode( '/', $parts );
		if ( $dir ) {
			$dir = "/$dir";
		}
		$file_name = __DIR__ . "{$dir}/class-{$file_name}.php";

		if ( file_exists( $file_name ) ) {
			require_once $file_name;
		}
	}

		public static function admin_enqueue_scripts( $hook ) {
			if ( 'post.php' !== $hook ) {
				return;
			}
			wp_enqueue_script(
				'bring-fraktguiden-pro-booking',
				plugin_dir_url( __DIR__ ) . 'pro/assets/js/booking.js',
				[],
				Bring_Fraktguiden::VERSION,
				true
			);
		}
}
