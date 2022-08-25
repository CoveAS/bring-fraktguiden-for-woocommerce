<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden_Pro\Booking\Actions;

use Bring_Booking_Consignment_Request;
use Bring_Fraktguiden\VAS;
use Bring_WC_Order_Adapter;
use Exception;
use Fraktguiden_Helper;
use Fraktguiden_Service;

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
