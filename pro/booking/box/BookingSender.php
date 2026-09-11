<?php

namespace BringFraktguidenPro\Booking\Box;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguiden\Customs\NatureOfCargo;
use BringFraktguidenPro\Booking\Consignment_Request\Bring_Booking_Consignment_Request;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use Exception;
use WC_Logger;
use WC_Order;

/**
 * Sends one order to Bring as one consignment.
 *
 * A WooCommerce order records no link between a product line and a shipping
 * line, so nothing says which goods travel on which shipment. One order is
 * therefore one consignment. See the failed idea in CLAUDE.md.
 */
class BookingSender
{
	/**
	 * The shop settings a booking cannot do without.
	 */
	private const REQUIRED_SETTINGS = [
		'booking_address_store_name',
		'booking_address_street1',
		'booking_address_postcode',
		'booking_address_city',
		'booking_address_country',
	];

	/**
	 * Book the order and record the attempt.
	 *
	 * @throws Exception When the shop or the order cannot be booked at all.
	 */
	public static function send(WC_Order $order, BookingForm $form): BookingRecord
	{
		self::guard_settings();

		$adapter        = new Bring_WC_Order_Adapter($order);
		$shipping_items = $adapter->get_fraktguiden_shipping_items();
		$shipping_item  = reset($shipping_items);

		if (!$shipping_item) {
			throw new Exception(__('Add a shipping line to the order before you book it.', 'bring-fraktguiden-for-woocommerce'));
		}

		self::write_shipping_item($shipping_item, $form);

		$request = Bring_Booking_Consignment_Request::create($shipping_item);
		$request->fill([
			'customer_number'                       => $form->customer_number,
			'shipping_date_time'                    => $form->shipping_date_time(),
			'customer_specified_delivery_date_time' => $form->delivery_date_time(),
			'additional_services'                   => $form->additional_services,
			'additional_info_sender'                => $form->info_sender,
			'additional_info_recipient'             => $form->info_recipient,
			'nature_of_cargo'                       => NatureOfCargo::tryFrom($form->nature_of_cargo),
		]);

		$response = $request->post();

		if ('yes' === Fraktguiden_Helper::get_option('debug')) {
			$log = new WC_Logger();
			$log->add(Fraktguiden_Helper::ID, '[BOOKING] Request data: ' . wp_json_encode($request->create_data(), JSON_PRETTY_PRINT));
			$log->add(Fraktguiden_Helper::ID, '[BOOKING] Response: ' . wp_json_encode($response->to_array(), JSON_PRETTY_PRINT));
		}

		$record = BookingHistory::append($order, $response, $form);

		self::note_outcome($order, $record);

		return $record;
	}

	/**
	 * Write what the booking needs onto the shipping line.
	 *
	 * The consignment request reads the product and the parcels from the line,
	 * so the form has to land there before the request is built.
	 */
	private static function write_shipping_item($shipping_item, BookingForm $form): void
	{
		if ($form->service) {
			$shipping_item->update_meta_data('bring_product', $form->service);
		}

		$shipping_item->update_meta_data(
			'_fraktguiden_packages_v2',
			array_map(fn(PackageLine $line) => $line->to_meta(), $form->packages)
		);

		$shipping_item->save();
	}

	/**
	 * Add an order note, and move the order on when the booking worked.
	 */
	private static function note_outcome(WC_Order $order, BookingRecord $record): void
	{
		if ($record->failed()) {
			$order->add_order_note(
				__('Bring refused the booking. See the Bring Booking box for the reason.', 'bring-fraktguiden-for-woocommerce')
			);

			return;
		}

		$status = Fraktguiden_Helper::get_option('auto_set_status_after_booking_success');
		$note   = __('Booked with Bring', 'bring-fraktguiden-for-woocommerce');

		if ('none' === $status || !$status) {
			$order->add_order_note($note);

			return;
		}

		$order->update_status($status, $note . PHP_EOL);
	}

	/**
	 * @throws Exception When the shop address is not filled in.
	 */
	private static function guard_settings(): void
	{
		foreach (self::REQUIRED_SETTINGS as $setting) {
			if (Fraktguiden_Helper::get_option($setting)) {
				continue;
			}

			throw new Exception(__('Fill in your store address in the booking settings before you book.', 'bring-fraktguiden-for-woocommerce'));
		}
	}
}
