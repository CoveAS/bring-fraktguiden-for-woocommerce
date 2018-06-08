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
    $consignments = [];
    $response = $this->get_booking_response();
    return Bring_Consignment::create_from_response( $response, $this->order->get_id() );
  }

  /**
   * Returns the consignments json decoded from the stored MyBring response.
   * If the saved response has errors, return empty array.
   *
   * @return array
   */
  public function get_mailbox_consignments() {
    $response = $this->get_booking_response();
    if ( ! $response || $this->has_booking_errors() ) {
      return [ ];
    }
    $body = json_decode( $response['body'] );

    return $body->data;
  }

  /**
   * Returns the consignments json decoded from the stored MyBring response.
   * If the saved response has errors, return empty array.
   *
   * @return array
   */
  public function get_consignment_type() {
    $response = $this->get_booking_response();
    if ( ! $response || $this->has_booking_errors() ) {
      return '';
    }
    $body = json_decode( $response['body'] );

    return property_exists( $body, 'data' ) ? 'mailbox' : 'booking';
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
    // Add any non-ok body to the error array because it contains the explanation
    // eg. status_code = 400 has [ 'body' => string 'Authentication failed...' ]
    if ( 200 != $response['status_code'] ) {
      $result[] = $response['body'];
    }

    return $result;
  }

  /**
   * Returns true if the order has booking consignments.
   *
   * @return bool
   */
  public function has_booking_consignments() {
    if ( 'mailbox' == $this->get_consignment_type() ) {
      return count( $this->get_mailbox_consignments() ) > 0;
    } else {
      return count( $this->get_booking_consignments() ) > 0;
    }
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
    if ( ! in_array( $response['status_code'],  [200, 201, 202, 203, 204] ) ) {
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

  static function check_meta_key( $array, $key ) {
    if ( ! isset( $array[$key] ) ) {
      return false;
    }
    if ( empty( $array[$key] ) ) {
      return false;
    }
    if ( ! $array[$key][0] ) {
      return false;
    }
    return true;
  }

  /**
   * @param array $shipping_items Order items to save
   */
  public function admin_update_pickup_point( $shipping_items ) {
    $shipping_methods = $shipping_items['shipping_method'];
    if ( $shipping_methods ) {
      foreach ( $shipping_methods as $item_id => $shipping_method ) {
        if ( strpos( $shipping_method, Fraktguiden_Helper::ID ) !== false ) {
          $pickup_point_id = [];
          if ( isset( $shipping_items['_fraktguiden_pickup_point_id'] ) ) {
            $pickup_point_id = $shipping_items['_fraktguiden_pickup_point_id'][$item_id];
          }

          $packages = false;
          if ( isset( $shipping_items['_fraktguiden_packages'] ) ) {
            $shipping_items['_fraktguiden_packages'][$item_id];
          }
          if ( $packages ) {
            // wc_update_order_item_meta( $item_id, '_fraktguiden_packages', json_decode( stripslashes( $packages ), true ) );
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
      $pickup_point_id       = $method->get_meta( 'pickup_point_id' );
      $pickup_point          = null;
      $pickup_point_cached   = null;
      $pickup_point_postcode = null;
      if ( $pickup_point_id ) {
        $shipping_address = $this->order->get_address( 'shipping' );

        $request  = new WP_Bring_Request();
        $response = $request->get( 'https://api.bring.com/pickuppoint/api/pickuppoint/' . $shipping_address['country'] . '/id/' . $pickup_point_id . '.json' );

        $pickup_point          = $response->has_errors() ? null : json_decode( $response->get_body() )->pickupPoint[0];
        // $pickup_point_cached   = $method->get_meta( '_fraktguiden_pickup_point_cached' );
        // $pickup_point_postcode = $method->get_meta( '_fraktguiden_pickup_point_postcode' );

      }
      $data[] = [
          'item_id'                  => $item_id,
          'pickup_point'             => $pickup_point,
          'packages'                 => json_encode( $method->get_meta( '_fraktguiden_packages' ) )
      ];

    }
    return $data;
  }


  /**
   * Returns pickup point information for shipping item.
   *
   * @param int $item_id
   * @return array
   */
  public function get_pickup_point_for_shipping_item( $item_id ) {
    $result          = [ ];
    $pickup_point_id = wc_get_order_item_meta( $item_id, 'pickup_point_id', true );
    if ( $pickup_point_id ) {
      $result['id'] = $pickup_point_id;
    }
    var_dump( $result );
    return $result;
  }

  /**
   * Returns Fraktguiden shipping method items.
   *
   * Same as wc_order->get_shipping_methods() except that non-bring methods are filtered away.
   *
   * @return array
   */
  public function get_fraktguiden_shipping_items() {
    $result = [ ];

    $shipping_methods = $this->order->get_shipping_methods();
    foreach ( $shipping_methods as $item_id => $shipping_method ) {
      $method_id = wc_get_order_item_meta( $item_id, 'method_id', true );
      if ( strpos( $method_id, Fraktguiden_Helper::ID ) !== false ) {
        $shipping_method['method_id'] = $method_id;
        $result[$item_id] = $shipping_method;
      }
    }
    return $result;
  }

}
