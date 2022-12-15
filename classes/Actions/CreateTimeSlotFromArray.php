<?php

namespace Bring_Fraktguiden\Actions;

class CreateTimeSlotFromArray
{
	public function __invoke( $time_slots ): array
	{
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
