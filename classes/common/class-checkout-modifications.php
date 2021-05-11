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
	}


	public static function filter_fragments( $fragments ) {
		ob_start();
		self::template();
		$fragments['.bring-fraktguiden-date-options'] = ob_get_clean();

		return $fragments;
	}

	public static function template() {
		$args = self::get_args();
		extract( $args );
		include dirname( dirname( __DIR__ ) ) . '/templates/woocommerce/alternative-dates.php';
	}

	public static function get_args() {
		$args                    = [
			'earliest'     => false,
			'range'        => [],
			'alternatives' => [],
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

		$alternatives = Alternative_Delivery_Date_Factory::from_array(
			$meta_data['alternative_delivery_dates']
		);

		if ( empty( $alternatives ) ) {
			return $args;
		}
		$time_slot_group      = reset( $alternatives );
		$args['earliest']     = reset( $time_slot_group['items'] );
		$args['range']        = self::extract_range( $alternatives );
		$args['alternatives'] = $alternatives;

		return $args;
	}

	public static function extract_range( $alternatives ) {
		$range      = [];
		$first_date = null;
		$last_date  = null;

		// Find the first and last dates in the available alternatives
		foreach ( $alternatives as $time_slot_group ) {
			$last = end( $time_slot_group['items'] );
			if ( ! $last_date || $last->expected_delivery_date > $last_date ) {
				$last_date = $last->expected_delivery_date;
			}
			$first = reset( $time_slot_group['items'] );
			if ( ! $first_date || $first->expected_delivery_date < $first_date ) {
				$first_date = $first->expected_delivery_date;
			}
		}
		// Create a time period.
		$interval  = \DateInterval::createFromDateString( '1 day' );
		$last_date = clone $last_date;
		$period    = new \DatePeriod(
			$first_date,
			$interval,
			$last_date->modify( '+1 day' )
		);

		// Fill the range array.
		foreach ( $period as $date ) {
			$key           = $date->format( "Ymd" );
			$range[ $key ] = [
				'day'  => ucfirst( wp_date( 'D', $date->getTimestamp() ) ),
				'date' => wp_date( 'j F', $date->getTimestamp() ),
			];
		}

		return $range;
	}
}
