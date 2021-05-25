<?php

namespace Bring_Fraktguiden\Factories;

use Bring_Fraktguiden\Models\Alternative_Delivery_Date;

class Alternative_Delivery_Date_Factory {
	protected $date_factory;
	protected $time_slot_factory;

	public function __construct() {
		$this->date_factory = new Date_Factory();
		$this->time_slot_factory = new Time_Slot_Factory();
	}

	public function from_array( $alternative_delivery_dates ) {
		$time_slot_groups = [];

		foreach ( $alternative_delivery_dates as $date_data ) {
			$date                         = new Alternative_Delivery_Date();
			$date->working_days           = $date_data['workingDays'];
			$date->expected_delivery_date = $this->date_factory->from_array( $date_data['expectedDeliveryDate'] );
			$date->time_slots             = $this->time_slot_factory->from_array( $date_data['expectedDeliveryDate']['timeSlots'] );
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
				$time_slot_groups[ $time_slot ]['items'][ $key ]->shipping_dates[] = $this->date_factory->from_array( $date_data['shippingDate'] );
			}
		}
		foreach ( $time_slot_groups as &$time_slot_group ) {
			ksort( $time_slot_group['items'], SORT_NATURAL );
		}
		unset( $time_slot_group );

		return $time_slot_groups;
	}
}
