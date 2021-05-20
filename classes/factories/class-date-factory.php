<?php

namespace Bring_Fraktguiden\Factories;

class Date_Factory {
	public function from_array( $date_array ) {
		$date_array = wp_parse_args(
			$date_array,
			[
				'hour'   => 0,
				'minute' => 0,
				'second' => 0,
			]
		);

		$date = new \DateTime(
			sprintf(
				'%04d-%02d-%02dT%02d:%02d:%02d',
				$date_array['year'],
				$date_array['month'],
				$date_array['day'],
				$date_array['hour'],
				$date_array['minute'],
				$date_array['second']
			),
			new \DateTimeZone( 'Europe/Oslo' )
		);

		return $date;
	}
}
