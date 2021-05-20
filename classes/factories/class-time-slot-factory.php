<?php

namespace Bring_Fraktguiden\Factories;

class Time_Slot_Factory {
	public function from_array( $time_slots ) {
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
