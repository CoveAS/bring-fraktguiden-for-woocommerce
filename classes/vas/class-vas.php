<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Vas;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Exception;

/**
 * Fraktguiden_Service class
 */
class VAS {

	/**
	 * Key
	 *
	 * @var string
	 */
	public $enabled = false;

	/**
	 * Code
	 *
	 * @var string
	 */
	public $code;

	/**
	 * Name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Default
	 *
	 * @var boolean
	 */
	public $value = false;

	/**
	 * Vue Component
	 *
	 * @var string
	 */
	public $vue_component = 'checkbox';

	/**
	 * Array of bring product ids
	 *
	 * @var array
	 */
	public $bring_products = [];

	/**
	 * Construct
	 *
	 * @param string $vas_data VAS data
	 */
	public function __construct( $vas_data, $value = null ) {
		$this->enabled        = $vas_data['enabled'];
		$this->code           = $vas_data['code'];
		$this->value          = $value === null ? $vas_data['default'] : $value;
		$this->name           = $vas_data['name'];
		$this->bring_products = $vas_data['bring_products'];
	}

	/**
	 * Create collection
	 *
	 * @throws Exception
	 */
	public static function create_collection( $bring_product, $service_option ): array {
		$collection = [];
		$all_vas_data = Fraktguiden_Helper::get_vas_data();
		$found = false;

		foreach ( $all_vas_data as $vas_data ) {
			if ( ! in_array( $bring_product, $vas_data['bring_products'], false ) ) {
				continue;
			}
			$found = true;
			if ( empty( $vas_data['enabled'] ) ) {
				continue;
			}
			if ( empty( $vas_data['class'] ) ) {
				throw new Exception( "VAS item, {$vas_data['code']}, does not have a class" );
			}
			$vas_class    = $vas_data['class'];
			$value        =  ! empty( $service_option["vas_{$vas_data['code']}"] );
			$collection[] = new $vas_class( $vas_data, $value );
		}
		return $collection;
	}
}
