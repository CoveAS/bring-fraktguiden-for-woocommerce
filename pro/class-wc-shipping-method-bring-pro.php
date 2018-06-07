<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

include_once 'order/class-bring-wc-order-adapter.php';
include_once 'pickuppoint/class-fraktguiden-pickup-point.php';
include_once 'booking/class-bring-booking.php';

Fraktguiden_Pickup_Point::init();

if ( is_admin() ) {
  if ( Fraktguiden_Helper::get_option( 'booking_enabled' ) == 'yes' ) {
    Bring_Booking::init();
  }
}

# Add admin css
add_action( 'admin_enqueue_scripts', array( 'WC_Shipping_Method_Bring_Pro', 'load_admin_css' ) );

class WC_Shipping_Method_Bring_Pro extends WC_Shipping_Method_Bring {

  private $pickup_point_enabled;
  private $mybring_api_uid;
  private $mybring_api_key;
  private $booking_enabled;
  private $booking_address_store_name;
  private $booking_address_street1;
  private $booking_address_street2;
  private $booking_address_postcode;
  private $booking_address_city;
  private $booking_address_country;
  private $booking_address_reference;
  private $booking_address_contact_person;
  private $booking_address_phone;
  private $booking_address_email;
  private $booking_test_mode;

  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

  public function __construct( $instance_id = 0 ) {

    parent::__construct( $instance_id );

    $this->title        = __( 'Bring Fraktguiden', 'bring-fraktguiden' );
    $this->method_title = __( 'Bring Fraktguiden', 'bring-fraktguiden' );

    $this->pickup_point_enabled           = $this->get_setting( 'pickup_point_enabled', 'no' );
    $this->mybring_api_uid                = $this->get_setting( 'mybring_api_uid' );
    $this->mybring_api_key                = $this->get_setting( 'mybring_api_key' );
    $this->booking_enabled                = $this->get_setting( 'booking_enabled', 'no' );
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

    add_filter( 'bring_shipping_rates', [$this, 'filter_shipping_rates'] );
  }

