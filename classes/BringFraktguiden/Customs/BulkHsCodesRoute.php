<?php

namespace BringFraktguiden\Customs;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * The one route the bulk booking modal talks to.
 *
 * The payload holds the selected order ids and the HS codes a shop worker set.
 * The codes are written first, and the answer carries the fresh markup of the
 * customs warning and the table. So the browser never builds either itself.
 */
class BulkHsCodesRoute
{
	public const ROUTE_NAMESPACE = 'bring-fraktguiden/v1';

	public const ROUTE = '/orders/hs-codes';

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

		$html = self::warning_html($order_ids) . self::html(BulkHsCodes::rows($order_ids));

		return new WP_REST_Response(['html' => $html]);
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
