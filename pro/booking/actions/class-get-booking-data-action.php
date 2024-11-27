<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Actions;

use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguidenPro\Booking\Consignment_Request\Bring_Booking_Consignment_Request;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Customer class
 */
class Get_Booking_Data_Action {
	public function __invoke( Bring_WC_Order_Adapter $adapter ): array {
		$packages = [];
		foreach ( $adapter->get_fraktguiden_shipping_items() as $shipping_item ) {
			// 1. Create Booking Consignment
			$consignment = Bring_Booking_Consignment_Request::create( $shipping_item );

			// 2. Get packages from that consignment
			foreach ( $consignment->create_packages( true ) as $package ) {
				$key        = $package['shipping_item_info']['shipping_method']['service'];
				$packages[] = [
					'id'          => $package['shipping_item_info']['item_id'],
					'key'         => $key,
					'pickupPoint' => $package['shipping_item_info']['shipping_method']['pickup_point_id'],
					'dimensions'  => $package['dimensions'],
					'weightInKg'  => $package['weightInKg'],
				];
			}
		}

		return [
			'orderId'             => $adapter->order->get_id(),
			'orderItemIds'        => array_keys( $adapter->get_fraktguiden_shipping_items() ),
			'services'            => Fraktguiden_Service::all(),
			'bag_on_door_consent' => $adapter->order->get_meta( '_bag_on_door_consent' ),
			'packages'            => $packages,
			'i18n'                => [
				'tip'                                 => __( 'Shipping item id', 'bring-fraktguiden-for-woocommerce' ),
				'orderID'                             => __( 'Order ID', 'bring-fraktguiden-for-woocommerce' ),
				'product'                             => __( 'Product', 'bring-fraktguiden-for-woocommerce' ),
				'width'                               => __( 'Width', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
				'height'                              => __( 'Height', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
				'length'                              => __( 'Length', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
				'weight'                              => __( 'Weight', 'bring-fraktguiden-for-woocommerce' ) . '(kg)',
				'pickupPoint'                         => __( 'Pickup point', 'bring-fraktguiden-for-woocommerce' ),
				'delete'                              => __( 'Delete', 'bring-fraktguiden-for-woocommerce' ),
				'add'                                 => __( 'Add', 'bring-fraktguiden-for-woocommerce' ),
				'signature_required'                  => esc_html__( 'Signature required' ),
				'signature_required_description'      => esc_html__( 'prevents customers from selecting bag on door delivery' ),
				'bag_on_door'                         => esc_html__( 'Bag on door (mailbox)' ),
				'bag_on_door_description'             => esc_html__( 'Mailbox Parcel (Pakke i postkassen) is a parcel that will be delivered in the recipient’s mailbox. If the parcel for various reasons does not fit in the mailbox, the sender may, against a surcharge, choose to leave the parcel on the door handle (in a special bag) to avoid it being sent to the pickup point. It’s recommended that this delivery option is actively confirmed by the receiver upon booking in the sender’s webshop. When the parcel is delivered as a bag on the door, the bar code is scanned and the recipient will receive an SMS/email. Note that if the parcel is delivered in the mailbox the additional fee will not occur.' ),
				'id_verification'                     => esc_html__( 'ID verification' ),
				'id_verification_description'         => esc_html__( 'ID is checked upon delivery. Any person (other than the recipient) can receive the shipment, but must legitimize before receiving it.' ),
				'electronic_notification'             => esc_html__( 'Electronic notification' ),
				'electronic_notification_description' => esc_html__( 'Digital notification by SMS and/or e-mail.' ),
				'individual_verification'             => esc_html__( 'Individual verification' ),
				'individual_verification_description' => esc_html__( 'Only the specified recipient can receive the shipment by showing identification. Use of authorization is not possible.' ),
			]
		];
	}
}
