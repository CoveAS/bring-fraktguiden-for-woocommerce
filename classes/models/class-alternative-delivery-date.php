<?php

namespace Bring_Fraktguiden\Models;

class Alternative_Delivery_Date {
	public $working_days;
	public $shipping_date;
	public $expected_delivery_date;
	public $time_slots;

	public function date( $format = 'j F') {
		return wp_date($format, $this->expected_delivery_date->getTimestamp(), $this->expected_delivery_date->getTimezone() );
	}
}
