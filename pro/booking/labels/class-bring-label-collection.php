<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Labels;

/**
 * Bring_Label_Collection class
 */
abstract class Bring_Label_Collection {

	/**
	 * Files
	 *
	 * @var array
	 */
	public $files;

	/**
	 * Merge
	 */
	abstract public function merge();

	/**
	 * Check if there are no files
	 */
	public function is_empty() {
		return empty( $this->files );
	}

	/**
	 * Add
	 *
	 * @param int    $order_id Order ID.
	 * @param string $file     File.
	 */
	public function add( $order_id, $file ) {
		$this->files[] = [
			'order_id' => $order_id,
			'file'     => $file,
		];
	}

	/**
	 * Get order IDs
	 *
	 * @return array
	 */
	public function get_order_ids() {
		$order_ids = [];

		foreach ( $this->files as $file ) {
			$order_ids[] = $file['order_id'];
		}

		return array_unique( $order_ids );
	}
}
