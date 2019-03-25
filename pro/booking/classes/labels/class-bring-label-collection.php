<?php

abstract class Bring_Label_Collection {

	public $files;

	abstract public function merge();

	public function is_empty() {
		return empty( $this->files );
	}

	public function add( $order_id, $file ) {
		$this->files[] = [
			'order_id' => $order_id,
			'file'     => $file,
		];
	}

	public function get_order_ids() {
		$order_ids = [];
		foreach ( $this->files as $file ) {
			$order_ids[] = $file['order_id'];
		}
		return array_unique( $order_ids );
	}
}
