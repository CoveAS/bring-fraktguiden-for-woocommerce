<?php

namespace BringFraktguidenPro\Booking\Box;

use BringFraktguiden\Customs\OrderHsCodes;
use Exception;
use WC_Order;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * The one route the booking box talks to.
 *
 * The payload holds the booking form and the HS codes of the products. An
 * action field says what to do with the form: keep it as a draft, throw it
 * away, or book it. The codes are written on every call.
 *
 * An answer that changes the form carries the fresh markup of the box, so the
 * browser never builds the form itself. A draft save changes no form, and its
 * answer carries no markup, so the shop worker keeps the caret and the scroll
 * position while a save runs.
 */
class BookingRoute
{
	public const ROUTE_NAMESPACE = 'bring-fraktguiden/v1';

	public static function register(): void
	{
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/orders/(?P<order_id>\d+)/booking',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [self::class, 'handle'],
				'permission_callback' => [self::class, 'may_edit'],
				'args'                => [
					'order_id' => ['type' => 'integer', 'required' => true],
				],
			]
		);
	}

	/**
	 * Only a person who may edit this shop's orders can touch a booking.
	 */
	public static function may_edit(WP_REST_Request $request): bool
	{
		$order = wc_get_order((int) $request['order_id']);

		if (!$order instanceof WC_Order) {
			return false;
		}

		return current_user_can('edit_shop_order', $order->get_id())
			|| current_user_can('edit_shop_orders');
	}

	public static function handle(WP_REST_Request $request): WP_REST_Response|WP_Error
	{
		$order = wc_get_order((int) $request['order_id']);

		if (!$order instanceof WC_Order) {
			return new WP_Error('bfg_no_order', __('Order not found.', 'bring-fraktguiden-for-woocommerce'), ['status' => 404]);
		}

		$action = (string) $request->get_param('action');
		$form   = BookingForm::from_payload((array) $request->get_param('form'));

		// An HS code is a trait of the product, not of the booking, so it is
		// written here and never enters the form.
		OrderHsCodes::save($order, (array) $request->get_param('hs_codes'));

		return match ($action) {
			'reset'  => self::reset($order),
			'book'   => self::book($order, $form, (string) $request->get_param('token')),
			'form'   => self::answer($order, force_form: true),
			'reload' => self::reload($order, $form),
			default  => self::save($order, $form),
		};
	}

	private static function save(WC_Order $order, BookingForm $form): WP_REST_Response
	{
		BookingDraft::write($order, $form);

		return new WP_REST_Response(['saved' => true]);
	}

	/**
	 * Keep the draft and send the form back.
	 *
	 * A new service brings other extra services and other fields, so the box
	 * cannot redraw itself.
	 */
	private static function reload(WC_Order $order, BookingForm $form): WP_REST_Response
	{
		BookingDraft::write($order, $form);

		return self::answer($order, force_form: true);
	}

	private static function reset(WC_Order $order): WP_REST_Response
	{
		BookingDraft::clear($order);

		return self::answer($order, force_form: true);
	}

	private static function book(WC_Order $order, BookingForm $form, string $token): WP_REST_Response
	{
		if (!BookingToken::spend($order, $token)) {
			return self::answer(
				$order,
				force_form: false,
				error: __('This booking was already sent. Reload the order to book it again.', 'bring-fraktguiden-for-woocommerce')
			);
		}

		BookingDraft::write($order, $form);

		try {
			$record = BookingSender::send($order, $form);
		} catch (Exception $exception) {
			return self::answer($order, force_form: true, error: $exception->getMessage());
		}

		if (!$record->failed()) {
			BookingDraft::clear($order);
		}

		return self::answer($order, force_form: $record->failed());
	}

	private static function answer(WC_Order $order, bool $force_form, string $error = ''): WP_REST_Response
	{
		// The order was written through this request, so read it again before
		// the box renders what it now holds.
		$fresh = wc_get_order($order->get_id());

		return new WP_REST_Response([
			'html' => BookingBox::html($fresh, $force_form, $error),
		]);
	}
}
