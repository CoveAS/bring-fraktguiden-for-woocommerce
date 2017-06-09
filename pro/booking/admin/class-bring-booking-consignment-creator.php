<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Consignment_Creator {

  /** @var int */
  protected $purchase_order;
  /** @var int */
  protected $shipping_date_time;
  /** @var array */
  protected $sender_address;
  /** @var array */
  protected $recipient_address;
  /** @var array */
  protected $pickup_point;
  /** @var array */
  protected $product_services;
  /** @var string */
  protected $product_id;
  /** @var string */
  protected $customer_number;
  /** @var array */
  protected $packages;

  public function __construct() {
    /**/
  }

  /**
   * @param int $purchase_order
   * @return $this
   */
  public function set_purchase_order( $purchase_order ) {
    $this->purchase_order = $purchase_order;
    return $this;
  }

  /**
   * @return int
   */
  public function get_purchase_order( ) {
    return $this->purchase_order;
  }

  /**
   * @param int $shipping_date_time
   * @return $this
   */
  public function set_shipping_date_time( $shipping_date_time ) {
    $this->shipping_date_time = $shipping_date_time;
    return $this;
  }

  /**
   * @return int
   */
  public function get_shipping_date_time() {
    return $this->shipping_date_time;
  }

  /**
   * @param array $sender_address
   * @return $this
   */
  public function set_sender_address( $sender_address ) {
    $this->sender_address = $sender_address;
    return $this;
  }

  /**
   * @return array
   */
  public function get_sender_address() {
    return $this->sender_address;
  }

  /**
   * @param array $recipient_address
   * @return $this
   */
  public function set_recipient_address( $recipient_address ) {
    $this->recipient_address = $recipient_address;
    return $this;
  }

  /**
   * @return array
   */
  public function get_recipient_address() {
    return $this->recipient_address;
  }

  /**
   * @param array $pickup_point
   * @return $this
   */
  public function set_pickup_point( $pickup_point ) {
    $this->pickup_point = $pickup_point;
    return $this;
  }

  /**
   * @return array
   */
  public function get_pickup_point() {
    return $this->pickup_point;
  }

  /**
   * @param string $product_id
   * @return $this
   */
  public function set_product_id( $product_id ) {
    $this->product_id = $product_id;
    return $this;
  }

  /**
   * @return string
   */
  public function get_product_id() {
    return $this->product_id;
  }

  /**
   * @param string $product_services
   * @return $this
   */
  public function set_product_services( $product_services ) {
    $this->product_services = $product_services;
    return $this;
  }

  /**
   * @return string
   */
  public function get_product_services() {
    return $this->product_services;
  }

  /**
   * @param string $customer_number
   * @return $this
   */
  public function set_customer_number( $customer_number ) {
    $this->customer_number = $customer_number;
    return $this;
  }

  /**
   * @return string
   */
  public function get_customer_number() {
    return $this->customer_number;
  }

  /**
   * @param array $packages
   * @return $this
   */
  public function set_packages( $packages ) {
    $this->packages = $packages;
    return $this;
  }

  /**
   * @return array
   */
  public function get_packages() {
    return $this->packages;
  }

  public function create_data() {
    return [
        'shippingDateTime' => $this->get_shipping_date_time(),
        'parties'          => [
            'sender'      => $this->get_sender_address(),
            'recipient'   => $this->get_recipient_address(),
            'pickupPoint' => $this->get_pickup_point()
        ],
        'product'          => [
            'id'                 => $this->get_product_id(),
            'customerNumber'     => $this->get_customer_number(),
            'services'           => $this->get_product_services(),
            'customsDeclaration' => null
        ],
        'purchaseOrder'    => null,
        'correlationId'    => '',
        'packages'         => $this->get_packages()
    ];
  }

}
