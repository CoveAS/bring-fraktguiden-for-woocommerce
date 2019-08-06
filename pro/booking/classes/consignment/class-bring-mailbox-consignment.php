<?php
/**
 * This file contains Bring_Booking_Customer class
 *
 * @package Bring_Fraktguiden\Bring_Booking_Customer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Mailbox_Consignment class
 */
class Bring_Mailbox_Consignment extends Bring_Consignment {

	/**
	 * Item
	 *
	 * @var array
	 */
	protected $item;

	/**
	 * Data
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Type
	 *
	 * @var string
	 */
	public $type = 'mailbox';

	/**
	 * Construct
	 *
	 * @param int   $order_id Order ID.
	 * @param array $item     Item.
	 * @param array $data     Data.
	 *
	 * @return void
	 */
	public function __construct( $order_id, $item, $data ) {
		$this->order_id = $order_id;
		$this->item     = $item;
		$this->data     = $data;
	}

	/**
	 * Get tracking code
	 *
	 * @return string
	 */
	public function get_tracking_code() {
		return $this->item['shipmentNumber'];
	}

	/**
	 * Get consignment number
	 *
	 * @return string
	 */
	public function get_consignment_number() {
		return $this->item['packageNumber'];
	}

	/**
	 * Get consignment number
	 *
	 * @return string
	 */
	public function get_customer_number() {
		return $this->data['attributes']['customerNumber'];
	}

	/**
	 * Get test indicator
	 *
	 * @return boolean
	 */
	public function get_test_indicator() {
		return $this->data['attributes']['testIndicator'];
	}

	/**
	 * Get label URL
	 *
	 * @return string
	 */
	public function get_label_url() {
		if ( isset( $this->data['attributes']['rfidLabelUri'] ) ) {
			return $this->data['attributes']['rfidLabelUri'];
		}
		return $this->data['attributes']['labelUri'];
	}

	/**
	 * Get tracking link
	 *
	 * @return string
	 */
	public function get_tracking_link() {
		if ( ! $this->item['rfid'] ) {
			return false;
		}

		return 'https://tracking.bring.com/tracking.html?q=' . $this->get_tracking_code();
	}

	/**
	 * Get date time
	 *
	 * @return string
	 */
	public function get_date_time() {
		$time = strtotime( $this->data['attributes']['orderTime'] );

		return date( 'Y-m-d H:i:s', $time );
	}
}
