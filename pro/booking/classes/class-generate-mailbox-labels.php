<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Generate_Mailbox_Labels class
 */
class Generate_Mailbox_Labels {

	/**
	 * Setup
	 *
	 * @return void
	 */
	public static function setup() {
		add_action( 'current_screen', __CLASS__ . '::generate' );
	}

	/**
	 * Generate labels
	 *
	 * @param string $screen Screen.
	 */
	public static function generate( $screen ) {
		if ( 'mailbox_waybill' !== $screen->post_type ) {
			return;
		}

		$posts = get_posts(
			[
				'post_type'      => 'shop_order',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => [
					'relation'       => 'AND',
					'booking_clause' => [
						'key'     => '_bring_booking_response',
						'compare' => 'EXISTS',
					],
					'waybill_clause' => [
						'key'     => '_mailbox_label_ids',
						'compare' => 'NOT EXISTS',
					],
				],
			]
		);

		foreach ( $posts as $post_id ) {
			$wc_order           = wc_get_order( $post_id );
			$adapter            = new Bring_WC_Order_Adapter( $wc_order );
			$order_consignments = $adapter->get_booking_consignments();
			$label_ids          = [];

			foreach ( $order_consignments as $consignment ) {
				if ( 'Bring_Mailbox_Consignment' !== get_class( $consignment ) ) {
					continue;
				}

				// @TODO: Move this to when booking is complete
				$label_ids[] = self::create_label( $consignment );
			}

			update_post_meta( $post_id, '_mailbox_label_ids', $label_ids );
		}
	}

	/**
	 * Create Label
	 *
	 * @param Bring_Mailbox_Consignment $consignment Consignment.
	 */
	public static function create_label( $consignment ) {
		$new_post = [
			'post_title'  => $consignment->get_consignment_number(),
			'post_type'   => 'mailbox_label',
			'post_status' => 'draft',
		];

		$id = wp_insert_post( $new_post );

		update_post_meta( $id, '_order_id', $consignment->order_id );
		update_post_meta( $id, '_label_url', $consignment->get_label_url() );
		update_post_meta( $id, '_consignment_number', $consignment->get_consignment_number() );
		update_post_meta( $id, '_customer_number', $consignment->get_customer_number() );
		update_post_meta( $id, '_test_mode', ( $consignment->get_test_indicator() ? 'yes' : 'no' ) );

		return $id;
	}

	/**
	 * Trash old labels
	 * Labels older than 120 hours should be deleted.
	 */
	public static function cleanup() {
		// Delete old labels.
	}
}
