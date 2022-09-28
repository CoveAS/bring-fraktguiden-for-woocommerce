<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden_Pro\Booking\Actions\Get_First_Enabled_Bring_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Consignment_Request class
 */
class Bring_Booking_Consignment_Request extends Bring_Consignment_Request {

	/**
	 * Get Endpoint URL
	 *
	 * @return string
	 */
	public function get_endpoint_url() {
		return 'https://api.bring.com/booking/api/booking';
	}

	/**
	 * Create packages
	 *
	 * @param boolean $include_info Include info.
	 *
	 * @return array
	 */
	public function create_packages( $include_info = false ) {
		$order_items_packages = $this->shipping_item->get_meta( '_fraktguiden_packages' );

		if ( ! $order_items_packages ) {
			$order_items_packages = $this->order_update_packages();
		}

		// Make sure packages is an array of arrays.
		if ( isset( $order_items_packages['length0'] ) ) {
			$order_items_packages = [ $this->shipping_item->get_id() => $order_items_packages ];
		}

		if ( ! $order_items_packages ) {
			return [];
		}

		$elements       = [ 'width', 'height', 'length', 'weightInGrams' ];
		$elements_count = count( $elements );

		foreach ( $order_items_packages as $item_id => $package ) {
			if (! is_array($package)) {
				continue;
			}
			$package_count = count( $package ) / $elements_count;

			for ( $i = 0; $i < $package_count; $i ++ ) {
				$weight = 0;

				if ( isset( $package[ 'weight' . $i ] ) ) {
					$weight = $package[ 'weight' . $i ];
				}

				if ( isset( $package[ 'weightInGrams' . $i ] ) ) {
					$weight = $package[ 'weightInGrams' . $i ];
				}

				$package_type = null;
				if ( $this->service->home_delivery ) {
					$package_type = Fraktguiden_Helper::get_option( 'booking_home_delivery_package_type', 'hd_eur' );
				}

				$weight_in_kg = (int) $weight / 1000;
				$data         = [
					'weightInKg'       => $weight_in_kg,
					'goodsDescription' => null,
					'dimensions'       => [
						'widthInCm'  => $package[ 'width' . $i ],
						'heightInCm' => $package[ 'height' . $i ],
						'lengthInCm' => $package[ 'length' . $i ],
					],
					'containerId'      => null,
					'packageType'      => $package_type,
					'numberOfItems'    => null,
					'correlationId'    => null,
				];

				if ( $include_info ) {
					$data['shipping_item_info'] = [
						'item_id'         => $item_id,
						'shipping_method' => [
							'name'            => $this->shipping_item['method_id'],
							'service'         => $this->service_id,
							'pickup_point_id' => $this->shipping_item->get_meta( 'pickup_point_id' ),
						],
					];
				}

				$result[] = $data;
			}
		}

		return $result;
	}

	/**
	 * Return the sender's address formatted for Bring consignment
	 *
	 * @return array
	 */
	public function get_sender_address() {
		$wc_order        = $this->shipping_item->get_order();
		$additional_info = '';

		$bring_additional_info_sender = filter_input( INPUT_POST, '_bring_additional_info_sender', FILTER_SANITIZE_STRING );

		if ( ! is_null( $bring_additional_info_sender ) ) {
			$additional_info = $bring_additional_info_sender;
		}

		$sender = $this->get_sender();

		return apply_filters(
			'bring_fraktguiden_get_consignment_sender_address',
			[
				'name'                  => $sender['booking_address_store_name'],
				'addressLine'           => $sender['booking_address_street1'],
				'addressLine2'          => $sender['booking_address_street2'],
				'postalCode'            => $sender['booking_address_postcode'],
				'city'                  => $sender['booking_address_city'],
				'countryCode'           => $sender['booking_address_country'],
				'reference'             => $this->get_reference(),
				'additionalAddressInfo' => $additional_info,
				'contact'               => [
					'name'        => $sender['booking_address_contact_person'],
					'email'       => $sender['booking_address_email'],
					'phoneNumber' => $sender['booking_address_phone'],
				],
			],
			$wc_order,
			$this
		);
	}

	/**
	 * Returns the recipient (order/shipping address)
	 *
	 * @return array
	 */
	public function get_recipient_address() {
		$order           = $this->shipping_item->get_order();
		$full_name       = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
		$name            = $order->get_shipping_company() ? $order->get_shipping_company() : $full_name;
		$additional_info = null;

		$bring_additional_info_recipient = filter_input( INPUT_POST, '_bring_additional_info_recipient', FILTER_SANITIZE_STRING );

		if ( ! is_null( $bring_additional_info_recipient ) ) {
			$additional_info = $bring_additional_info_recipient;
		}

		return apply_filters(
			'bring_fraktguiden_get_consignment_recipient_address',
			[
				'name'                  => $name,
				'addressLine'           => $order->get_shipping_address_1(),
				'addressLine2'          => $order->get_shipping_address_2(),
				'postalCode'            => $order->get_shipping_postcode(),
				'city'                  => $order->get_shipping_city(),
				'countryCode'           => $order->get_shipping_country(),
				'reference'             => null,
				'additionalAddressInfo' => $additional_info,
				'contact'               => [
					'name'        => $full_name,
					'email'       => $order->get_billing_email(),
					'phoneNumber' => $order->get_billing_phone(),
				],
			],
			$order,
			$this
		);
	}

