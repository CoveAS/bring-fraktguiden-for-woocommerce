<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */
return [
	// New VAS.
	[
		'enabled'        => true,
		'code'           => '1142',
		'name'           => 'Notification VAS',
		'bring_products' => [ 5000, 4850, 5100, 5300 ],
	],
	[
		'enabled'        => false,
		'code'           => '1000',
		'name'           => 'Cash On Delivery (COD)',
		'bring_products' => [ 5800 ],
	],
	[
		'enabled'        => true,
		'code'           => '2084',
		'name'           => 'Electronic notification',
		'bring_products' => [ 5800 ],
	],
	[
		'enabled'        => true,
		'code'           => '0041',
		'name'           => 'Simplified delivery',
		'bring_products' => [ 5000, 4850, 5100, 5300, 5600 ],
	],
	[
		'enabled'        => true,
		'code'           => '2141',
		'name'           => 'Frost-free',
		'bring_products' => [ 5000, 4850, 5100, 5300 ],
	],
	[
		'enabled'        => true,
		'code'           => '1133',
		'name'           => 'Proof of identity required',
		'bring_products' => [ 5000, 4850, 5800, 5600 ],
	],
	[
		'enabled'        => false,
		'code'           => '2045',
		'name'           => 'Consignment stopped and returned',
		'bring_products' => [ 5000, 4850, 5300, 5800, 5600 ],
	],
	[
		'enabled'        => true,
		'code'           => '1082',
		'name'           => 'Social check',
		'bring_products' => [ 5000, 4850, 5100, 5300, 5800, 5600 ],
	],
	[
		'enabled'        => true,
		'code'           => '2142',
		'name'           => 'Special goods',
		'bring_products' => [ 5100, 5300 ],
	],
	[
		'enabled'        => true,
		'code'           => '0068',
		'name'           => 'Optional insurance',
		'bring_products' => [ 5000, 5800, 5600, 9000 ],
	],
	[
		'enabled'        => true,
		'code'           => '1062',
		'name'           => 'Saturday delivery',
		'bring_products' => [ 4850, 9600 ],
	],
	[
		'enabled'        => true,
		'code'           => '2086',
		'name'           => 'Notification by letter',
		'bring_products' => [ 5800 ],
	],
	[
		'enabled'        => true,
		'code'           => '1245',
		'name'           => 'Dangerous goods',
		'bring_products' => [ 5100, 5300 ],
	],
	// Old VAS.
	[
		'enabled'        => true,
		'code'           => 'EVARSLING',
		'name'           => 'Recipient notification over SMS or E-Mail',
		'bring_products' => [ 'BPAKKE_DOR-DOR', 'SERVICEPAKKE, EKSPRESS09' ],
	],
	[
		'enabled'        => false,
		'code'           => 'POSTOPPKRAV',
		'name'           => 'Cash on Delivery',
		'bring_products' => [ 'MAIL', 'BPAKKE_DOR-DOR', 'SERVICEPAKKE', 'PA_DOREN', 'EKSPRESS09' ],
	],
	[
		'enabled'        => false,
		'code'           => 'LORDAGSUTKJORING',
		'name'           => 'Delivery on Saturdays',
		'bring_products' => 'EKSPRESS09',
	],
	[
		'enabled'        => false,
		'code'           => 'ENVELOPE',
		'name'           => 'Express Envelope',
		'bring_products' => [ 'EXPRESS_INTERNATIONAL_0900', 'EXPRESS_INTERNATIONAL_1200', 'EXPRESS_INTERNATIONAL' ],
	],
	[
		'enabled'        => false,
		'code'           => 'ADVISERING',
		'name'           => 'Bring contacts recipient',
		'bring_products' => 'CARGO_GROUPAGE',
	],
	[
		'enabled'        => true,
		'code'           => 'PICKUP_POINT',
		'name'           => 'Delivery to pickup point',
		'bring_products' => [ 'PICKUP_PARCEL', 'PICKUP_PARCEL_BULK' ],
	],
	[
		'enabled'        => false,
		'code'           => 'EVE_DELIVERY',
		'name'           => 'Evening delivery',
		'bring_products' => [ 'CARGO', 'CARGO_GROUPAGE' ],
	],
	[
		'enabled'        => false,
		'code'           => 'SIMPLIFIED_DELIVERY',
		'name'           => 'Simplified delivery',
		'bring_products' => [ 'PAKKE_I_POSTKASSEN', 'PAKKE_I_POSTKASSEN_SPORBAR' ],
	],
];
