<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

abstract class Bring_Consignment {

  /**
   * Create from response
   * @return array
   */
  static function create_from_response( $response, $order_id ) {
    if ( ! $response ) {
      return [ ];
    }
    $body = is_object( $response ) ? $response->body : $response['body'];
    $body = json_decode( $body, 1 );
    $consignments = [];

    if ( isset( $body['consignments'] ) ) {
      // Build the booking consignments
      foreach ( $body['consignments'] as $item ) {
        // Check for errors
        if ( count( $item['errors'] ) > 0 ) {
          // Return empty if any errors are found
          return [];
        }
        $consignments[] = new Bring_Booking_Consignment( $order_id, $item );
      }
    } else if ( isset( $body['data'] ) ) {
      foreach ( $body['data']['attributes']['packages'] as $item ) {
      // Build the mailbox consignments
        $consignments[] = new Bring_Mailbox_Consignment( $order_id, $item, $body['data'] );
      }
    }
    return $consignments;
  }

  public function get_label_file() {
    return new Bring_Booking_File( 'label', $this->get_consignment_number(), $this->get_label_url(), $this->order_id );
  }

  public function download_label() {
    $url = $this->get_label_url();
  }
}