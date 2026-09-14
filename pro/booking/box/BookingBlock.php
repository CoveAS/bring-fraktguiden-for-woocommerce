<?php

namespace BringFraktguidenPro\Booking\Box;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Bring_Booking;

/**
 * A reason why this shop cannot book, and the page that fixes it.
 *
 * The booking box prints the reason in place of the form, and the REST route
 * refuses a send while a reason stands.
 */
class BookingBlock
{
	public function __construct(
		public readonly string $message,
		public readonly string $link_text,
		public readonly string $url,
	) {}

	/**
	 * Return the first reason that blocks booking, or null when none does.
	 */
	public static function find(): ?self
	{
		if (!Fraktguiden_Helper::pro_activated()) {
			return new self(
				__('Booking is a Pro feature, and Pro is off on this shop.', 'bring-fraktguiden-for-woocommerce'),
				__('Turn on Pro', 'bring-fraktguiden-for-woocommerce'),
				admin_url('admin.php?page=bring_fraktguiden_pro'),
			);
		}

		if (!Bring_Booking::is_valid_for_use()) {
			return new self(
				__('The Mybring account is not connected, so Bring cannot take a booking.', 'bring-fraktguiden-for-woocommerce'),
				__('Connect the account', 'bring-fraktguiden-for-woocommerce'),
				admin_url('admin.php?page=bring_fraktguiden_home'),
			);
		}

		return null;
	}
}
