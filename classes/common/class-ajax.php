<?php

namespace Bring_Fraktguiden\Common;

/**
 * Checkout Modifications
 */
class Ajax {

	static function setup() {
		add_action( 'wp_ajax_bring_select_time_slot', __CLASS__ . '::select_time_slot' );
		add_action( 'wp_ajax_nopriv_bring_select_time_slot', __CLASS__ . '::select_time_slot' );
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
}
