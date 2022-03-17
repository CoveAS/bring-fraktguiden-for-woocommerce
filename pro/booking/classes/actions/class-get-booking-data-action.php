<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden_Pro\Booking\Actions;

use Bring_Booking_Consignment_Request;
use Fraktguiden_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Customer class
 */
class Get_Booking_Data_Action {
	public function __invoke( \Bring_WC_Order_Adapter $order ) {
		$packages = [];
		foreach ( $order->get_fraktguiden_shipping_items() as $shipping_method ) {
			// 1. Create Booking Consignment
			$consignment = new Bring_Booking_Consignment_Request( $shipping_method );

			// 2. Get packages from that consignment
			foreach ( $consignment->create_packages( true ) as $package ) {
				$key             = $package['shipping_item_info']['shipping_method']['service'];
				$packages[] = [
					'id'          => $package['shipping_item_info']['item_id'],
					'key'         => $key,
					'serviceData' => Fraktguiden_Helper::get_service_data_for_key( $key ),
					'pickupPoint' => $package['shipping_item_info']['shipping_method']['pickup_point_id'],
					'dimensions'  => $package['dimensions'],
					'weightInKg'  => $package['weightInKg'],
				];
			}
		}

		return json_encode(
			[
				'orderId'       => $order->order->get_id(),
				'orderItemIds'  => array_keys( $order->get_fraktguiden_shipping_items() ),
				'services'      => array_values(Fraktguiden_Helper::get_all_services()),
				'packages' => $packages,
				'i18n'          => [
					'tip'         => __( 'Shipping item id', 'bring-fraktguiden-for-woocommerce' ),
					'orderID'     => __( 'Order ID', 'bring-fraktguiden-for-woocommerce' ),
					'product'     => __( 'Product', 'bring-fraktguiden-for-woocommerce' ),
					'width'       => __( 'Width', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
					'height'      => __( 'Height', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
					'length'      => __( 'Length', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
					'weight'      => __( 'Weight', 'bring-fraktguiden-for-woocommerce' ) . '(kg)',
					'pickupPoint' => __( 'Pickup point', 'bring-fraktguiden-for-woocommerce' ),
					'delete'      => __( 'Delete', 'bring-fraktguiden-for-woocommerce' ),
					'add'         => __( 'Add', 'bring-fraktguiden-for-woocommerce' ),
				]
			]
		);
	}
}
