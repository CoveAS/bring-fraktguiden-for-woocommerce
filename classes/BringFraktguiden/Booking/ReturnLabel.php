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
	 * @return array<string, string>
	 */
	public static function services(): array
	{
		return [
			'9300' => __( 'Return from pick-up point', 'bring-fraktguiden-for-woocommerce' ),
			'9350' => __( 'Return parcel to business', 'bring-fraktguiden-for-woocommerce' ),
			'9000' => __( 'Return business parcel', 'bring-fraktguiden-for-woocommerce' ),
			'9600' => __( 'Return express', 'bring-fraktguiden-for-woocommerce' ),
		];
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
