<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguidenPro\Booking\Bring_Booking;
use BringFraktguidenPro\PickUpPoint\LegacyPickupPoints;
use BringFraktguidenPro\PickUpPoint\PickUpPoint;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'order/class-bring-wc-order-adapter.php';
require_once 'booking/class-bring-booking.php';

if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) {
	LegacyPickupPoints::setup();
	add_action( 'init', PickUpPoint::class.'::init');
}

if ( is_admin() ) {
	if ( 'yes' === Fraktguiden_Helper::get_option( 'booking_enabled' ) ) {
		Bring_Booking::init();
	}
}

// Add admin CSS.
add_action( 'admin_enqueue_scripts', array( 'WC_Shipping_Method_Bring_Pro', 'load_admin_css' ) );

/**
 * WC_Shipping_Method_Bring_Pro class
 */
class WC_Shipping_Method_Bring_Pro extends WC_Shipping_Method_Bring {

	/**
	 * $pickup_point_enabled
	 *
	 * @var string
	 */
	// private $pickup_point_enabled;

	/**
	 * $mybring_api_uid
	 *
	 * @var string
	 */
	private $mybring_api_uid;

	/**
	 * $mybring_api_key
	 *
	 * @var string
	 */
	private $mybring_api_key;

	/**
	 * $booking_enabled
	 *
	 * @var string
	 */
	private $booking_enabled;

	/**
	 * $booking_without_bring
	 *
	 * @var string
	 */
	private $booking_without_bring;

	/**
	 * $booking_address_store_name
	 *
	 * @var string
	 */
	private $booking_address_store_name;

	/**
	 * $booking_address_street1
	 *
	 * @var string
	 */
	private $booking_address_street1;

	/**
	 * $booking_address_street2
	 *
	 * @var string
	 */
	private $booking_address_street2;

	/**
	 * $booking_address_postcode
	 *
	 * @var string
	 */
	private $booking_address_postcode;

	/**
	 * $booking_address_city
	 *
	 * @var string
	 */
	private $booking_address_city;

	/**
	 * $booking_address_country
	 *
	 * @var string
	 */
	private $booking_address_country;

	/**
	 * $booking_address_reference
	 *
	 * @var string
	 */
	private $booking_address_reference;

	/**
	 * $booking_address_contact_person
	 *
	 * @var string
	 */
	private $booking_address_contact_person;

	/**
	 * $booking_address_phone
	 *
	 * @var string
	 */
	private $booking_address_phone;

	/**
	 * $booking_address_email
	 *
	 * @var string
	 */
	private $booking_address_email;

	/**
	 * $booking_test_mode
	 *
	 * @var string
	 */
	private $booking_test_mode;

	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	static $filters_registered = false;

