<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Consignment;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Consignment class
 */
class Bring_Booking_Consignment extends Bring_Consignment {

	/**
	 * Item
	 *
	 * @var array
	 */
	protected $item;

	/**
	 * Order ID
	 *
	 * @var int
	 */
	protected $order_id;

	/**
	 * Type
	 *
	 * @var string
	 */
	public $type = 'booking';

	/**
	 * Construct
	 *
	 * @param int   $order_id Order ID.
	 * @param array $item     Item.
	 */
	public function __construct( $order_id, $item ) {
		$this->order_id = $order_id;
		$this->item     = $item;
	}

	/**
	 * Get consignment number
	 *
	 * @return string
	 */
	public function get_consignment_number() {
		return $this->item['confirmation']['consignmentNumber'];
	}

	/**
	 * Get label URL
	 *
	 * @return string
	 */
	public function get_label_url() {
		return $this->item['confirmation']['links']['labels'];
	}

	/**
	 * Get tracking link
	 *
	 * @return string
	 */
	public function get_tracking_link() {
		return $this->item['confirmation']['links']['tracking'];
	}

	/**
	 * Get dates
	 *
	 * @return array
	 */
	public function get_dates() {
		return $this->item['confirmation']['dateAndTimes'];
	}

	/**
	 * Get packages
	 *
	 * @return array
	 */
	public function get_packages() {
		return $this->item['confirmation']['packages'];
	}
}
