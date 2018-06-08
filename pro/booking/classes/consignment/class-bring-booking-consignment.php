<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Consignment extends Bring_Consignment {

  protected $item;
  protected $order_id;
  public $type = 'booking';

  function __construct( $order_id, $item ) {
    $this->order_id = $order_id;
    $this->item = $item;
  }

  /**
   * Get consignment number
   * @return string
   */
  public function get_consignment_number() {
    return $this->item['confirmation']['consignmentNumber'];
  }

  /**
   * Get label URL
   * @return string
   */
  public function get_label_url() {
    return $this->item['confirmation']['links']['labels'];
  }

  public function get_links() {
    return $this->item['confirmation']['links'];
  }
  public function get_dates() {
    return $this->item['confirmation']['dateAndTimes'];
  }
  public function get_packages() {
    return $this->item['confirmation']['packages'];
  }
}