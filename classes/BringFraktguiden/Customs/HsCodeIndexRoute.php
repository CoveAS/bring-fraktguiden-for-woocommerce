<?php

namespace BringFraktguiden\Customs;

use WP_REST_Response;

/**
 * The route the HS code picker reads the tariff from.
 *
 * The browser keeps the answer, so a shop downloads the tariff once. See
 * HsCodeIndex.
 */
class HsCodeIndexRoute
{
	public const ROUTE_NAMESPACE = 'bring-fraktguiden/v1';

	public const ROUTE = '/hs-codes';

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
				'methods'             => 'GET',
				'callback'            => [self::class, 'handle'],
				'permission_callback' => [self::class, 'may_read'],
			]
		);
	}

	/**
	 * The picker sits on the product screen and on the order screen, so the
	 * route answers a user who may edit either.
	 */
	public static function may_read(): bool
	{
		return current_user_can('edit_products') || current_user_can('edit_shop_orders');
	}

	public static function handle(): WP_REST_Response
	{
		return new WP_REST_Response(HsCodeIndex::get());
	}
}
