<?php

namespace Bring_Fraktguiden\Factories;

use Bring_Fraktguiden\Models\Alternative_Delivery_Date;

class Alternative_Delivery_Date_Factory {
	public $lead_time;
	public $cutoff_time = 0;

	public function __construct( $lead_time, $cutoff_time ) {
		$this->lead_time   = (int) $lead_time;
		if (preg_match('/^\d{2}:\d{2}$/', $cutoff_time ) ) {
			$this->cutoff_time = (int) str_replace( ':', '', $cutoff_time );
		}
	}

	public function from_array( $alternative_delivery_dates ) {
		$time_slot_groups = [];

		foreach ( $alternative_delivery_dates as $date_data ) {
			$date                         = new Alternative_Delivery_Date();
			$date->working_days           = $date_data['workingDays'];
			$date->expected_delivery_date = $this->compress_date( $date_data['expectedDeliveryDate'] );
			$date->time_slots             = $this->compress_time_slots( $date_data['expectedDeliveryDate']['timeSlots'] );
			$date->shipping_dates         = [];
			foreach ( $date->time_slots as $time_slot ) {
				if ( empty( $time_slot_groups[ $time_slot ] ) ) {
					$time_slot_groups[ $time_slot ] = [
						'id'    => substr( $time_slot, 0, 5 ) . ':00',
						'label' => apply_filters(
							'bring_fraktguiden_timeslot_label',
							preg_replace(
								'/^(\d{2}):00\-(\d{2}):00$/',
								'$1-$2',
								$time_slot

							)
						),
						'items' => [],
					];
				}
				$key = $date->date( 'Y-m-d' );

				if ( ! isset( $time_slot_groups[ $time_slot ]['items'][ $key ] ) ) {
					$time_slot_groups[ $time_slot ]['items'][ $key ] = $date;
				}
				$time_slot_groups[ $time_slot ]['items'][ $key ]->shipping_dates[] = $this->compress_date( $date_data['shippingDate'] );
			}
		}
		foreach ( $time_slot_groups as &$time_slot_group ) {
			ksort( $time_slot_group['items'], SORT_NATURAL );
		}
		unset( $time_slot_group );

		$cutoff    = new \DateTime(
			null,
			new \DateTimeZone( 'Europe/Oslo' )
		);
		$lead_time = $this->lead_time;
		if ( $lead_time && $lead_time > 0 ) {
			if ( (int) $cutoff->format( 'Hi' ) > $this->cutoff_time ) {
				$lead_time += 1;
			}
			$cutoff->add( new \DateInterval( "P{$lead_time}D" ) );
		}

		// Filter available time slots by shipping date.
		foreach ( $time_slot_groups as &$time_slot_group ) {
			foreach ( $time_slot_group['items'] as $key => $item ) {
				$valid = false;
				foreach ( $item->shipping_dates as $shipping_date ) {
					if ( $shipping_date > $cutoff ) {
						$valid = true;
						break;
					}
				}
				if ( ! $valid ) {
					unset( $time_slot_group['items'][ $key ] );
				}
			}
		}

		return $time_slot_groups;
	}

	public function compress_date( $date_array ) {
		$date = new \DateTime(
			"{$date_array['year']}-{$date_array['month']}-{$date_array['day']}T17:00:00",
			new \DateTimeZone( 'Europe/Oslo' )
		);

		return $date;
	}

	public function compress_time_slots( $time_slots ) {
		$new_time_slots = [];
		foreach ( $time_slots as $time_slot ) {
			$new_time_slots[] = sprintf(
				'%02d:%02d-%02d:%02d',
				$time_slot['startTime']['hour'],
				$time_slot['startTime']['minute'],
				$time_slot['endTime']['hour'],
				$time_slot['endTime']['minute'],
			);
		}

		return $new_time_slots;
	}
}
