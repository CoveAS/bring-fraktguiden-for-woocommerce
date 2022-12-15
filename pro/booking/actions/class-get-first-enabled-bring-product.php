<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Actions;

use Bring_Fraktguiden\Common\Fraktguiden_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Customer class
 */
class Get_First_Enabled_Bring_Product {
	public function __invoke(): string {
		$services = Fraktguiden_Service::all( null, true );
		if ( empty( $services ) ) {
			return '5800';
		}
		$service = array_shift( $services );

		return $service->bring_product;
	}
}
