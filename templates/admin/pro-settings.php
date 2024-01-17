<?php


/**
 * Init form fields for Mybring
 *
 * @return void
 */
function init_form_fields_for_mybring() {
	$form_fields['booking_title'] = [
		'title'       => __( 'Mybring Booking', 'bring-fraktguiden-for-woocommerce' ),
		'description' => '',
		'type'        => 'title',
		'class'       => 'separated_title_tab',
	];

	$form_fields['booking_enabled'] = [
		'title'   => __( 'Enable', 'bring-fraktguiden-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable Mybring booking', 'bring-fraktguiden-for-woocommerce' ),
		'default' => 'no',
	];

	$form_fields['booking_without_bring'] = [
		'title'   => __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Allow booking without Bring shipping', 'bring-fraktguiden-for-woocommerce' ),
		'default' => 'no',
	];

	$form_fields['booking_test_mode_enabled'] = [
		'title'       => __( 'Test mode', 'bring-fraktguiden-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable test mode for Mybring booking', 'bring-fraktguiden-for-woocommerce' ),
		'description' => __( 'When enabled, Bookings will not be invoiced or fulfilled by Bring', 'bring-fraktguiden-for-woocommerce' ),
		'default'     => 'yes',
	];
	$form_fields['booking_status_section_title'] = [
		'type'        => 'title',
		'title'       => __( 'Processing', 'bring-fraktguiden-for-woocommerce' ),
		'description' => __( 'Change order status after booking or printing labels.', 'bring-fraktguiden-for-woocommerce' )
			. ' <span style="color: #c00">'
			. ' <strong>' . __( 'WARNING!', 'bring-fraktguiden-for-woocommerce' ) . '</strong> '
			.  __( 'This will change the status even if the order is completed', 'bring-fraktguiden-for-woocommerce' )
			. '</span>',
		'class'       => 'bring-separate-admin-section',
	];

	$form_fields['auto_set_status_after_booking_success'] = [
		'title'    => __( 'Order status after booking', 'bring-fraktguiden-for-woocommerce' ),
		'type'     => 'select',
		'desc_tip' => __( 'Order status will be automatically set when successfully booked', 'bring-fraktguiden-for-woocommerce' ),
		'class'    => 'chosen_select',
		'css'      => 'width: 400px;',
		'options'  => array( 'none' => __( 'None', 'bring-fraktguiden-for-woocommerce' ) ) + wc_get_order_statuses(),
		'default'  => 'none',
	];
	$form_fields['auto_set_status_after_print_label_success'] = [
		'title'    => __( 'Order status after printing', 'bring-fraktguiden-for-woocommerce' ),
		'type'     => 'select',
		'desc_tip' => __( 'Order status will be automatically set when a label is downloaded', 'bring-fraktguiden-for-woocommerce' ),
		'class'    => 'chosen_select',
		'css'      => 'width: 400px;',
		'options'  => array( 'none' => __( 'None', 'bring-fraktguiden-for-woocommerce' ) ) + wc_get_order_statuses(),
		'default'  => 'none',
	];


	$form_fields['booking_home_delivery_section_title'] = [
		'type'        => 'title',
		'title'       => __( 'Home delivery', 'bring-fraktguiden-for-woocommerce' ),
		'class'       => 'bring-separate-admin-section',
	];
	$form_fields['booking_home_delivery_package_type'] = [
		'title'    => __( 'Package type for home delivery', 'bring-fraktguiden-for-woocommerce' ),
		'type'     => 'select',
		'desc_tip' => __( 'Only applies to home delivery services', 'bring-fraktguiden-for-woocommerce' ),
		'options'  => [
			'hd_eur'     => 'HD_EUR_PALLET',
			'hd_half'    => 'HD_HALF_PALLET',
			'hd_quarter' => 'HD_QUARTER_PALLET',
			'hd_loose'   => 'HD_SPECIAL_PALLET',
		],
		'default'  => 'hd_eur',
	];

}
/**
 * Init form fields for Booking
 *
 * @return void
 */
 function init_form_fields_for_booking() {
	$form_fields['booking_address_section_title'] = [
		'type'        => 'title',
		'title'       => __( 'Store address and contact information', 'bring-fraktguiden-for-woocommerce' ),
		'description' => __( 'This address will be your \'from\' address and will populate the details given to Bring during the booking process', 'bring-fraktguiden-for-woocommerce' ),
		'class'       => 'bring-separate-admin-section',
	];

	$form_fields['booking_address_store_name'] = [
		'title'   => __( 'Store Name', 'bring-fraktguiden-for-woocommerce' ),
		'type'    => 'text',
		'custom_attributes' => [ 'maxlength' => '35' ],
		'default' => get_bloginfo( 'name' ),
	];

	$form_fields['booking_address_street1'] = [
		'title'             => __( 'Street Address 1', 'bring-fraktguiden-for-woocommerce' ),
		'custom_attributes' => [ 'maxlength' => '35' ],
		'type'              => 'text',
	];

	$form_fields['booking_address_street2'] = [
		'title'             => __( 'Street Address 2', 'bring-fraktguiden-for-woocommerce' ),
		'custom_attributes' => [ 'maxlength' => '35' ],
		'type'              => 'text',
	];

	$form_fields['booking_address_postcode'] = [
		'title' => __( 'Postcode', 'bring-fraktguiden-for-woocommerce' ),
		'type'  => 'text',
	];

	$form_fields['booking_address_city'] = [
		'title' => __( 'City', 'bring-fraktguiden-for-woocommerce' ),
		'type'  => 'text',
	];

	$form_fields['booking_address_country'] = [
		'title'   => __( 'Country', 'bring-fraktguiden-for-woocommerce' ),
		'class'   => 'chosen_select',
		'css'     => 'width: 400px;',
		'type'    => 'select',
		'options' => WC()->countries->get_countries(),
		'default' => WC()->countries->get_base_country(),
	];

	$form_fields['booking_address_reference'] = [
		'title'             => __( 'Reference', 'bring-fraktguiden-for-woocommerce' ),
		'type'              => 'text',
		'custom_attributes' => array( 'maxlength' => '35' ),
		'description'       => sprintf(
			__(
				'Specify shipper or consignee reference. Available macros: %s',
				'bring-fraktguiden-for-woocommerce'
			),
			'{order_id}, {products}'
		),
	];

	$form_fields['booking_address_contact_person'] = [
		'title' => __( 'Contact Person', 'bring-fraktguiden-for-woocommerce' ),
		'type'  => 'text',
	];

	$form_fields['booking_address_phone'] = [
		'title' => __( 'Phone', 'bring-fraktguiden-for-woocommerce' ),
		'type'  => 'text',
	];

	$form_fields['booking_address_email'] = [
		'title'    => __( 'Email', 'bring-fraktguiden-for-woocommerce' ),
		'type'     => 'email'
	];
}

