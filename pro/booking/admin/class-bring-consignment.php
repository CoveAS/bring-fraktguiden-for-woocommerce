<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

abstract class Bring_Consignment {
  public $service_id;
  public $shipping_item;
  public $shipping_date_time;
  public $customer_number;

  function __construct( $shipping_item ) {
    $this->shipping_item = $shipping_item;
    $service_id      = Fraktguiden_Helper::parse_shipping_method_id( $shipping_item['method_id'] )['service'];
    $this->service_id = $service_id;
  }

  public function fill( $args ) {
    $this->customer_number    = $args['customer_number'];
    $this->shipping_date_time = $args['shipping_date_time'];
    return $this;
  }

  /**
   * Get reference
   * @return string
   */
  public function get_reference() {
    $order = $this->shipping_item->get_order();
    $reference = Fraktguiden_Helper::get_option( 'booking_address_reference' );
    return self::parse_sender_address_reference( $reference, $order );
  }

  /**
   * Get sender
   * @return array
   */
  public function get_sender() {
    $form_fields = [
        'booking_address_store_name',
        'booking_address_street1',
        'booking_address_street2',
        'booking_address_postcode',
        'booking_address_city',
        'booking_address_country',
        'booking_address_contact_person',
        'booking_address_phone',
        'booking_address_email',
        'booking_address_reference',
    ];
    // Load sender address data from options.
    $result = [];
    foreach ( $form_fields as $field ) {
      $result[$field] = Fraktguiden_Helper::get_option( $field );
    }
    return $result;
  }

  /**
   * Parses the sender address reference value.
   * Supports simple template macros.
   *
   * Eg. "Order: {order_id}" will be replace {order_id} with the order's ID
   *
   * Available macros:
   *
   *   {order_id}
   *
   * @param string $reference
   * @param WC_Order $wc_order
   * @return mixed
   */
  static function parse_sender_address_reference( $reference, $wc_order ) {
    $replacements = array(
        '{order_id}' => $wc_order->get_id(),
    );
    $result = $reference;
    foreach ( $replacements as $replacement => $value ) {
      $result = preg_replace( "/" . preg_quote( $replacement ) . "/", $value, $result );
    }
    return $result;
  }

  abstract public function get_endpoint_url();
}
