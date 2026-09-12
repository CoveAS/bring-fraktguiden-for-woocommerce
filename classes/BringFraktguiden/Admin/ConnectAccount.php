<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * The form of the setup page that connects the shop to a Bring account.
 *
 * The test asks Mybring for the customer list of the account. That endpoint
 * needs the two credential headers and nothing else, so a shop with no address
 * and no services can still connect.
 */
class ConnectAccount
{
	public const ACTION = 'bfg_connect_account';

	/** The query argument that carries the result back to the setup page. */
	public const RESULT = 'bfg-connected';

	/** The option that holds the result of the last test. */
	public const OPTION = 'mybring_authentication';

	/** The option that holds the choice to leave the account unconnected. */
	public const SKIPPED = 'bfg_connect_skipped';

	private const URL = 'https://api.bring.com/booking/api/customers';

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

		if (isset($_POST['skip'])) {
			update_option(self::SKIPPED, true);
			wp_safe_redirect(admin_url('admin.php?page=bring_fraktguiden_home'));
			exit;
		}

		// A key copied by hand often carries a space or a line break.
		$uid = trim((string) ($_POST['mybring_api_uid'] ?? ''));
		$key = trim((string) ($_POST['mybring_api_key'] ?? ''));

		// Keep what the shop owner typed, whatever Bring answers. A failed test
		// must not throw the typing away.
		Fraktguiden_Helper::update_option('mybring_api_uid', $uid);
		Fraktguiden_Helper::update_option('mybring_api_key', $key);

		update_option(self::OPTION, self::test($uid, $key) + ['credentials' => self::fingerprint($uid, $key)]);

		// The shop owner asked to connect, so the test now decides the step.
		delete_option(self::SKIPPED);

		wp_safe_redirect(add_query_arg(
			self::RESULT,
			self::connected() ? 'yes' : 'no',
			admin_url('admin.php?page=bring_fraktguiden_home')
		));
		exit;
	}

	/**
	 * Has the shop connected to Bring?
	 *
	 * The stored result belongs to the credentials it was made with. A shop
	 * owner who changes either field elsewhere is not connected any more.
	 */
	public static function connected(): bool
	{
		$result = (array) get_option(self::OPTION);
		$uid = (string) Fraktguiden_Helper::get_option('mybring_api_uid');
		$key = (string) Fraktguiden_Helper::get_option('mybring_api_key');

		if (! $uid || ! $key) {
			return false;
		}

		if (($result['credentials'] ?? '') !== self::fingerprint($uid, $key)) {
			return false;
		}

		return (bool) ($result['authenticated'] ?? false);
	}

	/** Did the shop owner choose to leave the account unconnected? */
	public static function skipped(): bool
	{
		return (bool) get_option(self::SKIPPED);
	}

	/** Names the credential pair a stored result belongs to. */
	private static function fingerprint(string $uid, string $key): string
	{
		return md5($uid . '|' . $key);
	}

	/** The message of the last test. */
	public static function message(): string
	{
		return (string) (get_option(self::OPTION)['message'] ?? '');
	}

	/**
	 * Ask Mybring for the customer list with these credentials.
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

		$response = wp_remote_get(self::URL, [
			'timeout' => 20,
			'headers' => [
				'Accept' => 'application/json',
				'X-MyBring-API-Uid' => $uid,
				'X-MyBring-API-Key' => $key,
				'X-Bring-Client-URL' => Fraktguiden_Helper::get_client_url(),
			],
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
			'message' => sprintf(
				/* translators: %s: the Bring login email of the shop. */
				__('Your shop is connected to Bring as %s.', 'bring-fraktguiden-for-woocommerce'),
				$uid
			),
		];
	}
}
