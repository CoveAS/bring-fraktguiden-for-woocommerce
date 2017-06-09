<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Class Bring_WC_Order_Adapter
 *
 * Wraps an WC_Order and adds Bring related methods
 */
class Bring_WC_Order_Adapter {

  /** @var WC_Order */
  public $order = null;

  public function __construct( $order ) {
    $this->order = $order;
  }

  /**
   * Returns true if the order is booked.
   *
   * @return bool
   */
  public function is_booked() {
    return $this->has_booking_consignments();
  }

  /**
   * Saves the booking response to the order.
   *
   * @param WP_Bring_Response $response
   */
  public function update_booking_response( $response ) {
    // Create an array of the response for post meta.
    $response_as_array = $response->to_array();
    update_post_meta( $this->order->get_id(), '_bring_booking_response', $response_as_array );
  }

  /**
   * Returns the saved booking response array.
   *
   * @return array
   */
  public function get_booking_response() {
    return get_post_meta( $this->order->get_id(), '_bring_booking_response', true );
  }

  /**
   * Returns the consignments json decoded from the stored MyBring response.
   * If the saved response has errors, return empty array.
   *
   * @return array
   */
  public function get_booking_consignments() {
    $response = $this->get_booking_response();
    if ( ! $response || $this->has_booking_errors() ) {
      return [ ];
    }
    $body = json_decode( $response['body'] );
    return $body->consignments;
  }

  /**
   * Creates an array of all errors from a response.
   *
   * @return array
   */
  public function get_booking_errors() {
    $result = [ ];

    $response = $this->get_booking_response();

    // Add bring specific errors
    $body = json_decode( $response['body'] );
    if ( $body && property_exists( $body, 'consignments' ) ) {
      foreach ( $body->consignments as $consignment ) {
        if ( property_exists( $consignment, 'errors' ) ) {
          foreach ( $consignment->errors as $error ) {
            $code = $error->code;
            foreach ( $error->messages as $message ) {
              $result[] = $code . ': ' . $message->message;
            }
          }
        }
      }
    }

    // Add errors from the response errors array.
    foreach ( $response['errors'] as $error ) {
      $result[] = $error;
    }

    return $result;
  }

  /**
   * Returns true if the order has booking consignments.
   *
   * @return bool
   */
  public function has_booking_consignments() {
    return count( $this->get_booking_consignments() ) > 0;
  }

