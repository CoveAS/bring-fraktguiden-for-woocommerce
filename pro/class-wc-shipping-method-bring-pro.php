<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

include_once 'order/class-bring-wc-order-adapter.php';
include_once 'pickuppoint/class-fraktguiden-pickup-point.php';
include_once 'booking/class-bring-booking.php';

if ( Fraktguiden_Helper::get_option( 'pickup_point_enabled' ) == 'yes' ) {
  Fraktguiden_Pickup_Point::init();
}

if ( is_admin() ) {
  if ( Fraktguiden_Helper::get_option( 'booking_enabled' ) == 'yes' ) {
    Bring_Booking::init();
  }
}

# Add admin css
add_action( 'admin_enqueue_scripts', array( 'WC_Shipping_Method_Bring_Pro', 'load_admin_css' ) );

class WC_Shipping_Method_Bring_Pro extends WC_Shipping_Method_Bring {

  private $pickup_point_enabled;
  private $pickup_point_required;
  private $mybring_api_uid;
  private $mybring_api_key;
  private $booking_enabled;
  private $booking_address_store_name;
  private $booking_address_street1;
  private $booking_address_street2;
  private $booking_address_postcode;
  private $booking_address_city;
  private $booking_address_country;
  private $booking_address_contact_person;
  private $booking_address_phone;
  private $booking_address_email;
  private $booking_test_mode;

  public function __construct($instance_id = 0) {

    parent::__construct($instance_id);

    $this->title        = __( 'Bring Fraktguiden Pro', self::TEXT_DOMAIN );
    $this->method_title = __( 'Bring Fraktguiden Pro', self::TEXT_DOMAIN );

    $this->pickup_point_enabled  = array_key_exists( 'pickup_point_enabled', $this->settings ) ? $this->settings['pickup_point_enabled'] : 'no';
    $this->pickup_point_required = array_key_exists( 'pickup_point_required', $this->settings ) ? $this->settings['pickup_point_required'] : 'no';

    $this->mybring_api_uid = array_key_exists( 'mybring_api_uid', $this->settings ) ? $this->settings['mybring_api_uid'] : '';
    $this->mybring_api_key = array_key_exists( 'mybring_api_key', $this->settings ) ? $this->settings['mybring_api_key'] : '';

    $this->booking_enabled                = array_key_exists( 'booking_enabled', $this->settings ) ? $this->settings['booking_enabled'] : 'no';
    $this->booking_address_store_name     = array_key_exists( 'booking_address_store_name', $this->settings ) ? $this->settings['booking_address_store_name'] : get_bloginfo( 'name' );
    $this->booking_address_street1        = array_key_exists( 'booking_address_street1', $this->settings ) ? $this->settings['booking_address_street1'] : '';
    $this->booking_address_street2        = array_key_exists( 'booking_address_street2', $this->settings ) ? $this->settings['booking_address_street2'] : '';
    $this->booking_address_postcode       = array_key_exists( 'booking_address_postcode', $this->settings ) ? $this->settings['booking_address_postcode'] : '';
    $this->booking_address_city           = array_key_exists( 'booking_address_city', $this->settings ) ? $this->settings['booking_address_city'] : '';
    $this->booking_address_country        = array_key_exists( 'booking_address_country', $this->settings ) ? $this->settings['booking_address_country'] : '';
    $this->booking_address_contact_person = array_key_exists( 'booking_address_contact_person', $this->settings ) ? $this->settings['booking_address_contact_person'] : '';
    $this->booking_address_phone          = array_key_exists( 'booking_address_phone', $this->settings ) ? $this->settings['booking_address_phone'] : '';
    $this->booking_address_email          = array_key_exists( 'booking_address_email', $this->settings ) ? $this->settings['booking_address_email'] : '';

    $this->booking_test_mode = array_key_exists( 'booking_test_mode', $this->settings ) ? $this->settings['booking_test_mode'] : 'no';
  }

