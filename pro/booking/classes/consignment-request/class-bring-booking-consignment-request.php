<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Consignment_Request extends Bring_Consignment_Request {

  /**
   * Get Endpoint URL
   * @return string
   */
  public function get_endpoint_url() {
    return 'https://api.bring.com/booking/api/booking';
  }

  /**
   * Create packages
   * @param  boolean $include_info
   * @return array
   */
  public function create_packages( $include_info = false) {
    $order_items_packages = $this->shipping_item->get_meta( '_fraktguiden_packages' );
    if ( ! $order_items_packages ) {
      $order_items_packages = $this->order_update_packages();
    }
    // Make sure packages is an array of arrays
    if ( isset( $order_items_packages['length0'] ) ) {
      $order_items_packages = [ $this->shipping_item->get_id() => $order_items_packages ];
    }
    if ( ! $order_items_packages ) {
      return [];
    }
    $elements = [ 'width', 'height', 'length', 'weightInGrams' ];
    $elements_count = count( $elements );
    foreach ( $order_items_packages as $item_id => $package ) {
      $package_count = count( $package ) / $elements_count;
      for ( $i = 0; $i < $package_count; $i++ ) {
        $weight_in_kg = (int)$package['weightInGrams' . $i] / 1000;
        $data = [
          'weightInKg'       => $weight_in_kg,
          'goodsDescription' => null,
          'dimensions'       => [
            'widthInCm'  => $package['width' . $i],
            'heightInCm' => $package['height' . $i],
            'lengthInCm' => $package['length' . $i],
          ],
          'containerId'      => null,
          'packageType'      => null,
          'numberOfItems'    => null,
          'correlationId'    => null,
        ];
        if ( $include_info ) {
          $info = [];
          $data['shipping_item_info'] = [
            'item_id'         => $item_id,
            'shipping_method' => [
              'name'            => $this->shipping_item['method_id'],
              'service'         => $this->service_id,
              'pickup_point_id' => $this->shipping_item->get_meta( 'pickup_point_id' ),
            ],
          ];
        }
        $result[] = $data;
      }
    }
    return $result;
  }

  /**
   * Return the sender's address formatted for Bring consignment
   *
   * @param WC_Order $wc_order
   * @param string $additional_info
   * @return array
   */
  public function get_sender_address() {
    $wc_order = $this->shipping_item->get_order();
    $additional_info = '';
    if ( isset( $_REQUEST['_bring_additional_info'] ) ) {
      $additional_info = filter_var( $_REQUEST['_bring_additional_info'], FILTER_SANITIZE_STRING );
    }
    $sender = $this->get_sender();

    return [
      "name"                  => $sender['booking_address_store_name'],
      "addressLine"           => $sender['booking_address_street1'],
      "addressLine2"          => $sender['booking_address_street2'],
      "postalCode"            => $sender['booking_address_postcode'],
      "city"                  => $sender['booking_address_city'],
      "countryCode"           => $sender['booking_address_country'],
      "reference"             => $this->get_reference(),
      "additionalAddressInfo" => $additional_info,
      "contact"               => [
        "name"        => $sender['booking_address_contact_person'],
        "email"       => $sender['booking_address_email'],
        "phoneNumber" => $sender['booking_address_phone'],
      ],
    ];
  }

  /**
   * Returns the recipient (order/shipping address)
   *
   * @return array
   */
  public function get_recipient_address() {
    $order     = $this->shipping_item->get_order();
    $full_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
    $name      = $order->get_shipping_company() ? $order->get_shipping_company() : $full_name;
    return [
      "name"                  => $name,
      "addressLine"           => $order->get_shipping_address_1(),
      "addressLine2"          => $order->get_shipping_address_2(),
      "postalCode"            => $order->get_shipping_postcode(),
      "city"                  => $order->get_shipping_city(),
      "countryCode"           => $order->get_shipping_country(),
      "reference"             => null,
      "additionalAddressInfo" => $order->get_customer_note(),
      "contact"               => [
        "name"        => $full_name,
        "email"       => $order->get_billing_email(),
        "phoneNumber" => $order->get_billing_phone(),
      ],
    ];
  }

  /**
   * Create data
   * @return array
   */
  public function create_data() {
    $recipient_address = $this->get_recipient_address();

    $consignments = [
      'shippingDateTime' => $this->shipping_date_time,
      // Sender and recipient
      'parties' => [
        'sender'      => $this->get_sender_address(),
        'recipient'   => $recipient_address,
      ],
      // Product / Service
      'product' => [
        'id'                 => strtoupper($this->service_id),
        'customerNumber'     => $this->customer_number,
        'services'           => null,
        'customsDeclaration' => null
      ],
      'purchaseOrder' => null,
      'correlationId' => null,
      // Packages
      'packages' => $this->create_packages()
    ];
    if ( Fraktguiden_Helper::get_option( 'evarsling' ) == 'yes' ) {
      $consignments['product']['services'] = [
        'recipientNotification' => [
          'email'  => $recipient_address['contact']['email'],
          'mobile' => $recipient_address['contact']['phoneNumber'],
        ],
      ];
    }
    // Add pickup point
    $pickup_point_id = $this->shipping_item->get_meta( 'pickup_point_id' );
    if ( $pickup_point_id ) {
      $consignments['parties']['pickupPoint'] = [
        'id'          => $pickup_point_id,
        'countryCode' => $this->shipping_item->get_order()->get_shipping_country(),
      ];
    }
    if ( Fraktguiden_Helper::get_option( 'evarsling' ) == 'yes' ) {
      $consignments['product']['services'] = [
        'recipientNotification' => [
          'email'  => $recipient_address['contact']['email'],
          'mobile' => $recipient_address['contact']['phoneNumber'],
        ]
      ];
    }
    $data = [
      'testIndicator' => ( Fraktguiden_Helper::get_option( 'booking_test_mode_enabled' ) == 'yes' ),
      'schemaVersion' => 1,
      'consignments'  => [ $consignments ],
    ];
    return $data;
  }
}
