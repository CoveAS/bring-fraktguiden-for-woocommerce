<?php

namespace Bring_Fraktguiden\Common;

use Bring_Fraktguiden\Factories\Alternative_Delivery_Date_Factory;

/**
 * Checkout Modifications
 */
class Checkout_Modifications {

	static function setup() {
		add_action(
			'woocommerce_checkout_order_review',
			__CLASS__ . '::template',
			15
		);

		add_filter(
			'woocommerce_update_order_review_fragments',
			__CLASS__ . '::filter_fragments'
		);

		add_action(
			'wp_enqueue_scripts',
			__CLASS__ . '::enqueue_scripts'
		);

		add_action(
			'woocommerce_checkout_create_order_shipping_item',
			__CLASS__ . '::attach_item_meta',
			10,
			4
		);
	}


	public static function enqueue_scripts() {
		if ( ! is_checkout() ) {
			return;
		}

		$url = plugins_url( 'assets/js/bring-fraktguiden-checkout.js', dirname( __DIR__ ) );
		wp_register_script(
			'fraktguiden-checkout-js',
			$url,
			[ 'jquery' ],
			\Bring_Fraktguiden::VERSION,
			true
		);
		wp_localize_script(
			'fraktguiden-checkout-js',
			'_fraktguiden_checkout',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			]
		);
		wp_enqueue_script( 'fraktguiden-checkout-js' );
	}

	public static function filter_fragments( $fragments ) {
		ob_start();
		self::template();
		$fragments['.bring-fraktguiden-date-options'] = ob_get_clean();

		return $fragments;
	}

	public static function template() {
		$args = self::get_alternative_date_parameters();
		extract( $args );
		include dirname( dirname( __DIR__ ) ) . '/templates/woocommerce/alternative-dates.php';
	}

	public static function get_alternative_date_parameters() {
		$args                    = [
			'earliest'     => false,
			'range'        => [],
			'alternatives' => [],
			'selected'     => WC()->session->get( 'bring_fraktguiden_time_slot' ),
		];
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$meta_data               = false;
		foreach ( WC()->shipping()->get_packages() as $i => $package ) {
			if ( ! isset( $chosen_shipping_methods[ $i ], $package['rates'][ $chosen_shipping_methods[ $i ] ] ) ) {
				continue;
			}
			// Get current selected rate.
			$rate      = $package['rates'][ $chosen_shipping_methods[ $i ] ];
			$meta_data = $rate->get_meta_data();
			break;
		}

		if ( empty( $meta_data['alternative_delivery_dates'] ) ) {
			return $args;
		}
		$factory      = new Alternative_Delivery_Date_Factory();
		$alternatives = $factory->from_array(
			$meta_data['alternative_delivery_dates']
		);

		if ( empty( $alternatives ) ) {
			return $args;
		}
		$time_slot_group      = reset( $alternatives );
		$args['earliest']     = reset( $time_slot_group['items'] );
		$args['range']        = self::extract_date_range( $alternatives );
		$args['alternatives'] = $alternatives;

		if ( ! self::validate_selected_time_slot( $args['selected'], $alternatives ) ) {
			$args['selected'] = array_key_first( $time_slot_group['items'] ) . 'T' . $time_slot_group['id'];
		}

		return $args;
	}

	public static function validate_selected_time_slot( $selected, $alternatives ) {
		foreach ( $alternatives as $time_slot_group ) {
			foreach ( $time_slot_group['items'] as $key => $alternative ) {
				if ( $selected === $key . 'T' . $time_slot_group['id'] ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function extract_date_range( $alternatives ) {
		$range      = [];
		$first_date = null;
		$last_date  = null;

		// Find the first and last dates in the available alternatives
		foreach ( $alternatives as $time_slot_group ) {
			$last = end( $time_slot_group['items'] );
			if ( ! $last_date || $last->expected_delivery_date > $last_date ) {
				$last_date = clone $last->expected_delivery_date;
			}
			$first = reset( $time_slot_group['items'] );
			if ( ! $first_date || $first->expected_delivery_date < $first_date ) {
				$first_date = clone $first->expected_delivery_date;
			}
		}
		// Create a time period.
		$interval = \DateInterval::createFromDateString( '1 day' );
		$period   = new \DatePeriod(
			$first_date->modify( '-1 day' ),
			$interval,
			$last_date->modify( '+1 day' )
		);

		// Fill the range array.
		foreach ( $period as $date ) {
			$key           = $date->format( "Y-m-d" );
			$range[ $key ] = [
				'd'    => $date->format( 'D' ),
				'day'  => ucfirst( wp_date( 'D', $date->getTimestamp(), $date->getTimezone() ) ),
				'date' => str_replace(
					' ',
					'&nbsp;',
					wp_date( 'j. F', $date->getTimestamp(), $date->getTimezone() )
				),
			];
		}

		return $range;
	}

	/**
	 * Attach item meta
	 *
	 * @param \WC_Order_Item_Shipping $item Shipping item.
	 * @param string $package_key Package key.
	 * @param array $package Package.
	 * @param \WC_Order $order Order Instance.
	 */
	public static function attach_item_meta( $item, $package_key, $package, $order ) {
		$bring_product = $item->get_meta( 'bring_product' );
		if ( empty( $bring_product ) ) {
			return;
		}
		$shipping_methods   = \WC_Shipping::instance()->get_shipping_methods();
		$shipping_method_id = $item->get_method_id();
		if ( empty( $shipping_methods[ $shipping_method_id ] ) ) {
			return;
		}
		$shipping_method = $shipping_methods[ $shipping_method_id ];
		$field_key       = $shipping_method->get_field_key( 'services' );
		if ( ! \Fraktguiden_Service::vas_for( $field_key, $bring_product, [ 'alternative_delivery_dates' ] ) ) {
			return;
		}
		$time_slot = WC()->session->get( 'bring_fraktguiden_time_slot' );
		$item->add_meta_data( 'bring_fraktguiden_time_slot', $time_slot, true );
		$order->add_order_note(
			__( 'Customer requested time slot: ' ) . $time_slot
		);

		add_action(
			'woocommerce_checkout_update_order_meta',
			__CLASS__ . '::attach_order_note',
		);
	}

	/**
	 * Attach item meta
	 *
	 * @param integer $order_id Order id.
	 * @param string $package_key Package key
	 * @param array $package Package.
	 * @param \WC_Order $order Order Instance.
	 */
	public static function attach_order_note( $order_id ) {
		$order     = wc_get_order( $order_id );
		$time_slot = WC()->session->get( 'bring_fraktguiden_time_slot' );
		$order->add_order_note(
			__( 'Customer requested delivery time:', 'bring-fraktguiden-for-woocommerce' ) . " $time_slot"
		);
	}
}
