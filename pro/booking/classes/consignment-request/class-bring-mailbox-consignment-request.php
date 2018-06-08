<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Mailbox_Consignment_Request extends Bring_Consignment_Request {

  /**
   * Get Endpoint URL
   * @return string
   */
  public function get_endpoint_url() {
    return 'https://api.bring.com/order/to-mailbox/labels';
  }

  /**
   * Create package
   * @param  array $package
   * @return array
   */
  public function create_package( $package ) {
    $order     = $this->shipping_item->get_order();
    $full_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
    $name      = $order->get_shipping_company() ? $order->get_shipping_company() : $full_name;

    $tracking = preg_match( '/SPORBAR$/', $this->service_id );

    $phone_number = $order->get_billing_phone();
    $phone_number = Fraktguiden_Helper::phone_i18n( $phone_number, $order->get_billing_country() );
    return [
      'rfid'          => $tracking,
      'weight'        => $package['weightInGrams0'],
      'recipientName' => $name,
      'postalCode'    => $order->get_shipping_postcode(),
      'streetAddress' => $order->get_shipping_address_1(),
      'phoneNumber'   => $phone_number,
      'email'         => $order->get_billing_email()
    ];
  }

  /**
   * Create packages
   * @return array
   */
  public function create_packages() {
    $order_items_packages = wc_get_order_item_meta( $this->shipping_item->get_id(), '_fraktguiden_packages', false );
    if ( ! $order_items_packages ) {
      $this->order_update_packages();
      $order_items_packages = wc_get_order_item_meta( $this->shipping_item->get_id(), '_fraktguiden_packages', false );
    }
    $packages = [];
    foreach ( $order_items_packages as $package ) {
      $packages[] = $this->create_package( $package );
    }
    return $packages;
  }

  /**
   * Create data
   * @return array
   */
  public function create_data() {
    $sender = $this->get_sender();
    return [
      'data' => [
        'type' => 'labels',
        'attributes' => [
          'customerNumber' => $this->customer_number,
          'senderName'     => $sender['booking_address_store_name'],
          'postalCode'     => $sender['booking_address_postcode'],
          'streetAddress'  => $sender['booking_address_street1'],
          'senderEmail'    => $sender['booking_address_email'],
          'reference'      => $this->get_reference(),
          'testIndicator'  => false,
          'packages'       => $this->create_packages(),
        ]
      ]
    ];
  }


}
