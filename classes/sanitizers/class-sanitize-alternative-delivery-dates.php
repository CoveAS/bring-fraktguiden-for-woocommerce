<?php

namespace Bring_Fraktguiden\Sanitizers;

use Exception;

/**
 * Class Fraktguiden_Helper
 *
 * Shared between regular and pro version
 */
class Sanitize_Alternative_Delivery_Dates {

	public static function sanitize( $alternative_delivery_dates ) {
		if ( ! is_array( $alternative_delivery_dates ) ) {
			return [];
		}

		$dates = [];

		foreach ( $alternative_delivery_dates as $item ) {
			try {
				$item = self::sanitize_item( $item );
			} catch ( Exception $e ) {
				// @TODO: Log the error
				continue;
			}
			$dates[] = $item;
		}
		return $dates;

	}

	protected static function sanitize_digit( $digit ) {
		if ( ! ctype_digit( $digit ) && ! is_int( $digit ) ) {
			throw new Exception( 'Digit was not a number' );
		}
		return intval( $digit );
	}

	protected static function sanitize_item( $item ) {
		if ( ! isset( $item['workingDays'] ) ) {
			throw new Exception( 'workingDays should not be empty' );
		}
		if ( ! isset( $item['shippingDate'] ) ) {
			throw new Exception( 'shippingDate should not be empty' );
		}
		if ( ! isset( $item['formattedExpectedDeliveryDate'] ) ) {
			throw new Exception( 'formattedExpectedDeliveryDate must be set' );
		}
        if ( ! isset( $item['expectedDeliveryDate'] ) ) {
			throw new Exception( 'expectedDeliveryDate must be set' );
		}
		$sanitized_item = [
			'workingDays' => self::sanitize_digit( $item['workingDays'] ?? '' ),
			'shippingDate' => self::sanitize_date( $item['shippingDate'], false ),
			'formattedExpectedDeliveryDate' => self::sanitize_formatted_date( $item['formattedExpectedDeliveryDate'] ),
			'expectedDeliveryDate' => self::sanitize_date( $item['expectedDeliveryDate'], true ),
		];
		return $sanitized_item;
	}

	protected static function sanitize_formatted_date( $date ) {
		if ( ! is_string( $date ) ) {
			throw new Exception( 'Formatted date must be string' );
		}
		if ( ! preg_match( '/^\d{2}\.\d{2}\.\d{4}$/', $date ) ) {
			throw new Exception( 'Formatted date has incorrect format' );
		}
		return $date;
	}

	protected static function sanitize_date( $date, $with_time_slots ) {

		if ( empty( $date['year'] ) ) {
			throw new Exception( 'Date must contain year' );
		}
		if ( empty( $date['month'] ) ) {
			throw new Exception( 'Date must contain month' );
		}
		if ( empty( $date['day'] ) ) {
			throw new Exception( 'Date must contain day' );
		}
		$sanitized_item = [
          'year' => self::sanitize_digit( $date['year'] ),
          'month' => self::sanitize_digit( $date['month'] ),
          'day' => self::sanitize_digit( $date['day'] ),
		];
		if ( ! $with_time_slots ) {
			return $sanitized_item;
		}

		$sanitized_item['timeSlots'] = self::sanitize_time_slots( $date['timeSlots'] );

		return $sanitized_item;
	}

	protected static function sanitize_time_slots( $slots ) {
		if ( ! is_array( $slots ) ) {
			throw new Exception( 'Time slots was not an array' );
		}
		$sanitized_slots = [];
		foreach ( $slots as $slot ) {
			$sanitized_slots[] = self::sanitize_time_slot( $slot );
		}
		return $sanitized_slots;
	}

	/**
	 * @throws Exception
	 */
	protected static function sanitize_time_slot( $slot ) {
		if ( empty( $slot['startTime'] ) ) {
			throw new Exception( 'Time slots must contain startTime' );
		}
		if ( empty( $slot['endTime'] ) ) {
			throw new Exception( 'Time slots must contain endTime' );
		}
		$sanitized_slot = [
			'startTime' => [
				'hour' =>  self::sanitize_digit( $slot['startTime']['hour'] ),
				'minute' =>  self::sanitize_digit( $slot['startTime']['minute'] ),
			],
			'endTime' => [
				'hour' =>  self::sanitize_digit( $slot['endTime']['hour'] ),
				'minute' =>  self::sanitize_digit( $slot['endTime']['minute'] ),
			],
		];
		return $sanitized_slot;
	}
}
