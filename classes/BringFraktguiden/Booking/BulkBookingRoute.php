<?php

namespace BringFraktguiden\Booking;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguiden\Customs\BulkHsCodes;
use BringFraktguiden\Customs\BulkOrders;
use BringFraktguiden\Customs\CustomsWarning;
use BringFraktguiden\Customs\CustomsWarningView;
use BringFraktguiden\Services\CrossBorderRule;
use WC_Order;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * The one route the bulk booking modal talks to.
 *
 * The payload holds the selected order ids and the HS codes a shop worker set.
 * The codes are written first, and the answer carries the fresh markup of the
 * groups, of every warning and of the table. So the browser never builds any of
 * it itself.
 */
class BulkBookingRoute
{
	public const ROUTE_NAMESPACE = 'bring-fraktguiden/v1';

	public const ROUTE = '/orders/bulk-booking';

	public static function init(): void
	{
		add_action('rest_api_init', [self::class, 'register']);
	}

	public static function register(): void
	{
		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ROUTE,
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [self::class, 'handle'],
				'permission_callback' => [self::class, 'may_edit'],
			]
		);
	}

	/**
	 * Only a person who may edit this shop's orders can touch the codes.
	 */
	public static function may_edit(): bool
	{
		return current_user_can('edit_shop_orders');
	}

	public static function handle(WP_REST_Request $request): WP_REST_Response
	{
		$order_ids = array_map('intval', (array) $request->get_param('orders'));

		BulkHsCodes::save($order_ids, (array) $request->get_param('codes'));

		$html = self::groups_html($order_ids)
			. self::cross_border_html($order_ids)
			. self::warning_html($order_ids)
			. self::html(BulkHsCodes::rows($order_ids));

		return new WP_REST_Response(['html' => $html]);
	}

	/**
	 * Render the groups of the selection.
	 *
	 * @param int[] $order_ids
	 */
	private static function groups_html(array $order_ids): string
	{
		$booking_groups = [];

		foreach (BulkBookingGroups::of($order_ids) as $group => $orders) {
			$booking_groups[$group] = array_map(
				fn(WC_Order $order) => [
					'url'    => $order->get_edit_order_url(),
					'number' => (string) $order->get_order_number(),
				],
				$orders
			);
		}

		$booking_settings_url = admin_url('admin.php?page=bring_fraktguiden_booking');

		$booking_without_bring = filter_var(
			Fraktguiden_Helper::get_option('booking_without_bring'),
			FILTER_VALIDATE_BOOLEAN
		);

		ob_start();
		require dirname(__DIR__, 3) . '/build/templates/admin/parts/booking-groups.php';

		return (string) ob_get_clean();
	}

	/**
	 * Render one cross border warning for the whole selection.
	 *
	 * Bring sells some services inside one country only, and refuses a booking
	 * that leaves the country on such a service. The banner names every order
	 * of the selection that the sender country and the service disagree on, and
	 * the service it ships with, so the worker knows which one to change.
	 *
	 * @param int[] $order_ids
	 */
	private static function cross_border_html(array $order_ids): string
	{
		$from = (string) Fraktguiden_Helper::get_option('booking_address_country');

		$cross_border_orders = [];

		foreach ($order_ids as $order_id) {
			$order = wc_get_order((int) $order_id);

			if (!$order instanceof WC_Order) {
				continue;
			}

			$service = BulkOrders::service($order);

			if (CrossBorderRule::allows($from, $order->get_shipping_country(), $service)) {
				continue;
			}

			$cross_border_orders[] = [
				'url'     => $order->get_edit_order_url(),
				'number'  => (string) $order->get_order_number(),
				'service' => self::service_name($service),
			];
		}

		if (!$cross_border_orders) {
			return '';
		}

		ob_start();
		require dirname(__DIR__, 3) . '/build/templates/admin/parts/cross-border-warning.php';

		return (string) ob_get_clean();
	}

	/**
	 * Return the name Bring gives the service, or the code when it has none.
	 */
	private static function service_name(string $service): string
	{
		return Fraktguiden_Helper::get_service_data_for_key($service)['productName'] ?? $service;
	}

	/**
	 * Render one customs warning for the whole selection.
	 *
	 * @param int[] $order_ids
	 */
	private static function warning_html(array $order_ids): string
	{
		$warning = CustomsWarning::for_orders(
			BulkOrders::with_customs($order_ids),
			BulkOrders::service(...)
		);

		ob_start();
		CustomsWarningView::render($warning);

		return (string) ob_get_clean();
	}

	/**
	 * Render the table the booking box shows, with the rows of every order.
	 *
	 * @param array<int, array{name: string, image: string, code: string}> $hs_rows
	 */
	private static function html(array $hs_rows): string
	{
		if (!$hs_rows) {
			return '';
		}

		ob_start();
		require dirname(__DIR__, 3) . '/build/templates/admin/parts/customs-products.php';

		return (string) ob_get_clean();
	}
}
