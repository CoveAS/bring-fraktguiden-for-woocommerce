<?php

namespace BringFraktguiden\Booking;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * The return label that rides along with an outbound booking.
 *
 * Bring takes a returnProduct element next to product in a booking request and
 * answers with both labels. Bring invoices the return only when the customer
 * uses it. See doc/return-label.md.
 *
 * Only a parcel inside Norway carries a return this way. Such a service carries
 * 'return_label' => true in config/services.php.
 */
class ReturnLabel
{
	/**
	 * The return services a shop can pick, by Bring service code.
	 *
	 * Each service names a title and a description, because the codes differ in
	 * ways the name alone does not carry.
	 *
	 * @return array<string, array{title: string, description: string}>
	 */
	public static function services(): array
	{
		return [
			'9350' => [
				'title'       => __( 'Retur til bedrift', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'Bring drives the return to your address. Your customer hands the parcel in at a post office, a parcel box or their own mailbox. Pick this one if you want the return delivered to you.', 'bring-fraktguiden-for-woocommerce' ),
			],
			'9300' => [
				'title'       => __( 'Retur fra hentested', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'The return waits at the pick-up point nearest you, and you fetch it there. Your customer hands the parcel in the same way as with Retur til bedrift.', 'bring-fraktguiden-for-woocommerce' ),
			],
			'9000' => [
				'title'       => __( 'Retur pakke fra bedrift', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'For a return from a business customer, up to 35 kg per parcel. Pick this one only if you sell to businesses.', 'bring-fraktguiden-for-woocommerce' ),
			],
			'9600' => [
				'title'       => __( 'Retur ekspress', 'bring-fraktguiden-for-woocommerce' ),
				'description' => __( 'For a return from a business customer, delivered the next day with a time guarantee. It costs more than Retur pakke fra bedrift.', 'bring-fraktguiden-for-woocommerce' ),
			],
		];
	}

	/**
	 * The services as a plain code to title map, for a select.
	 *
	 * @return array<string, string>
	 */
	public static function titles(): array
	{
		return array_map( fn( array $service ) => $service['title'], self::services() );
	}

	/**
	 * Return the return service a booking of this product must carry.
	 *
	 * Return null when the shop wants no return label, or when Bring does not
	 * pair a return with this product.
	 *
	 * @param string $product The outbound Bring product, for example 5800.
	 */
	public static function for_product( string $product ): ?string
	{
		$service = Fraktguiden_Helper::get_option( 'booking_return_service', 'none' );

		if ( ! isset( self::services()[ $service ] ) ) {
			return null;
		}

		return self::accepts( $product ) ? $service : null;
	}

	/**
	 * The names of the outbound services that carry a return label.
	 *
	 * @return string[]
	 */
	public static function product_names(): array
	{
		$names = [];

		foreach ( Fraktguiden_Helper::get_services_data() as $group ) {
			foreach ( $group['services'] as $service ) {
				if ( $service['return_label'] ?? false ) {
					$names[] = $service['productName'];
				}
			}
		}

		return $names;
	}

	/**
	 * Return whether Bring pairs a return with an outbound product.
	 *
	 * @param string $product The outbound Bring product, for example 5800.
	 */
	public static function accepts( string $product ): bool
	{
		$product = strtoupper( $product );

		foreach ( Fraktguiden_Helper::get_services_data() as $group ) {
			$service = $group['services'][ $product ] ?? null;

			if ( $service ) {
				return $service['return_label'] ?? false;
			}
		}

		return false;
	}
}
