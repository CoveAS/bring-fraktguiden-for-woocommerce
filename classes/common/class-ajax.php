<?php

namespace Bring_Fraktguiden\Common;

/**
 * Checkout Modifications
 */
class Ajax {

	static function setup() {
		add_action( 'wp_ajax_bring_select_time_slot', __CLASS__ . '::select_time_slot' );
		add_action( 'wp_ajax_nopriv_bring_select_time_slot', __CLASS__ . '::select_time_slot' );
		add_action( 'wp_ajax_bring_save_license', __CLASS__ . '::save_license' );
	}

	public static function select_time_slot( $fragments ) {
		$time_slot = filter_input( INPUT_POST, 'time_slot', FILTER_DEFAULT );
		if ( empty( $time_slot ) ) {
			wp_send_json(
				[
					'status'      => 'error',
					'message'     => __( 'Required field, time_slot, was empty', 'bring-fraktguiden-for-woocommerce' ),
					'errors'      => '',
				]
			);
			die;
		}

		$old_time_slot = WC()->session->get( 'bring_fraktguiden_time_slot' );
		if ( empty( $old_time_slot ) || $old_time_slot !== $time_slot ) {
			// Save the new location to session.
			WC()->session->set(
				'bring_fraktguiden_time_slot',
				$time_slot
			);
		}
		wp_send_json(
			[
				'status'      => 'success',
				'message'     => __( 'Saved location ID', 'bring-fraktguiden-for-woocommerce' ),
				'errors'      => '',
			]
		);
	}

	public static function save_license() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json( [
				'status'  => 'error',
				'message' => __( 'Permission denied', 'bring-fraktguiden-for-woocommerce' ),
			] );
		}

		$license_key = filter_input( INPUT_POST, 'license_key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $license_key ) ) {
			wp_send_json( [
				'status'  => 'error',
				'message' => __( 'License key is required', 'bring-fraktguiden-for-woocommerce' ),
			] );
		}

		// Save license key and enable pro
		Fraktguiden_Helper::update_option( 'test_url', $license_key );
		Fraktguiden_Helper::update_option( 'pro_enabled', 'yes' );

		// Set pro_activated_on if not already set
		if ( ! Fraktguiden_Helper::get_option( 'pro_activated_on' ) ) {
			Fraktguiden_Helper::update_option( 'pro_activated_on', time() );
		}

		wp_send_json( [
			'status'  => 'success',
			'message' => __( 'License saved', 'bring-fraktguiden-for-woocommerce' ),
		] );
	}
}
