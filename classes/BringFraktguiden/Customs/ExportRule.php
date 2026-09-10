<?php

namespace BringFraktguiden\Customs;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Whether a booking must carry export customs data.
 *
 * A shipment that leaves Norway is an export, not transit. Bring requires
 * customs data on the services that carry 'customs' => true in
 * config/services.php. See doc/export.md.
 */
class ExportRule
{
	/**
	 * Return whether a booking must carry export customs data.
	 *
	 * @param string $from    The country code of the sender.
	 * @param string $to      The country code of the recipient.
	 * @param string $product The Bring product, for example BUSINESS_PARCEL.
	 */
	public static function requires_customs_data(string $from, string $to, string $product): bool
	{
		$from = strtoupper($from);
		$to   = strtoupper($to);

		if ('NO' !== $from || '' === $to || 'NO' === $to) {
			return false;
		}

		$service = Fraktguiden_Helper::get_service_data_for_key($product);

		return $service['customs'] ?? false;
	}
}
