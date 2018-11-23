<?php

/**
 * Fraktguiden Order Debug
 */
class Fraktguiden_Order_Debug {
	static function setup() {
		add_action( 'add_meta_boxes', __CLASS__.'::add_events_metaboxes' );
	}

	static function add_events_metaboxes( $post_type ) {
		if ( 'shop_order' != $post_type ) {
			return;
		}
		add_meta_box(
			'bring_fraktguiden_debug',
			'Bring Fraktguiden Debug information',
			__CLASS__.'::layout_of_meta_box_content'
		);
	}


	static function layout_of_meta_box_content() {
		?>
		<div>
			<div class="test-plane">
				<?php self::render(); ?>
			</div>
			<!-- <div class="test-plane"><h4></h4></div> -->
		</div>
		<?php
	}


	public static function render() {
		$order = wc_get_order( get_the_ID() );
		$booking_response = $order->get_meta( '_bring_booking_response', true );

		$labels = $order->get_meta( '_mailbox_label_ids', true );
		if ( ! empty( $labels ) ) {
			self::render_label_data( $labels );
		}
		echo '<h4>Booking information:</h4>';
		echo "<pre><code>";
		if ( ! empty( $booking_response ) ) {
			if ( preg_match( '/^2/', $booking_response['status_code'] ) ) {
				$data = json_decode( $booking_response['body'] );
				var_dump( $data );
			}
		} else {
			echo "Not yet booked\n";
		}
		echo "</code></pre>";

		$adapter = new Bring_WC_Order_Adapter( $order );
		$consignments = $adapter->get_booking_consignments();
		if ( ! empty( $consignments ) ) {
			echo '<h4>Tracking link:</h4>';
			foreach ( $consignments as $consignment ) {
				printf(
					'<a href="%1$s" target="_blank">%1$s</a>',
					esc_html( $consignment->get_tracking_link() )
				);
			}
		}

		echo "shipping\n";
		$shipping_items = $order->get_items( 'shipping' );
		foreach ( $shipping_items as $shipping_item ) {
			var_dump($shipping_item->get_meta_data());
		}
		echo "</pre>";

	}

	public static function render_fields( $fields, $data ) {
		echo '<dl>';
		foreach ( $fields as $field ) {
			$title = substr( $field, 1 );
			$title = str_replace( '_', ' ', $title );
			$title = ucfirst( $title );
			printf(
				'<dt>%s</dt>',
				esc_html( $title )
			);
			if ( empty( $data[ $field ] ) ) {
				continue;
			}
			foreach ( $data[ $field ] as $value ) {
				printf(
					'<dd>%s</dd>',
					esc_html( $value )
				);
			}
		}
		echo '</dl>';
	}

	public static function render_label_data( $ids ) {
		foreach ( $ids as $post_id ) {
			$data = get_post_meta( $post_id );

			printf(
				'<h4>%s</h4>',
				esc_html( 'Label information' )
			);
			$fields = [
				'_order_id',
				'_label_url',
				'_consignment_number',
				'_customer_number',
				'_test_mode',
			];

			self::render_fields( $fields, $data );

			if ( ! empty( $data['_mailbox_waybill_id'] ) ) {
				self::render_waybill_data( $data['_mailbox_waybill_id'] );
			}
		}
	}
	public static function render_waybill_data( $ids ) {

		foreach ( $ids as $post_id ) {
			printf(
				'<h4>%s</h4>',
				esc_html( 'Waybill #' . $post_id )
			);
			$meta = get_post_meta( $post_id, '_waybill_request_data', true );
			if ( empty( $meta ) ) {
				esc_html_e( 'Missing metadata on the waybill.' );
			} else {
				echo '<pre>';
				var_dump( $meta );
				echo '</pre>';
			}
		}
	}
}
