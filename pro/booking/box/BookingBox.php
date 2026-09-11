<?php

namespace BringFraktguidenPro\Booking\Box;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;
use BringFraktguiden\Customs\CustomsRoute;
use BringFraktguiden\Customs\CustomsWarning;
use BringFraktguiden\Customs\NatureOfCargo;
use BringFraktguidenPro\Booking\Bring_Booking;
use BringFraktguidenPro\Booking\Consignment_Request\Bring_Booking_Consignment_Request;
use BringFraktguidenPro\Booking\Bring_Booking_Customer;
use BringFraktguidenPro\Booking\Views\Bring_Booking_Labels;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use Exception;
use WC_Order;

/**
 * The Bring booking box of the order screen.
 *
 * The box is one form. It opens filled from the order, or from the draft the
 * shop worker left behind. A booked order shows its bookings instead, with a
 * button that opens the form again.
 *
 * The markup lives in src/templates/admin/booking/, and the compiler writes the
 * files this class loads.
 */
class BookingBox
{
	public const SERVICE_KEY = 'woocommerce_bring_fraktguiden_services';

	public static function init(): void
	{
		add_action('add_meta_boxes', [self::class, 'add'], 1, 2);
		add_action('admin_enqueue_scripts', [self::class, 'enqueue']);
	}

	/**
	 * Register the route the box talks to.
	 *
	 * A REST request is not an admin request, so this runs apart from init().
	 */
	public static function init_rest(): void
	{
		add_action('rest_api_init', [BookingRoute::class, 'register']);
	}

	/**
	 * Add the box to the order screen, classic and high performance alike.
	 */
	public static function add(string $post_type, $post): void
	{
		if (!in_array($post_type, ['shop_order', 'woocommerce_page_wc-orders'], true)) {
			return;
		}

		$order = wc_get_order($post);

		if (!$order instanceof WC_Order || !self::shows_for($order)) {
			return;
		}

		add_meta_box(
			'bring-fraktguiden-booking-box',
			__('Bring Booking', 'bring-fraktguiden-for-woocommerce'),
			fn() => print(self::html($order)),
			$post_type,
			'normal',
			'high'
		);
	}

	public static function enqueue(string $hook): void
	{
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;

		if (!$screen || !in_array($screen->id, ['shop_order', 'woocommerce_page_wc-orders'], true)) {
			return;
		}

		$plugin_dir = dirname(__DIR__, 3);

		// The script is an ES module and imports a shared chunk, so the tag needs
		// type="module". BringFraktguidenPro::add_type_module adds it.
		wp_enqueue_script(
			'bfg-booking-box',
			plugins_url(basename($plugin_dir) . '/build/js/booking-box.js'),
			[],
			\Bring_Fraktguiden::VERSION,
			true
		);
	}

	/**
	 * Return the markup of the box.
	 *
	 * The REST route calls this too, so the browser and the redraw share one
	 * render path.
	 *
	 * @param bool   $force_form Show the form even when the order is booked.
	 * @param string $error      A message to show above the form.
	 */
	public static function html(WC_Order $order, bool $force_form = false, string $error = ''): string
	{
		$adapter        = new Bring_WC_Order_Adapter($order);
		$shipping_items = $adapter->get_fraktguiden_shipping_items();
		$shipping_item  = reset($shipping_items) ?: null;

		$form    = BookingDraft::read($order) ?? BookingForm::from_order($order, $shipping_item);
		$records = BookingHistory::newest_first($order);

		$booked       = (bool) array_filter($records, fn(BookingRecord $record) => !$record->failed());
		$showing_form = $force_form || !$booked;

		// A shop worker who reloads after a refused booking still needs the
		// reason, so the form carries the newest failure.
		$last_failure = ($records && $records[0]->failed()) ? $records[0] : null;

		$service  = $form->service();
		$services = Fraktguiden_Service::all(self::SERVICE_KEY, true);

		$customers      = [];
		$customer_error = '';

		try {
			$customers = Bring_Booking_Customer::get_customer_numbers_formatted();
		} catch (Exception $exception) {
			$customer_error = $exception->getMessage();
		}

		$warning = $shipping_item
			? CustomsWarning::for_order($order, (string) $form->service)
			: null;

		$needs_cargo   = (bool) CustomsRoute::for_order($order, (string) $form->service);
		$cargo_reasons = NatureOfCargo::cases();
		$wants_date    = (bool) ($service?->service_data['delivery_date'] ?? false);

		$token      = $showing_form ? BookingToken::current($order) : '';
		$labels_url = Bring_Booking_Labels::create_download_url($order->get_id());
		$test_mode  = Bring_Booking::is_test_mode();

		$rest_url = rest_url(BookingRoute::ROUTE_NAMESPACE . '/orders/' . $order->get_id() . '/booking');
		$nonce    = wp_create_nonce('wp_rest');

		$has_shipping_line = (bool) $shipping_item;

		[$sender, $recipient] = self::parties($shipping_item, $form);

		ob_start();
		require dirname(__DIR__, 3) . '/build/templates/admin/booking/box.php';

		return (string) ob_get_clean();
	}

	/**
	 * Return the two addresses the booking would send.
	 *
	 * The shop worker reads them before the send, so the preview carries the
	 * extra address lines of the form.
	 *
	 * @return array{0: array|null, 1: array|null}
	 */
	private static function parties($shipping_item, BookingForm $form): array
	{
		if (!$shipping_item) {
			return [null, null];
		}

		try {
			$request                            = Bring_Booking_Consignment_Request::create($shipping_item);
			$request->additional_info_sender    = $form->info_sender;
			$request->additional_info_recipient = $form->info_recipient;

			return [$request->get_sender_address(), $request->get_recipient_address()];
		} catch (Exception) {
			return [null, null];
		}
	}

	/**
	 * Return whether this order gets a Bring booking box at all.
	 */
	private static function shows_for(WC_Order $order): bool
	{
		if ('yes' === Fraktguiden_Helper::get_option('booking_without_bring')) {
			return true;
		}

		return (new Bring_WC_Order_Adapter($order))->has_bring_shipping_methods();
	}
}
