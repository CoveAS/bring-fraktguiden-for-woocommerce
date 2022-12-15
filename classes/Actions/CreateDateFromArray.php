<?php
namespace Bring_Fraktguiden\Actions;

use DateTime;
use DateTimeZone;
use Exception;

class CreateDateFromArray
{
	/**
	 * @throws Exception
	 */
	public function __invoke(array $dateData): DateTime
	{
		$dateData = wp_parse_args(
			$dateData,
			[
				'hour'   => 0,
				'minute' => 0,
				'second' => 0,
			]
		);

		return new DateTime(
			sprintf(
				'%04d-%02d-%02dT%02d:%02d:%02d',
				$dateData['year'],
				$dateData['month'],
				$dateData['day'],
				$dateData['hour'],
				$dateData['minute'],
				$dateData['second']
			),
			new DateTimeZone( 'Europe/Oslo' )
		);
	}
}
