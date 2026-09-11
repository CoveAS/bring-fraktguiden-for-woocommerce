<?php

namespace BringFraktguidenPro\Booking\Box;

/**
 * One package of a booking.
 *
 * The booking form holds kilograms and centimetres, because a shop worker reads
 * a parcel label in those units. Bring wants grams, so the conversion happens
 * once, in to_meta().
 */
class PackageLine
{
	public function __construct(
		public float $weight_in_kg = 0.0,
		public float $length = 0.0,
		public float $width = 0.0,
		public float $height = 0.0,
	) {
	}

	/**
	 * Build a line from one payload row.
	 *
	 * @param array<string, mixed> $row
	 */
	public static function from_payload(array $row): self
	{
		return new self(
			max(0.0, (float) ($row['weight_in_kg'] ?? 0)),
			max(0.0, (float) ($row['length'] ?? 0)),
			max(0.0, (float) ($row['width'] ?? 0)),
			max(0.0, (float) ($row['height'] ?? 0)),
		);
	}

	/**
	 * Build a line from one row of `_fraktguiden_packages_v2`.
	 *
	 * @param array<string, mixed> $package
	 */
	public static function from_meta(array $package): self
	{
		return new self(
			((float) ($package['weight_in_grams'] ?? 0)) / 1000,
			(float) ($package['length'] ?? 0),
			(float) ($package['width'] ?? 0),
			(float) ($package['height'] ?? 0),
		);
	}

	/**
	 * Return the row shape that `_fraktguiden_packages_v2` holds.
	 *
	 * @return array<string, mixed>
	 */
	public function to_meta(): array
	{
		return [
			'weight_in_grams' => $this->weight_in_kg * 1000,
			'length'          => $this->length,
			'width'           => $this->width,
			'height'          => $this->height,
		];
	}

	/**
	 * @return array<string, float>
	 */
	public function to_array(): array
	{
		return [
			'weight_in_kg' => $this->weight_in_kg,
			'length'       => $this->length,
			'width'        => $this->width,
			'height'       => $this->height,
		];
	}
}
