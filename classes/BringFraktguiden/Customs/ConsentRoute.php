<?php

namespace BringFraktguiden\Customs;

use WP_REST_Response;
use WP_REST_Server;

/**
 * The route the customs consent callout signs through.
 *
 * The callout shows in the booking box and in the bulk booking modal, so both
 * screens sign here. The consent is a shop setting, so one call covers every
 * order. See CustomsConsent.
 */
class ConsentRoute
{
	public const ROUTE_NAMESPACE = 'bring-fraktguiden/v1';

	public const ROUTE = '/customs-consent';

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
				'permission_callback' => [self::class, 'may_sign'],
			]
		);
	}

	/**
	 * The consent binds the whole shop, so only a shop owner signs it.
	 */
	public static function may_sign(): bool
	{
		return current_user_can('manage_options');
	}

	public static function handle(): WP_REST_Response
	{
		CustomsConsent::sign();

		return new WP_REST_Response(['signed' => true]);
	}
}
