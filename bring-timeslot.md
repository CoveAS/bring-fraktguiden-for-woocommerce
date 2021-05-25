```php
<?php

$params = apply_filters( 'bring_fraktguiden_api_parameters', $params, $this );

$params['uniqueAlternateDeliveryDates']=true;
$params['numberofdeliverydates']=20;
		
add_action(
	'kco_update_shipping_data',
	function($data) {
		$data['delivery_details']['timeslot']
	}
);

// Pakke levert hjem & På Døren
add_filter(
	'kco_wc_shipping_options',
	function(array $shipping_options) {
		$shipping_options[] = array(
			// 'id'          => $method_id,
			// 'name'        => $method_name,
			// 'price'       => $method_price,
			// 'tax_amount'  => $method_tax_amount,
			// 'tax_rate'    => $method_tax_rate,
			// 'preselected' => $method_selected,
			'delivery_details' => [
				'carrier' => 'Bring',
				'product' => [
					'name'       => 'Pakke levert hjem',
					'identifier' => '5600',
				],
				'timeslot' => [
					[
						'id'    => '2021042109_8',              // Date + diff YYYYMMDDHH_{17-9}
						'start' => 'Lørdag 21. April 09:00',
						'end'   => '17:00',
					],
					[
						'id'    => '2021042117_5',
						'start' => 'Lørdag 21. April 17:00',
						'end'   => '22:00',
					],
				],
				'selected_addons' => [
					[
						'type'        => '',
						'price'       => '0',
						'external_id' => '',
						'user_input'  => '',
					]
				]
			]
		);
	}
);
```

Booking information:

customerSpecifiedDeliveryDateTime	dateTime	Optional The date and time selected for preferred delivery by the customer. Note that for some services, the time part of this field will be ignored. This field is applicable for ‘PA_DOREN’, ‘5600’, and ‘OIL_EXPRESS’.
Example: 2020-05-11T11:12:13 (yyyy-MM-ddThh:mm:ss)