  public function init_form_fields() {

    global $woocommerce;

    parent::init_form_fields();

    // *************************************************************************
    // Pickup Point
    // *************************************************************************

    $this->form_fields['pickup_point_title'] = [
        'type'  => 'title',
        'title' => __( 'Pickup Point Options', self::TEXT_DOMAIN )
    ];

    $this->form_fields['pickup_point_enabled'] = [
        'title'   => __( 'Enable', self::TEXT_DOMAIN ),
        'type'    => 'checkbox',
        'label'   => __( 'Enable pickup point', self::TEXT_DOMAIN ),
        'default' => 'no'
    ];

    $this->form_fields['pickup_point_required'] = [
        'title'   => __( 'Required', self::TEXT_DOMAIN ),
        'type'    => 'checkbox',
        'label'   => __( 'Make pickup point required on checkout', self::TEXT_DOMAIN ),
        'default' => 'no'
    ];

    // *************************************************************************
    // MyBring
    // *************************************************************************

    $has_api_uid_and_key = Fraktguiden_Helper::get_option( 'mybring_api_uid' ) && Fraktguiden_Helper::get_option( 'mybring_api_key' );

    $description = sprintf( __( 'In order to use Bring Booking you must be registered in <a href="%s" target="_blank">MyBring</a> and have an invoice agreement with Bring', self::TEXT_DOMAIN ), 'http://mybring.com/' );
    if ( ! $has_api_uid_and_key ) {
      $description .= '<p style="font-weight: bold;color: red">' . __( 'API User ID or API Key missing!', self::TEXT_DOMAIN ) . '</p>';
    }

    $this->form_fields['mybring_title'] = [
        'title'       => __( 'MyBring Account', self::TEXT_DOMAIN ),
        'description' => $description,
        'type'        => 'title'
    ];

    $this->form_fields['mybring_api_uid'] = [
        'title' => __( 'API User ID', self::TEXT_DOMAIN ),
        'type'  => 'text',
        'label' => __( 'API User ID', self::TEXT_DOMAIN ),
    ];

    $this->form_fields['mybring_api_key'] = [
        'title' => __( 'API Key', self::TEXT_DOMAIN ),
        'type'  => 'text',
        'label' => __( 'API Key', self::TEXT_DOMAIN ),
    ];

    // *************************************************************************
    // Booking
    // *************************************************************************

    $this->form_fields['booking_point_title'] = [
        'title' => __( 'Booking Options', self::TEXT_DOMAIN ),
        'type'  => 'title'
    ];

    $this->form_fields['booking_enabled'] = [
        'title'   => __( 'Enable', self::TEXT_DOMAIN ),
        'type'    => 'checkbox',
        'label'   => __( 'Enable booking', self::TEXT_DOMAIN ),
        'default' => 'no'
    ];

    $this->form_fields['booking_test_mode_enabled'] = [
        'title'       => __( 'Testing', self::TEXT_DOMAIN ),
        'type'        => 'checkbox',
        'label'       => __( 'Test mode', self::TEXT_DOMAIN ),
        'description' => __( 'For testing. Bookings will not be invoiced', self::TEXT_DOMAIN ),
        'default'     => 'yes'
    ];

    $this->form_fields['booking_address_store_name'] = [
        'title'   => __( 'Store Name', self::TEXT_DOMAIN ),
        'type'    => 'text',
        'default' => get_bloginfo( 'name' )
    ];

    $this->form_fields['booking_address_street1'] = [
        'title' => __( 'Street Address 1', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_street2'] = [
        'title' => __( 'Street Address 2', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_postcode'] = [
        'title' => __( 'Postcode', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_city'] = [
        'title' => __( 'City', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_country'] = [
        'title'   => __( 'Country', self::TEXT_DOMAIN ),
        'type'    => 'select',
        'class'   => 'chosen_select',
        'css'     => 'width: 450px;',
        'default' => $woocommerce->countries->get_base_country(),
        'options' => $woocommerce->countries->countries
    ];

    $this->form_fields['booking_address_contact_person'] = [
        'title' => __( 'Contact Person', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_phone'] = [
        'title' => __( 'Phone', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['booking_address_email'] = [
        'title' => __( 'Email', self::TEXT_DOMAIN ),
        'type'  => 'text',
    ];

    $this->form_fields['auto_set_status_after_booking_success'] = [
        'title'       => __( 'Order status after booking', self::TEXT_DOMAIN ),
        'type'        => 'select',
        'description' => __( 'Order status to automatically set after successful booking', self::TEXT_DOMAIN ),
        'class'       => 'chosen_select',
        'css'         => 'width: 450px;',
        'options'     => array( 'none' => __( 'None', self::TEXT_DOMAIN ) ) + wc_get_order_statuses(),
        'default'     => 'none'
    ];
  }

  /**
   * Load admin css
   */
  static function load_admin_css() {
    $src = plugins_url( 'assets/css/admin.css', __FILE__ );
    wp_register_script( 'bfg-admin-css', $src, array(), '##VERSION##' );
    wp_enqueue_style( 'bfg-admin-css', $src, array(), '##VERSION##', false );
  }

}
