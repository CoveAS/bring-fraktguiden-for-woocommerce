<?php

namespace Bring_Fraktguiden\Factories;

use Bring_Fraktguiden\Actions\CreateDateFromArray;
use Bring_Fraktguiden\Calculators\PriceCalculator;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;
use Bring_Fraktguiden\Sanitizers\Sanitize_Alternative_Delivery_Dates;
use Exception;

class RateFactory {
	static protected string $field_key = 'woocommerce_bring_fraktguiden_services';
	private array           $services;

	public function __construct(
		public string $id,
		public bool $debug,
		public float $fee,
		public bool $display_description,
	) {
		$this->services = Fraktguiden_Service::all( self::$field_key );
	}

	/**
	 * @throws Exception
	 */
	public function make( array $service_details, callable $log ): ?array {
		$bring_product          = $service_details['id'];
		$expected_delivery_date = false;
		if ( ! empty( $service_details['expectedDelivery']['expectedDeliveryDate'] ) ) {
			$expected_delivery_date = ( new CreateDateFromArray )(
				                          $service_details['expectedDelivery']['expectedDeliveryDate']
			                          )->format( 'c' ) ?? '';
		}

		if ( empty( $this->services[ $bring_product ] ) ) {
			if ( 'yes' === $this->debug ) {
				$log( 'Unidentified bring product: ' . $bring_product );
			}

			return null;
		}

		if ( ! empty( $service_details['errors'] ) ) {
			// Most likely an error.
			$log( $service_details['errors'] );

			return null;
		}
		$service       = Fraktguiden_Service::find( self::$field_key, $bring_product );
		$service_price = $service_details['price']['netPrice']['priceWithAdditionalServices']
		                 ?? $service_details['price']['netPrice']['priceWithoutAdditionalServices']
		                    ?? null;
		// Net price is only provided when a customer number is used in the API request. Fallback to list price.
		if ( Fraktguiden_Helper::get_option( 'price_to_use', 'net' ) === 'list' ?? empty( $service_price ) ) {
			$service_price = $service_details['price']['listPrice']['priceWithAdditionalServices']
			                 ?? $service_details['price']['listPrice']['priceWithoutAdditionalServices']
			                    ?? $service_price;
		}

		if ( $service->get_setting( 'custom_price_cb' ) !== 'on' && ! $service_price ) {
			if ( empty( $service_details['warnings'] ) ) {
				$log( [ __( 'No price provided for' ) . ' ' . $service_details['id'] . '. ' . __( 'Please consider setting a custom price for this service.' ) ] );

				return null;
			}
			$no_price = false;
			foreach ( $service_details['warnings'] as $warning ) {
				if ( 'NO_PRICE_INFORMATION' === $warning['code'] ) {
					$no_price = true;
					break;
				}
				$log( [ 'Warning: ' . $warning['description'] ] );
			}
			if ( ! $no_price ) {
				return null;
			}
		} elseif ( $service->get_setting( 'custom_price_cb' ) === 'on' ) {
			$service_price = [
				'amountWithoutVAT' => ( new PriceCalculator() )->excl_vat( $service->settings['custom_price'] )
			];
		}

		$bring_product = sanitize_title( $service_details['id'] );
		$cost          = $service_price['amountWithoutVAT'] ?? 0;
		$label         = $service_details['guiInformation']['productName'];
		$meta_data     = [
			'bring_description'               => $service_details['guiInformation']['descriptionText'],
			'bring_logo_alt'                  => $service_details['guiInformation']['logo'] ?? null,
			'bring_logo_url'                  => $service_details['guiInformation']['logoUrl'] ?? null,
			'bring_environmental_logo_url'    => $service_details['guiInformation']['environmentalLogoUrl'] ?? null,
			'bring_environmental_tag_url'     => $service_details['guiInformation']['environmentalTagUrl'] ?? null,
			'bring_environmental_description' => $service_details['environmentalData'][0]['description'] ?? null,
		];

		$rate = [
			'id'                     => $this->id,
			'bring_product'          => $bring_product,
			'cost'                   => (float) $cost + $this->fee,
			'label'                  => $label,
			'expected_delivery_date' => $expected_delivery_date,
			'meta_data'              => $meta_data,
		];

		if (
			! empty( $service_details['expectedDelivery']['alternativeDeliveryDates'] )
			&& Fraktguiden_Service::vas_for( self::$field_key, $bring_product, [ 'alternative_delivery_dates' ] )
		) {
			$rate['alternative_delivery_dates'] = Sanitize_Alternative_Delivery_Dates::sanitize(
				$service_details['expectedDelivery']['alternativeDeliveryDates']
			);
		}

		return apply_filters(
			'bring_product_api_rate',
			$rate,
			$service_details
		);
	}
}
