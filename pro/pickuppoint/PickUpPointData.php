<?php

namespace BringFraktguidenPro\PickUpPoint;

class PickUpPointData {
	public function __construct(
		public string $id,
		public string $name,
		public string $address = '',
		public string $postalCode = '',
		public string $city = '',
		public string $openingHours = '',
		public array  $specialOpeningHours = [],
		public string $postenMapsLink = '',
		public string $googleMapsLink = '',
		public string $description = '',
		public float $latitude = 0,
		public float $longitude = 0,
		public string $additionalServiceCode = '',
		public array  $photos = [],
	) {
	}

	public static function fromRaw( mixed $pickUpPoint ) {
		global $wp_locale;
		if (! is_array($pickUpPoint)) {
			return null;
		}
		// Name and id is required
		if (empty( $pickUpPoint['id'] ) || empty( $pickUpPoint['name'] )) {
			return null;
		}

		$language = match(get_locale()) {
			'nn_NO',
			'nb_NO' => 'Norwegian',
			'da_DK' => 'Danish',
			'sv_SE' => 'Swedish',
			'fi' => 'Finnish',
			default => 'English'
		};

		// 🙉🙈🙊
		$description = $pickUpPoint[
			'locationDescription' . ($language === 'Norwegian' ? '' : 'English')
		] ?? '';

		$openingHours = '';
		if ($language && isset($pickUpPoint['openingHours' . $language])) {
			$openingHours = $pickUpPoint['openingHours' . $language];
		}

		$photos = [];
		if (is_array($pickUpPoint['photos'] ?? null)) {
			foreach ($pickUpPoint['photos'] as $photo) {
				if (! is_array($photo) || empty($photo['bigPhotoUrl'])) {
					continue;
				}
				$photos[] = $photo['bigPhotoUrl'];
			}
		}

		return new PickUpPointData(
			id                   : $pickUpPoint['id'],
			name                 : $pickUpPoint['name'],
			address              : $pickUpPoint['address'] ?? '',
			postalCode           : $pickUpPoint['postalCode'] ?? '',
			city                 : $pickUpPoint['city'] ?? '',
			openingHours         : $openingHours,
			specialOpeningHours  : $pickUpPoint['specialOpeningHours'] ?? '',
			postenMapsLink       : $pickUpPoint['postenMapsLink'] ?? '',
			googleMapsLink       : $pickUpPoint['googleMapsLink'] ?? '',
			description          : $description,
			latitude             : $pickUpPoint['latitude'] ?? 0,
			longitude            : $pickUpPoint['longitude'] ?? 0,
			additionalServiceCode: $pickUpPoint['additionalServiceCode'] ?? '',
			photos               : $photos,
		);

//		$settings = FraktguidenSettings::getInstance();

	}

	public static function rawCollection( array $pickUpPoints ): array {
		return array_filter(array_map(
			fn($pickUpPoint) => PickUpPointData::fromRaw($pickUpPoint),
			$pickUpPoints
		));
	}
}