	/**
	 * Construct
	 *
	 * @param integer $instance_id Instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );

		$this->title        = __( 'Bring Fraktguiden', 'bring-fraktguiden-for-woocommerce' );
		$this->method_title = __( 'Bring Fraktguiden', 'bring-fraktguiden-for-woocommerce' );

		// $this->pickup_point_enabled           = $this->get_setting( 'pickup_point_enabled', 'no' );
		$this->mybring_api_uid                = $this->get_setting( 'mybring_api_uid' );
		$this->mybring_api_key                = $this->get_setting( 'mybring_api_key' );
		$this->booking_enabled                = $this->get_setting( 'booking_enabled', 'no' );
		$this->booking_without_bring          = $this->get_setting( 'booking_without_bring', 'no' );
		$this->booking_address_store_name     = $this->get_setting( 'booking_address_store_name', get_bloginfo( 'name' ) );
		$this->booking_address_street1        = $this->get_setting( 'booking_address_street1' );
		$this->booking_address_street2        = $this->get_setting( 'booking_address_street2' );
		$this->booking_address_postcode       = $this->get_setting( 'booking_address_postcode' );
		$this->booking_address_city           = $this->get_setting( 'booking_address_city' );
		$this->booking_address_country        = $this->get_setting( 'booking_address_country' );
		$this->booking_address_reference      = $this->get_setting( 'booking_address_reference' );
		$this->booking_address_contact_person = $this->get_setting( 'booking_address_contact_person' );
		$this->booking_address_phone          = $this->get_setting( 'booking_address_phone' );
		$this->booking_address_email          = $this->get_setting( 'booking_address_email' );
		$this->booking_test_mode              = $this->get_setting( 'booking_test_mode', 'no' );

		if (! self::$filters_registered) {
			add_filter( 'bring_shipping_rates', [ $this, 'filter_shipping_rates' ], 10, 2 );
			add_filter( 'bring_shipping_rates', [ $this, 'filter_shipping_rates_sorting' ], 9000, 2 );
			self::$filters_registered = true;
		}
	}

	/**
	 * Init form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {

		parent::init_form_fields();

		if ( $this->instance_id ) {
			return;
		}
		// $this->init_form_fields_for_pickup_point();
		$this->init_form_fields_for_mybring();
		$this->init_form_fields_for_booking();
	}

	/**
	 * Init form fields for pickup point
	 *
	 * @return void
	 */
	public function init_form_fields_for_pickup_point() {
		$this->form_fields['pickup_point_title'] = [
			'type'        => 'title',
			'title'       => __( 'Pickup Point Options', 'bring-fraktguiden-for-woocommerce' ),
			'description' => __( 'Enable pickup points on the cart / checkout. If disabled, Bring will show the names of shipment methods. <em>ie: <strong>"Climate Neutral Service Pack"</strong></em>', 'bring-fraktguiden-for-woocommerce' ),
			'class'       => 'separated_title_tab',
		];

		$this->form_fields['pickup_point_enabled'] = [
			'title'    => __( 'Enable', 'bring-fraktguiden-for-woocommerce' ),
			'type'     => 'checkbox',
			'desc_tip' => __( 'If not checked, default services will be shown', 'bring-fraktguiden-for-woocommerce' ),
			'label'    => __( 'Enable pickup point', 'bring-fraktguiden-for-woocommerce' ),
			'default'  => 'no',
		];

		$this->form_fields['pickup_point_limit'] = [
			'title'             => __( 'Pickup point limit', 'bring-fraktguiden-for-woocommerce' ),
			'type'              => 'number',
			'css'               => 'width: 8em;',
			'description'       => __( 'Leave blank to remove limit', 'bring-fraktguiden-for-woocommerce' ),
			'custom_attributes' => array( 'min' => 1 ),
			'desc_tip'          => __( 'If set, it will be the maximum number of pickup points shown', 'bring-fraktguiden-for-woocommerce' ),
			'default'           => '',
		];
	}

