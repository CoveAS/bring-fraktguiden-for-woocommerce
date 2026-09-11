<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * The form of the setup page that connects the shop to a Bring account.
 *
 * Bring has no endpoint that only checks credentials. Every request carries
 * them. So the test asks the shipping guide for one price and reads the answer.
 */
class ConnectAccount
{
	public const ACTION = 'bfg_connect_account';

	/** The query argument that carries the result back to the setup page. */
	public const RESULT = 'bfg-connected';

	/** The option that holds the result of the last test. */
	public const OPTION = 'mybring_authentication';

	private const URL = 'https://api.bring.com/shippingguide/v2/products';

	public static function init(): void
	{
		add_action('admin_post_' . self::ACTION, [self::class, 'handle']);
	}

	public static function handle(): void
	{
		if (! current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('You may not change the Bring settings.', 'bring-fraktguiden-for-woocommerce'), 403);
		}

		check_admin_referer(self::ACTION);

		// A key copied by hand often carries a space or a line break.
		$uid = trim((string) ($_POST['mybring_api_uid'] ?? ''));
		$key = trim((string) ($_POST['mybring_api_key'] ?? ''));

		// Keep what the shop owner typed, whatever Bring answers. A failed test
		// must not throw the typing away.
		Fraktguiden_Helper::update_option('mybring_api_uid', $uid);
		Fraktguiden_Helper::update_option('mybring_api_key', $key);

		update_option(self::OPTION, self::test($uid, $key));

		wp_safe_redirect(add_query_arg(
			self::RESULT,
			self::connected() ? 'yes' : 'no',
			admin_url('admin.php?page=bring_fraktguiden_home')
		));
		exit;
	}

	/** Has the shop connected to Bring? */
	public static function connected(): bool
	{
		return (bool) (get_option(self::OPTION)['authenticated'] ?? false);
	}

	/** The message of the last test. */
	public static function message(): string
	{
		return (string) (get_option(self::OPTION)['message'] ?? '');
	}

	/**
	 * Ask Bring for one price with these credentials.
	 *
	 * @return array{authenticated: bool, message: string}
	 */
	public static function test(string $uid, string $key): array
	{
		if (! $uid || ! $key) {
			return [
				'authenticated' => false,
				'message' => __('Fill in both the email and the API key.', 'bring-fraktguiden-for-woocommerce'),
			];
		}

		$country = ServiceWizard::sender_country();
		$zip = (string) Fraktguiden_Helper::get_option('from_zip');

		$response = wp_remote_post(self::URL, [
			'timeout' => 20,
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'X-MyBring-API-Uid' => $uid,
				'X-MyBring-API-Key' => $key,
				'X-Bring-Client-URL' => Fraktguiden_Helper::get_client_url(),
			],
			'body' => wp_json_encode([
				'withPrice' => true,
				'consignments' => [
					[
						'fromCountryCode' => $country,
						'fromPostalCode' => $zip,
						'toCountryCode' => $country,
						'toPostalCode' => $zip,
						'packages' => [['grossWeight' => 1000]],
						// ponytail: one product is enough to prove the
						// credentials. Bring refuses the whole request when they
						// are wrong, whatever the product.
						'products' => [['id' => 'SERVICEPAKKE']],
					],
				],
			]),
		]);

		if (is_wp_error($response)) {
			return [
				'authenticated' => false,
				'message' => sprintf(
					__('Bring could not be reached: %s', 'bring-fraktguiden-for-woocommerce'),
					$response->get_error_message()
				),
			];
		}

		$status = wp_remote_retrieve_response_code($response);

		if (401 === $status || 403 === $status) {
			return [
				'authenticated' => false,
				'message' => __('Bring did not accept these credentials.', 'bring-fraktguiden-for-woocommerce'),
				// The answer of Bring, for support. It is not shown to the shop owner.
				'detail' => substr((string) wp_remote_retrieve_body($response), 0, 500),
			];
		}

		if (200 !== $status) {
			return [
				'authenticated' => false,
				'message' => sprintf(
					__('Bring answered with an error (%d). Try again in a moment.', 'bring-fraktguiden-for-woocommerce'),
					$status
				),
			];
		}

		return [
			'authenticated' => true,
			'message' => __('Bring accepted your credentials.', 'bring-fraktguiden-for-woocommerce'),
		];
	}
}