/**
 * Init form fields for pickup point
 *
 * @return void
 */
function init_form_fields_for_pickup_point() {
	$form_fields['pickup_point_title'] = [
		'type'        => 'title',
		'title'       => __( 'Pickup Point Options', 'bring-fraktguiden-for-woocommerce' ),
		'description' => __( 'Enable pickup points on the cart / checkout. If disabled, Bring will show the names of shipment methods. <em>ie: <strong>"Climate Neutral Service Pack"</strong></em>', 'bring-fraktguiden-for-woocommerce' ),
		'class'       => 'separated_title_tab',
	];

	$form_fields['pickup_point_enabled'] = [
		'title'    => __( 'Enable', 'bring-fraktguiden-for-woocommerce' ),
		'type'     => 'checkbox',
		'desc_tip' => __( 'If not checked, default services will be shown', 'bring-fraktguiden-for-woocommerce' ),
		'label'    => __( 'Enable pickup point', 'bring-fraktguiden-for-woocommerce' ),
		'default'  => 'no',
	];

	$form_fields['pickup_point_limit'] = [
		'title'             => __( 'Pickup point limit', 'bring-fraktguiden-for-woocommerce' ),
		'type'              => 'number',
		'css'               => 'width: 8em;',
		'description'       => __( 'Leave blank to remove limit', 'bring-fraktguiden-for-woocommerce' ),
		'custom_attributes' => array( 'min' => 1 ),
		'desc_tip'          => __( 'If set, it will be the maximum number of pickup points shown', 'bring-fraktguiden-for-woocommerce' ),
		'default'           => '',
	];
}