  public function init_form_fields() {

    parent::init_form_fields();

    // *************************************************************************
    // Pickup Point
    // *************************************************************************

    $this->form_fields['pickup_point_title'] = [
        'type'        => 'title',
        'title'       => __( 'Pickup Point Options', 'bring-fraktguiden' ),
        'description' => __( 'Enable pickup points on the cart / checkout. If disabled, Bring will show the names of shipment methods. <em>ie: <strong>"Climate Neutral Service Pack"</strong></em>', 'bring-fraktguiden' ),
        'class'       => 'separated_title_tab',
    ];

    $this->form_fields['pickup_point_enabled'] = [
        'title'   => __( 'Enable', 'bring-fraktguiden' ),
        'type'    => 'checkbox',
        'desc_tip' => __( 'If not checked, default services will be shown', 'bring-fraktguiden' ),
        'label'   => __( 'Enable pickup point', 'bring-fraktguiden' ),
        'default' => 'no',
    ];

    $this->form_fields['pickup_point_limit'] = [
        'title'   => __( 'Pickup point limit', 'bring-fraktguiden' ),
        'type'    => 'number',
        'css'      => 'width: 8em;',
        'description' => __( 'Leave blank to remove limit', 'bring-fraktguiden' ),
        'custom_attributes' => array( 'min' => 1 ),
        'desc_tip' => __( 'If set, it will be the maximum number of pickup points shown', 'bring-fraktguiden' ),
        'default' => '',
    ];

    if ( $this->instance_id ) {
      return;
    }

    // *************************************************************************
    // MyBring
    // *************************************************************************

    $has_api_uid_and_key = Fraktguiden_Helper::get_option( 'mybring_api_uid' ) && Fraktguiden_Helper::get_option( 'mybring_api_key' );

    $description = sprintf( __( 'In order to use Bring Booking you must be registered in <a href="%s" target="_blank">MyBring</a> and have an invoice agreement with Bring', 'bring-fraktguiden' ), 'http://mybring.com/' );
    if ( ! $has_api_uid_and_key && Fraktguiden_Helper::booking_enabled() ) {
      $description .= '<br><span style="font-weight: bold;color: red">' . __( 'API User ID or API Key missing!', 'bring-fraktguiden' ) . '</span>';
    }

    $this->form_fields['booking_title'] = [
        'title'       => __( 'MyBring Booking', 'bring-fraktguiden' ),
        'description' => $description,
        'type'        => 'title',
        'class'       => 'separated_title_tab',
    ];

    $this->form_fields['booking_enabled'] = [
        'title'   => __( 'Enable', 'bring-fraktguiden' ),
        'type'    => 'checkbox',
        'label'   => __( 'Enable MyBring booking', 'bring-fraktguiden' ),
        'default' => 'no'
    ];

    $this->form_fields['booking_test_mode_enabled'] = [
        'title'       => __( 'Test mode', 'bring-fraktguiden' ),
        'type'        => 'checkbox',
        'label'       => __( 'Enable test mode for MyBring booking', 'bring-fraktguiden' ),
        'description' => __( 'When enabled, Bookings will not be invoiced or fulfilled by Bring', 'bring-fraktguiden' ),
        'default'     => 'yes'
    ];

    $this->form_fields['booking_status_section_title'] = [
      'type'        => 'title',
      'title'       => __( 'After booking', 'bring-fraktguiden' ),
      'description' => __( 'Once an order is booked, it will be assigned the following status:', 'bring-fraktguiden' ),
      'class'       => 'bring-separate-admin-section',
    ];

    $this->form_fields['auto_set_status_after_booking_success'] = [
        'title'       => __( 'Order status after booking', 'bring-fraktguiden' ),
        'type'        => 'select',
        'desc_tip' => __( 'Order status will be automatically set when successfully booked', 'bring-fraktguiden' ),
        'class'       => 'chosen_select',
        'css'         => 'width: 400px;',
        'options'     => array( 'none' => __( 'None', 'bring-fraktguiden' ) ) + wc_get_order_statuses(),
        'default'     => 'none'
    ];

    // *************************************************************************
    // Booking
    // *************************************************************************

    $this->form_fields['booking_address_section_title'] = [
      'type'        => 'title',
      'title'       => __( 'Store address and contact information', 'bring-fraktguiden' ),
      'description' => __( 'This address will be your \'from\' address and will populate the details given to Bring during the booking process', 'bring-fraktguiden' ),
      'class'       => 'bring-separate-admin-section',
    ];

    $this->form_fields['booking_address_store_name'] = [
        'title'       => __( 'Store Name', 'bring-fraktguiden' ),
        'type'        => 'text',
        'placeholder' => get_bloginfo( 'name' ),
        'default'     => get_bloginfo( 'name' )
    ];

    $this->form_fields['booking_address_street1'] = [
        'title'             => __( 'Street Address 1', 'bring-fraktguiden' ),
        'custom_attributes' => array( 'maxlength' => '35' ),
        'type'              => 'text',
    ];

    $this->form_fields['booking_address_street2'] = [
        'title'             => __( 'Street Address 2', 'bring-fraktguiden' ),
        'custom_attributes' => array( 'maxlength' => '35' ),
        'type'              => 'text',
    ];

    $this->form_fields['booking_address_postcode'] = [
        'title' => __( 'Postcode', 'bring-fraktguiden' ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_city'] = [
        'title' => __( 'City', 'bring-fraktguiden' ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_country'] = [
        'title'    => __( 'Country', 'bring-fraktguiden' ),
        'class'    => 'chosen_select',
        'css'      => 'width: 400px;',
        'type'     => 'select',
        'options'  => WC()->countries->get_countries(),
        'default'  => WC()->countries->get_base_country(),
    ];

    $this->form_fields['booking_address_reference'] = [
        'title'             => __( 'Reference', 'bring-fraktguiden' ),
        'type'              => 'text',
        'custom_attributes' => array( 'maxlength' => '35' ),
        'description'       => __( 'Specify shipper or consignee reference. Available macros: {order_id}', 'bring-fraktguiden' )
    ];

    $this->form_fields['booking_address_contact_person'] = [
        'title' => __( 'Contact Person', 'bring-fraktguiden' ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_phone'] = [
        'title' => __( 'Phone', 'bring-fraktguiden' ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_email'] = [
        'title' => __( 'Email', 'bring-fraktguiden' ),
        'type'  => 'text',
    ];

  }

  /**
   * Init Settings
   */
  public function init_settings() {
    parent::init_settings();
    // Remove settings for empty fields so that WooCommerce can populate them with default values
    if ( ! $this->settings['booking_address_country'] ) {
      unset( $this->settings['booking_address_country'] );
    }
  }

  /**
   * Load admin css
   */
  static function load_admin_css() {
    $src = plugins_url( 'assets/css/admin.css', __FILE__ );
    wp_register_script( 'bfg-admin-css', $src, array(), Bring_Fraktguiden::VERSION );
    wp_enqueue_style( 'bfg-admin-css', $src, array(), Bring_Fraktguiden::VERSION, false );
  }

  /**
   * Filter shipping rates
   * Calculate free shipping and fixed prices
   *
   * @param  array $rates
   * @return array
   */
  public function filter_shipping_rates( $rates ) {
    $field_key                = $this->get_field_key( 'services' );
    $custom_prices            = get_option( $field_key . '_custom_prices' );
    $free_shipping_checks     = get_option( $field_key . '_free_shipping_checks' );
    $free_shipping_thresholds = get_option( $field_key . '_free_shipping_thresholds' );
    $cart                     = WC()->cart;


    $cart_items               = $cart ? $cart->get_cart() : [];
    $cart_total               = 0;

    foreach ( $cart_items as $cart_item_key => $values ) {
      $_product = $values['data'];
      $cart_total += $_product->get_price() * $values['quantity'];
    }
    if ( empty( $rates )  ) {
      return $rates;
    }
    foreach ( $rates as &$rate ) {
      if ( ! preg_match( '/^bring_fraktguiden:(.+)$/', $rate['id'], $matches ) ) {
        continue;
      }
      $key = strtoupper( $matches[1] );
      if ( 0 === strpos( $key, 'SERVICEPAKKE' ) ) {
        $key = 'SERVICEPAKKE';
      }
      if ( isset( $custom_prices[$key] ) && ctype_digit( $custom_prices[$key] ) ) {
        $rate['cost'] = floatval( $custom_prices[$key] );
      }
      if (
          isset( $free_shipping_checks[$key] ) &&
          'on' == $free_shipping_checks[$key] &&
          isset( $free_shipping_thresholds[$key] )
      ) {
        // Free shipping is checked and threshold is defined
        $threshold = $free_shipping_thresholds[$key];
        if ( ! ctype_digit( $threshold ) || $cart_total >= $threshold ) {
          // Threshold is not a number (ie. undefined) or
          // cart total is more than or equal to the threshold
          $rate['cost'] = 0;
        }
      }
    }
    return $rates;
  }
}