	/**
	 * Create data
	 *
	 * @return array
	 */
	public function create_data() {

		$consignment = $this->create_consignment();

		$data = [
			'testIndicator' => ( 'yes' === Fraktguiden_Helper::get_option( 'booking_test_mode_enabled' ) ),
			'schemaVersion' => 1,
			'consignments'  => [ $consignment ],
		];

		return apply_filters( 'bring_fraktguiden_booking_consignment_data', $data, $this );
	}

	/**
	 * Create
	 *
	 * @param WC_Order_Item_Shipping $shipping_item Shipping item.
	 *
	 * @throws Exception Exception.
	 *
	 * @return Bring_Booking_Consignment_Request
	 */
	public static function create( WC_Order_Item_Shipping $shipping_item ): Bring_Booking_Consignment_Request {
		$bring_product = self::get_bring_product( $shipping_item );

		if (
			filter_var(
				Fraktguiden_Helper::get_option( 'booking_without_bring' ),
				FILTER_VALIDATE_BOOLEAN
			)
			&& ! $bring_product
		) {
			$bring_product = (new Get_First_Enabled_Bring_Product())();
			$shipping_item->update_meta_data( 'bring_product', $bring_product );
			$shipping_item->save();
		}
		if ( ! $bring_product ) {
			$shipping_item->update_meta_data('bring_product', '5800');
			$shipping_item->save();
			throw new Exception( 'No bring product was found on the shipping method' );
		}

		return new self( $shipping_item );
	}

	private function create_consignment(): array {
		$is_bulk = $_REQUEST['action'] === 'bring_bulk_book';

		$recipient_address = $this->get_recipient_address();
		$consignment = [
			'shippingDateTime' => $this->shipping_date_time,
			// Sender and recipient.
			'parties'          => [
				'sender'    => $this->get_sender_address(),
				'recipient' => $recipient_address,
			],
			// Product / Service.
			'product'          => [
				'id'                 => strtoupper( $this->service_id ),
				'customerNumber'     => $this->customer_number,
				'services'           => null,
				'customsDeclaration' => null,
			],
			'purchaseOrder'    => null,
			'correlationId'    => null,
			// Packages.
			'packages'         => $this->create_packages(),
		];

		if ( ! empty( $this->customer_specified_delivery_date_time ) ) {
			$consignment['customerSpecifiedDeliveryDateTime'] = $this->customer_specified_delivery_date_time;
		}

		// Add pickup point.
		$pickup_point_id = $this->shipping_item->get_meta( 'pickup_point_id' );

		if ( $pickup_point_id ) {
			$consignment['parties']['pickupPoint'] = [
				'id'          => $pickup_point_id,
				'countryCode' => $this->shipping_item->get_order()->get_shipping_country(),
			];
		}

		$consignment['product']['additionalServices'] = [];
		$electronic_notification = filter_input( INPUT_POST, '2084', FILTER_VALIDATE_BOOLEAN );
		$evarsling = ( $this->service ? $this->service->vas_match( [ '2084', 'EVARSLING' ] ) : false );
		if (
			$evarsling
			&& ($electronic_notification || $is_bulk)
		) {
			$consignment['product']['additionalServices'][] = [
				'id'     => $evarsling,
				'email'  => $recipient_address['contact']['email'],
				'mobile' => $recipient_address['contact']['phoneNumber'],
			];

		}

		// Bag on door option
		$bag_on_door_consent = filter_input( INPUT_POST, 'bag_on_door', FILTER_VALIDATE_BOOLEAN );
		if (
			$this->service
			&& $this->service->has_vas( '1081' )
			&& ($bag_on_door_consent || $is_bulk)
		) {
			$consignment['product']['additionalServices'][] = ['id' => '1081'];
		}

		// ID verification
		$id_verification_checked = filter_input( INPUT_POST, 'id_verification', FILTER_VALIDATE_BOOLEAN );
		if (
			$this->service
			&& $this->service->has_vas( '1133' )
			&& ($id_verification_checked || $is_bulk)
		) {
			$consignment['product']['additionalServices'][] = ['id' => '1133'];
		}

		// Personal delivery option
		$individual_verification_checked = filter_input( INPUT_POST, 'individual_verification', FILTER_VALIDATE_BOOLEAN );
		if (
			$this->service
			&& $this->service->has_vas( '1134' )
			&& ($individual_verification_checked || $is_bulk)
		) {
			$consignment['product']['additionalServices'][] = ['id' => '1134'];
		}

		return $consignment;
	}
}