  /**
   * Returns true if there are any errors in the booking response.
   *
   * @return bool
   */
  public function has_booking_errors() {
    $response = $this->get_booking_response();
    if ( ! $response ) {
      return false;
    }
    if ( $response['status_code'] != 200 ) {
      return true;
    }

    $body = json_decode( $response['body'] );
    if ( property_exists( $body, 'consignments' ) ) {
      foreach ( $body->consignments as $consignment ) {
        if ( count( $consignment->errors ) > 0 ) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Returns true if the order has Bring shipping methods.
   * @return bool
   */
  public function has_bring_shipping_methods() {
    return count( $this->get_fraktguiden_shipping_items() ) > 0;
  }

  /**
   * Save pickup point information on checkout.
   *
   * @param int $pickup_point_id
   * @param int $post_code
   * @param string $cached Pickup point information (name, address etc)
   * @param string $packages JSON string representation of packages.
   */
  public function checkout_update_pickup_point_data( $pickup_point_id, $post_code, $cached ) {
    $shipping_methods = $this->order->get_shipping_methods();
    foreach ( $shipping_methods as $item_id => $method ) {
      $method_id = wc_get_order_item_meta( $item_id, 'method_id', true );
      if ( $method_id == Fraktguiden_Helper::ID . ':servicepakke' ) {
        // Right now the checkout only supports 1 shipping method per order.
        if ( ! ( empty( $pickup_point_id ) ) ) {
          wc_update_order_item_meta( $item_id, '_fraktguiden_pickup_point_id', $pickup_point_id );
          wc_update_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode', $post_code );
          wc_update_order_item_meta( $item_id, '_fraktguiden_pickup_point_info_cached', $cached );
        }
      }
    }
  }

  public function checkout_update_packages( $packages ) {
    if ( ! $packages ) {
      return;
    }
    $shipping_methods = $this->order->get_shipping_methods();
    foreach ( $shipping_methods as $item_id => $method ) {
      wc_update_order_item_meta( $item_id, '_fraktguiden_packages', json_decode( stripslashes( $packages ), true ) );
    }
  }

//  /**
//   * @todo: refactor
//   */
//  public function checkout_add_pickup_point() {
//    $shipping_methods = $this->order->get_shipping_methods();
//    foreach ( $shipping_methods as $item_id => $method ) {
//      $method_id = wc_get_order_item_meta( $item_id, 'method_id', true );
//
//      if ( $method_id == Fraktguiden_Helper::ID . ':servicepakke' ) {
//        // Right now the checkout only supports 1 shipping method per order.
//        if ( ! ( empty( $_POST['_fraktguiden_pickup_point_id'] ) ) ) {
//          wc_add_order_item_meta( $item_id, '_fraktguiden_pickup_point_id', $_POST['_fraktguiden_pickup_point_id'] );
//          wc_add_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode', $_POST['_fraktguiden_pickup_point_postcode'] );
//          wc_add_order_item_meta( $item_id, '_fraktguiden_pickup_point_info_cached', $_POST['_fraktguiden_pickup_point_info_cached'] );
//          wc_add_order_item_meta( $item_id, '_fraktguiden_packages', json_decode( stripslashes( $_POST['_fraktguiden_packages'] ), true ) );
//        }
//      }
//    }
//  }

  /**
   * @param array $shipping_items Order items to save
   */
  public function admin_update_pickup_point( $shipping_items ) {
    $shipping_methods = $shipping_items['shipping_method'];
    if ( $shipping_methods ) {
      foreach ( $shipping_methods as $item_id => $shipping_method ) {
        if ( strpos( $shipping_method, Fraktguiden_Helper::ID ) !== false ) {
          $pickup_point_id = $shipping_items['_fraktguiden_pickup_point_id'][$item_id];

          $packages = $shipping_items['_fraktguiden_packages'][$item_id];
          if ( $packages ) {
            wc_update_order_item_meta( $item_id, '_fraktguiden_packages', json_decode( stripslashes( $packages ), true ) );
          }

          if ( ! empty( $pickup_point_id ) ) {
            $pickup_point_postcode = $shipping_items['_fraktguiden_pickup_point_postcode'][$item_id];
            $pickup_point_info     = $shipping_items['_fraktguiden_pickup_point_info_cached'][$item_id];

            wc_update_order_item_meta( $item_id, '_fraktguiden_pickup_point_id', $pickup_point_id );
            wc_update_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode', $pickup_point_postcode );
            wc_update_order_item_meta( $item_id, '_fraktguiden_pickup_point_info_cached', $pickup_point_info );
          }
          else {
            wc_delete_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode' );
            wc_delete_order_item_meta( $item_id, '_fraktguiden_pickup_point_id' );
            wc_delete_order_item_meta( $item_id, '_fraktguiden_pickup_point_info_cached' );
          }
        }
        else {
          wc_delete_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode' );
          wc_delete_order_item_meta( $item_id, '_fraktguiden_pickup_point_id' );
          wc_delete_order_item_meta( $item_id, '_fraktguiden_pickup_point_info_cached' );
        }
      }
    }
  }

  public function get_shipping_data() {
    $data = [ ];

    foreach ( $this->get_fraktguiden_shipping_items() as $item_id => $method ) {
      $pickup_point_id       = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_id', true );
      $pickup_point          = null;
      $pickup_point_cached   = null;
      $pickup_point_postcode = null;
      if ( $pickup_point_id ) {
        $shipping_address = $this->order->get_address( 'shipping' );

        $request  = new WP_Bring_Request();
        $response = $request->get( 'https://api.bring.com/pickuppoint/api/pickuppoint/' . $shipping_address['country'] . '/id/' . $pickup_point_id . '.json' );

        $pickup_point          = $response->has_errors() ? null : json_decode( $response->get_body() )->pickupPoint[0];
        $pickup_point_cached   = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_cached', true );
        $pickup_point_postcode = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode', true );

      }
      $data[] = [
          'item_id'                  => $item_id,
          'pickup_point'             => $pickup_point,
          'pickup_point_info_cached' => $pickup_point_cached,
          'postcode'                 => $pickup_point_postcode,
          'packages'                 => json_encode( $this->get_packages_for_order_item( $item_id ) )
      ];

    }
    return $data;
  }


  /**
   * Returns pickup point for given shipping item id.
   * If not found an empty array is found.
   *
   * @param $item_id_to_find
   * @return array
   */
  public function get_pickup_point_for_shipping_item_formatted( $item_id_to_find ) {
    $result = [ ];

    $country_code = $this->order->get_shipping_country();

    foreach ( $this->get_fraktguiden_shipping_items() as $item_id => $shipping_item ) {
      $pickup_point_id = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_id', true );
      if ( $pickup_point_id && $item_id_to_find == $item_id ) {
        $result = [
            'id'          => $pickup_point_id,
            'countryCode' => $country_code,
        ];
        break;
      }
    }
    return $result;
  }

  /**
   * Returns pickup point information for shipping item.
   *
   * @param int $item_id
   * @return array
   */
  public function get_pickup_point_for_shipping_item( $item_id ) {
    $result          = [ ];
    $pickup_point_id = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_id', true );
    if ( $pickup_point_id ) {
      $result['id']        = $pickup_point_id;
      $result['post_code'] = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_postcode', true );
      $result['cached']    = wc_get_order_item_meta( $item_id, '_fraktguiden_pickup_point_info_cached', true );
    }
    return $result;
  }

  /**
   * Returns Fraktguiden shipping method items.
   *
   * @return array
   */
  public function get_fraktguiden_shipping_items() {
    $result = [ ];

    $shipping_methods = $this->order->get_shipping_methods();
    foreach ( $shipping_methods as $item_id => $shipping_method ) {
      $method_id = wc_get_order_item_meta( $item_id, 'method_id', true );
      if ( strpos( $method_id, Fraktguiden_Helper::ID ) !== false ) {
        $result[$item_id] = $shipping_method;
      }
    }
    return $result;
  }

  /**
   * @param $item_id
   * @return array
   */
  public function get_packages_for_order_item( $item_id ) {
    return wc_get_order_item_meta( $item_id, '_fraktguiden_packages', true );
  }

  /**
   * Returns all packages for the order.
   * An order can in theory have multiple shipping items.
   * A shipping item can have multiple packages.
   *
   * @param int|boolean $item_id_to_find
   * @return array
   */
  public function get_packages( $item_id_to_find = false ) {
    $result = [ ];
    foreach ( $this->get_fraktguiden_shipping_items() as $item_id => $shipping_method ) {
      $packages_array = $this->get_packages_for_order_item( $item_id );
      if ( $item_id_to_find && $item_id_to_find == $item_id ) {
        if ( $packages_array ) {
          $result[$item_id] = $packages_array;
        }
        return $result;
      }
      else {
        if ( $packages_array ) {
          $result[$item_id] = $packages_array;
        }
      }

    }
    return $result;
  }

  public function order_update_packages() {
    $order    = $this;
    $wc_order = $this->order;
    $cart = [];
    //build a cart like array
    foreach ( $wc_order->get_items() as $item_id => $item ) {
      if ( ! isset( $item['product_id'] ) ) {
        continue;
      }
      $cart[] = [
        'data' => wc_get_product( $item['product_id'] ),
        'quantity' => $item['qty'],
      ];
    }
    // var_dump( get_class_methods( $wc_order ) );
    $shipping_method = new WC_Shipping_Method_Bring;
    $packages = $shipping_method->pack_order( $cart );
    $order->checkout_update_packages( json_encode( $packages ) );
  }

  /**
   * Returns all packages for the order 'Bring booking formatted'.
   *
   * @param int|boolean $item_id_to_find
   * @param boolean $include_info
   *
   * @return array
   */
  public function get_packages_formatted( $item_id_to_find = false, $include_info = false ) {
    $result = [ ];

    $order_items_packages = $this->get_packages( $item_id_to_find );
    if ( ! $order_items_packages ) {
      $this->order_update_packages();
      $order_items_packages = $this->get_packages( $item_id_to_find );
    }
    if ( ! $order_items_packages ) {
      return [ ];
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
          $shipping_method  = '';
          $shipping_methods = $this->get_fraktguiden_shipping_items();
          foreach ( $shipping_methods as $id => $shipping_method ) {
            if ( $id == $item_id ) {
              $shipping_method = Fraktguiden_Helper::parse_shipping_method_id( $shipping_method['method_id'] );
              break;
            }
          }

          $data['shipping_item_info'] = [
              'item_id'         => $item_id,
              'shipping_method' => $shipping_method
          ];
        }

        $result[] = $data;

      }
    }
    return $result;
  }

  /**
   * Returns the recipient (order/shipping address)
   *
   * @return array
   */
  public function get_recipient_address_formatted() {
    $order     = $this->order;
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
        ]
    ];

  }

}
