<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Mailbox_Consignment extends Bring_Consignment {

  protected $item;
  protected $data;
  public $type = 'mailbox';

  function __construct( $order_id, $item, $data ) {
    $this->order_id = $order_id;
    $this->item = $item;
    $this->data = $data;
  }

  /**
   * Get tracking code
   * @return string
   */
  public function get_tracking_code() {
    return $this->item['shipmentNumber'];
  }

  /**
   * Get consignment number
   * @return string
   */
  public function get_consignment_number() {
    return $this->item['packageNumber'];
  }

  /**
   * Get consignment number
   * @return string
   */
  public function get_customer_number() {
    return $this->data['attributes']['customerNumber'];
  }

  /**
   * Get test indicator
   * @return boolean
   */
  public function get_test_indicator() {
    return $this->data['attributes']['testIndicator'];
  }

  /**
   * Get label URL
   * @return string
   */
  public function get_label_url() {
    if ( isset( $this->data['attributes']['rfidLabelUri'] ) ) {
      return $this->data['attributes']['rfidLabelUri'];
    }
    return $this->data['attributes']['labelUri'];
  }

  /**
   * Get date time
   * @return string
   */
  public function get_date_time() {
    $time  = strtotime( $this->data['attributes']['orderTime'] );
    return date( 'Y-m-d H:i:s', $time );
  }
}