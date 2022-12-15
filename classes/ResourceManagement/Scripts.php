<?php

namespace Bring_Fraktguiden\ResourceManagement;

use Bring_Fraktguiden;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;

class Scripts
{

	public static function setup(): void
	{
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
	}

	/**
	 * Admin enqueue script
	 * Add custom styling and javascript to the admin options
	 */
	public static function admin_enqueue_scripts( string $hook ): void
	{
		if ( 'woocommerce_page_wc-settings' !== $hook ) {
			return;
		}
		$baseUrl = plugin_dir_url(dirname(__DIR__));
		wp_enqueue_script( 'hash-tables', $baseUrl . '/assets/js/jquery.hash-tabs.min.js', [ 'jquery' ], Bring_Fraktguiden::VERSION );
		wp_enqueue_script( 'bring-admin-js', $baseUrl . '/assets/js/bring-fraktguiden-admin.js', [], Bring_Fraktguiden::VERSION );
		wp_enqueue_script( 'mybring-admin-js', $baseUrl . '/assets/js/mybring-admin.js', ['jquery'], Bring_Fraktguiden::VERSION, true );
		wp_enqueue_script( 'bring-settings-js', $baseUrl . '/assets/js/bring-fraktguiden-settings.js', [], Bring_Fraktguiden::VERSION, true );
		wp_localize_script(
			'bring-admin-js',
			'bring_fraktguiden',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			]
		);
		wp_localize_script(
			'bring-settings-js',
			'bring_fraktguiden_settings',
			[
				'services_data'    => Fraktguiden_Helper::get_services_data(),
				'services'         => Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services' ),
				'services_enabled' => array_keys( Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services', true ) ),
				'pro_activated'    => Fraktguiden_Helper::pro_activated(),
				'i18n'             => [
					'shipping_name'               => esc_html__( 'Service name:', 'bring-fraktguiden-for-woocommerce' ),
					'fixed_price_override'        => esc_html__( 'Fixed price override:', 'bring-fraktguiden-for-woocommerce' ),
					'alternative_customer_number' => esc_html__( 'Alternative customer number:', 'bring-fraktguiden-for-woocommerce' ),
					'free_shipping_activated_at'  => esc_html__( 'Free shipping activated at:', 'bring-fraktguiden-for-woocommerce' ),
					'additional_fee'              => esc_html__( 'Additional fee:', 'bring-fraktguiden-for-woocommerce' ),
					'value_added_services'        => esc_html__( 'Value added services', 'bring-fraktguiden-for-woocommerce' ),
					'pickup_point'                => esc_html__( 'Pickup points', 'bring-fraktguiden-for-woocommerce' ),
					'error_api_uid'               => esc_html__( 'The api email should be a valid email address.', 'bring-fraktguiden-for-woocommerce' ),
					'error_customer_number'       => esc_html__( 'Customer numbers should be letters (A-Z) and underscores followed by a dash and a number.', 'bring-fraktguiden-for-woocommerce' ),
					'error_api_key'               => esc_html__( 'The api key should only contain letters (a-z), numbers and dashes.', 'bring-fraktguiden-for-woocommerce' ),
					'error_spaces'                => esc_html__( 'Spaces are not allowed in the', 'bring_fraktguiden-for-woocommerce' ),
					'api_email'                   => esc_html__( 'API email', 'bring_fraktguiden-for-woocommerce' ),
					'api_key'                     => esc_html__( 'API key', 'bring_fraktguiden-for-woocommerce' ),
					'customer_number'             => esc_html__( 'customer number', 'bring_fraktguiden-for-woocommerce' ),
				],
			]
		);
		wp_enqueue_style( 'bring-fraktguiden-styles', $baseUrl . '/assets/css/bring-fraktguiden-admin.css', [], Bring_Fraktguiden::VERSION );
	}
}
