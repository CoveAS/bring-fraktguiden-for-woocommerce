<?php

namespace BringFraktguidenPro\Booking;

use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

class Bring_Booking_Url
{
	public function __construct(readonly public Bring_WC_Order_Adapter $adapter)
	{
	}

	public function __toString(): string
	{
		$order_ids = $this->adapter->order->get_id();
		return admin_url( 'admin.php?page=bring_book_orders&order_ids=' . $order_ids );
	}
}
