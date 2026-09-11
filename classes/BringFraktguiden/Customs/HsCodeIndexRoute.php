<?php

namespace BringFraktguiden\Customs;

use WP_REST_Response;

/**
 * The route the HS code picker reads the tariff from.
 *
 * The answer carries an ETag, so a browser that already holds the tariff gets
 * a short 304 instead of half a megabyte. See HsCodeIndex.
 */
class HsCodeIndexRoute
{
	public const ROUTE_NAMESPACE = 'bring-fraktguiden/v1';

	public const ROUTE = '/hs-codes';

	/**
	 * Whether this route answers the request now.
	 */
	private static bool $serving = false;

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

		add_filter('rest_send_nocache_headers', [self::class, 'allow_cache']);
	}

	/**
	 * Let the browser keep the answer of this route.
	 *
	 * The REST server sends no-store to a signed in user, and a browser then
	 * keeps nothing. The tariff is half a megabyte, so it must be kept.
	 *
	 * @param bool $send Whether the server sends the no-cache headers.
	 */
	public static function allow_cache(bool $send): bool
	{
		return self::$serving ? false : $send;
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
		self::$serving = true;

		$index = HsCodeIndex::get();
		$tag   = '"' . $index['version'] . '"';

		if (self::matches($tag)) {
			$response = new WP_REST_Response(null, 304);
		} else {
			$response = new WP_REST_Response($index);
		}

		$response->header('ETag', $tag);

		// The browser asks every time, and the ETag makes the answer short
		// whenever the tariff has not changed.
		$response->header('Cache-Control', 'private, max-age=0, must-revalidate');

		return $response;
	}

	/**
	 * Tell whether the browser already holds this version of the index.
	 *
	 * A cache may weaken a tag, and then it comes back with a W/ in front.
	 */
	private static function matches(string $tag): bool
	{
		$sent = trim((string) wp_unslash($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));

		return $tag === preg_replace('/^W\//', '', $sent);
	}
}