	/**
	 * Init form fields for Mybring
	 *
	 * @return void
	 */
	public function init_form_fields_for_mybring() {
		$this->form_fields['booking_title'] = [
			'title'       => __( 'Mybring Booking', 'bring-fraktguiden-for-woocommerce' ),
			'description' => '',
			'type'        => 'title',
			'class'       => 'separated_title_tab',
		];

		$this->form_fields['booking_enabled'] = [
			'title'   => __( 'Enable', 'bring-fraktguiden-for-woocommerce' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable Mybring booking', 'bring-fraktguiden-for-woocommerce' ),
			'default' => 'no',
		];

		$this->form_fields['booking_without_bring'] = [
			'title'   => __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ),
			'type'    => 'checkbox',
			'label'   => __( 'Allow booking without Bring shipping', 'bring-fraktguiden-for-woocommerce' ),
			'default' => 'no',
		];

		$this->form_fields['booking_test_mode_enabled'] = [
			'title'       => __( 'Test mode', 'bring-fraktguiden-for-woocommerce' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable test mode for Mybring booking', 'bring-fraktguiden-for-woocommerce' ),
			'description' => __( 'When enabled, Bookings will not be invoiced or fulfilled by Bring', 'bring-fraktguiden-for-woocommerce' ),
			'default'     => 'yes',
		];
		$this->form_fields['booking_status_section_title'] = [
			'type'        => 'title',
			'title'       => __( 'Processing', 'bring-fraktguiden-for-woocommerce' ),
			'description' => __( 'Change order status after booking or printing labels.', 'bring-fraktguiden-for-woocommerce' )
							. ' <span style="color: #c00">'
							. ' <strong>' . __( 'WARNING!', 'bring-fraktguiden-for-woocommerce' ) . '</strong> '
		                    .  __( 'This will change the status even if the order is completed', 'bring-fraktguiden-for-woocommerce' )
		                    . '</span>',
			'class'       => 'bring-separate-admin-section',
		];

		$this->form_fields['auto_set_status_after_booking_success'] = [
			'title'    => __( 'Order status after booking', 'bring-fraktguiden-for-woocommerce' ),
			'type'     => 'select',
			'desc_tip' => __( 'Order status will be automatically set when successfully booked', 'bring-fraktguiden-for-woocommerce' ),
			'class'    => 'chosen_select',
			'css'      => 'width: 400px;',
			'options'  => array( 'none' => __( 'None', 'bring-fraktguiden-for-woocommerce' ) ) + wc_get_order_statuses(),
			'default'  => 'none',
		];
		$this->form_fields['auto_set_status_after_print_label_success'] = [
			'title'    => __( 'Order status after printing', 'bring-fraktguiden-for-woocommerce' ),
			'type'     => 'select',
			'desc_tip' => __( 'Order status will be automatically set when a label is downloaded', 'bring-fraktguiden-for-woocommerce' ),
			'class'    => 'chosen_select',
			'css'      => 'width: 400px;',
			'options'  => array( 'none' => __( 'None', 'bring-fraktguiden-for-woocommerce' ) ) + wc_get_order_statuses(),
			'default'  => 'none',
		];


		$this->form_fields['booking_home_delivery_section_title'] = [
			'type'        => 'title',
			'title'       => __( 'Home delivery', 'bring-fraktguiden-for-woocommerce' ),
			'class'       => 'bring-separate-admin-section',
		];
		$this->form_fields['booking_home_delivery_package_type'] = [
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
	public function init_form_fields_for_booking() {
		$this->form_fields['booking_address_section_title'] = [
			'type'        => 'title',
			'title'       => __( 'Store address and contact information', 'bring-fraktguiden-for-woocommerce' ),
			'description' => __( 'This address will be your \'from\' address and will populate the details given to Bring during the booking process', 'bring-fraktguiden-for-woocommerce' ),
			'class'       => 'bring-separate-admin-section',
		];

		$this->form_fields['booking_address_store_name'] = [
			'title'   => __( 'Store Name', 'bring-fraktguiden-for-woocommerce' ),
			'type'    => 'text',
			'custom_attributes' => [ 'maxlength' => '35' ],
			'default' => get_bloginfo( 'name' ),
		];

		$this->form_fields['booking_address_street1'] = [
			'title'             => __( 'Street Address 1', 'bring-fraktguiden-for-woocommerce' ),
			'custom_attributes' => [ 'maxlength' => '35' ],
			'type'              => 'text',
		];

		$this->form_fields['booking_address_street2'] = [
			'title'             => __( 'Street Address 2', 'bring-fraktguiden-for-woocommerce' ),
			'custom_attributes' => [ 'maxlength' => '35' ],
			'type'              => 'text',
		];

		$this->form_fields['booking_address_postcode'] = [
			'title' => __( 'Postcode', 'bring-fraktguiden-for-woocommerce' ),
			'type'  => 'text',
		];

		$this->form_fields['booking_address_city'] = [
			'title' => __( 'City', 'bring-fraktguiden-for-woocommerce' ),
			'type'  => 'text',
		];

		$this->form_fields['booking_address_country'] = [
			'title'   => __( 'Country', 'bring-fraktguiden-for-woocommerce' ),
			'class'   => 'chosen_select',
			'css'     => 'width: 400px;',
			'type'    => 'select',
			'options' => WC()->countries->get_countries(),
			'default' => WC()->countries->get_base_country(),
		];

		$this->form_fields['booking_address_reference'] = [
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

		$this->form_fields['booking_address_contact_person'] = [
			'title' => __( 'Contact Person', 'bring-fraktguiden-for-woocommerce' ),
			'type'  => 'text',
		];

		$this->form_fields['booking_address_phone'] = [
			'title' => __( 'Phone', 'bring-fraktguiden-for-woocommerce' ),
			'type'  => 'text',
		];

		$this->form_fields['booking_address_email'] = [
			'title'    => __( 'Email', 'bring-fraktguiden-for-woocommerce' ),
			'type'     => 'email'
		];
	}

	/**
	 * Init Settings
	 */
	public function init_settings() {
		parent::init_settings();

		// Remove settings for empty fields so that WooCommerce can populate them with default values.
		if ( isset( $this->settings['booking_address_country'] ) && ! $this->settings['booking_address_country'] ) {
			unset( $this->settings['booking_address_country'] );
		}
	}

	/**
	 * Load admin css
	 */
	public static function load_admin_css() {
		$src = plugins_url( 'assets/css/admin.css', __FILE__ );
		wp_enqueue_style( 'bfg-admin-css', $src, array(), Bring_Fraktguiden::VERSION, false );
	}

	/**
	 * Filter shipping rates
	 * Calculate free shipping and fixed prices
	 *
	 * @param  array                    $rates           Rates.
	 * @param  WC_Shipping_Method_Bring $shipping_method Shipping method.
	 * @return array
	 */
	public function filter_shipping_rates( $rates, $shipping_method ) {
		$field_key  = $this->get_field_key( 'services' );
		$services   = Fraktguiden_Service::all( $field_key );
		$cart       = WC()->cart;
		$cart_items = $cart ? $cart->get_cart() : [];
		$cart_total = 0;

		if ( empty( $rates ) ) {
			return $rates;
		}

		foreach ( $cart_items as $values ) {
			$cart_total += $values['line_total'];
			if ( function_exists( 'wc_prices_include_tax' ) && wc_prices_include_tax() ) {
				$cart_total += $values['line_tax'];
			}
		}

		foreach ( $rates as &$rate ) {
			if ( ! str_starts_with( $rate['id'], 'bring_fraktguiden' ) ) {
				continue;
			}

			$key = strtoupper( $rate['bring_product'] );

			if ( empty( $services[ $key ] ) ) {
				continue;
			}

			$service = $services[ $key ];
			if ( ! empty( $service->settings['custom_name'] ) && empty( $service->settings['pickup_point_cb'] ) ) {
				$rate['label'] = $service->settings['custom_name'];
			}

			if ( $service->settings['custom_price_cb'] ) {
				$rate['cost'] = $this->calculate_excl_vat( $service->settings['custom_price'] );
			}
			if ( $service->settings['additional_fee_cb'] ) {
				$rate['cost'] += $this->calculate_excl_vat( $service->settings['additional_fee'] );
			}
			if ( $service->settings['free_shipping_cb'] ) {
				// Free shipping is checked and threshold is defined.
				$threshold = $service->settings['free_shipping'];
				if ( ! is_numeric( $threshold ) || $cart_total >= $threshold ) {
					// Threshold is not a number (ie. undefined) or
					// cart total is more than or equal to the threshold.
					$rate['cost'] = 0;
				}
			}
		}

		return $rates;
	}

	/**
	 * Sort shipping rates
	 *
	 * @param array $a Shipping rate A.
	 * @param array $b Shipping rate B.
	 *
	 * @return int
	 */
	public static function sort_shipping_rates( $a, $b ) {
		$sorting = Fraktguiden_Helper::get_option( 'service_sorting', 'price' );
		if ( $a['cost'] == $b['cost'] ) {
			return 0;
		}

		if ( $a['cost'] > $b['cost'] ) {
			return ( $sorting === 'price' ) ? 1 : -1;
		}
		return ( $sorting === 'price' ) ? -1 : 1;
	}

	/**
	 * Filter shipping rates for sorting
	 *
	 * @param  array                    $rates           Rates.
	 * @param  WC_Shipping_Method_Bring $shipping_method Shipping method.
	 * @return array
	 */
	public function filter_shipping_rates_sorting( $rates, $shipping_method ) {
		$sorting = Fraktguiden_Helper::get_option( 'service_sorting', 'price' );
		if ( 'none' !== $sorting ) {
			uasort( $rates, __CLASS__ . '::sort_shipping_rates' );
		}
		return $rates;
	}
}
