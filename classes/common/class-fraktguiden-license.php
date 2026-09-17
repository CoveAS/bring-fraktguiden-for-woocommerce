<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Common;

use Bring_Fraktguiden;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Fraktguiden_License class
 */
class Fraktguiden_License
{

	public const STATE_OPTION = 'bring_fraktguiden_license_state';

	/**
	 * The license server, when no constant names another one.
	 */
	public const SERVER_URL = 'https://bringfraktguiden.no/';

	protected static self $instance;

	/**
	 * Get instance
	 * Singleton helper. Get the current instance of the class
	 */
	public static function get_instance(): Fraktguiden_License
	{
		if (!isset(self::$instance)) {
			self::$instance = new Fraktguiden_License();
		}
		return self::$instance;
	}

	/**
	 * The address of the license check script.
	 */
	public static function check_url(): string
	{
		if (defined('BRING_LICENSE_URL')) {
			return BRING_LICENSE_URL;
		}

		return self::SERVER_URL.'license-check.php';
	}

	/**
	 * The root of the license server.
	 *
	 * BRING_LICENSE_URL names the check script, so the root is the folder that
	 * holds it.
	 */
	public static function server_url(): string
	{
		if (defined('BRING_LICENSE_URL')) {
			return trailingslashit(dirname(BRING_LICENSE_URL));
		}

		return self::SERVER_URL;
	}

	/**
	 * The page that sells a license.
	 *
	 * The domain of this shop travels in the query. The license server keeps it
	 * and fills the website field of its checkout.
	 */
	public static function purchase_url(): string
	{
		$domain = wp_parse_url(get_site_url(), PHP_URL_HOST) ?: '';

		return add_query_arg('domain', $domain, self::server_url());
	}

	/**
	 * Ask the license server a question.
	 *
	 * @param array $data GET parameters.
	 *
	 * @return array|false The answer, or false when the server does not answer.
	 */
	public function request($data)
	{
		$url = self::check_url().'?'.http_build_query($data);

		$response = wp_remote_get(
			$url,
			[
				'timeout'    => 5,
				'user-agent' => 'Bring plugin @ '.get_site_url(),
			]
		);

		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			return false;
		}

		$data = json_decode(wp_remote_retrieve_body($response), true);

		if (empty($data)) {
			return false;
		}

		return $data;
	}

	/**
	 * Valid
	 *
	 * Check if the bring license is valid or not
	 */
	public function valid(): bool
	{
		// store_answer() writes the option only for a date in the future. A
		// missing option therefore means the shop holds no license.
		$valid_to = (int) get_option('bring_fraktguiden_pro_valid_to', 0);

		return $valid_to > time();
	}

	/**
	 * Clean a key that a user typed or pasted.
	 *
	 * The license server applies the same rule before it looks the key up.
	 * The key alphabet leaves out I, L, O and U, so those letters map to the
	 * digits a reader mistook them for.
	 *
	 * @param string $key Key.
	 *
	 * @return string
	 */
	public static function normalise_key(string $key): string
	{
		$key = strtoupper((string) preg_replace('/[^0-9a-zA-Z]/', '', $key));

		return strtr($key, ['I' => '1', 'L' => '1', 'O' => '0']);
	}

	/**
	 * Group a key in fours, for a reader.
	 *
	 * A key of 16 characters reads AAAA-BBBB-CCCC-DDDD. Only the display uses
	 * this form. Every call to the license server sends the plain key.
	 */
	public static function format_key(string $key): string
	{
		return implode('-', str_split(self::normalise_key($key), 4));
	}

	/**
	 * Get the key this shop holds.
	 */
	public static function get_key(): string
	{
		return self::normalise_key(Fraktguiden_Helper::get_option('license_key') ?? '');
	}

	/**
	 * Get the last answer of the license server.
	 *
	 * @return array
	 */
	public static function get_state(): array
	{
		$state = get_option(self::STATE_OPTION, []);

		return is_array($state) ? $state : [];
	}

	/**
	 * Check the license
	 *
	 * The caller passes a key when the shop just saved one. The settings cache
	 * still holds the key of the last request at that point.
	 *
	 * @param string|null $key Key to ask about, or null to read the saved one.
	 *
	 * @throws Exception
	 */
	public function check_license(?string $key = null): void
	{
		$url      = get_site_url();
		$url_info = wp_parse_url($url);

		if (!$url_info) {
			$this->ping();
			return;
		}

		$key = null === $key ? self::get_key() : self::normalise_key($key);

		$this->store_answer(
			$this->request($this->request_data($key ? 'check_key' : 'check_license', $key)),
			$key ? 'key' : 'domain'
		);
	}

	/**
	 * Build the parameters every license call sends.
	 *
	 * @param string $action Action.
	 * @param string $key    Cleaned key, or an empty string.
	 *
	 * @return array
	 */
	protected function request_data(string $action, string $key = ''): array
	{
		$url      = get_site_url();
		$url_info = wp_parse_url($url);

		$data = [
			'action'        => $action,
			'domain'        => $url_info['host'] ?? '',
			'url'           => $url,
			'booking_count' => $this->booking_count(),
			'pro_enabled'   => Fraktguiden_Helper::get_option('pro_enabled'),
			'version'       => Bring_Fraktguiden::VERSION,
		];

		if ($key) {
			$data['key'] = $key;
		}

		return $data;
	}

	/**
	 * Get the booking count, and drop the months that are older than two.
	 *
	 * @return array
	 */
	protected function booking_count(): array
	{
		$date_utc  = new DateTime('-2 months', new DateTimeZone('UTC'));
		$date_then = (int) $date_utc->format('Ymd');

		$count = get_option('bring_fraktguiden_booking_count', []);

		if (!is_array($count)) {
			$count = [];
		}

		$changed = false;
		foreach ($count as $date => $amount) {
			if ($date_then > $date) {
				$changed = true;
				unset($count[$date]);
			}
		}

		if ($changed) {
			update_option('bring_fraktguiden_booking_count', $count, false);
		}

		return $count;
	}

	/**
	 * Store the answer of the license server.
	 *
	 * @param array|false $data   Answer.
	 * @param string      $source Where the answer came from, key or domain.
	 *
	 * @return array The new state.
	 */
	public function store_answer($data, string $source): array
	{
		if (empty($data['data']['license'])) {
			return self::get_state();
		}

		$license = $data['data']['license'];

		$state = [
			'key_state'    => $license['key_state'] ?? '',
			'domain'       => $license['domain'] ?? '',
			'moves_left'   => isset($license['moves_left']) ? (int) $license['moves_left'] : null,
			'other_domain' => $license['other_domain'] ?? '',
			'year'         => isset($license['year']) ? (int) $license['year'] : null,
			'reason'       => $license['reason'] ?? '',
			'move_url'     => $license['move_url'] ?? '',
			'manage_url'   => $license['manage_url'] ?? '',
			'source'       => $source,
			'checked_at'   => time(),
		];

		update_option(self::STATE_OPTION, $state, false);

		// The domain owns the license, so the key of the answer is the right one.
		// It replaces a key the shop typed wrong, and fills in a missing key.
		if (!empty($license['key'])) {
			Fraktguiden_Helper::update_option('license_key', self::normalise_key($license['key']));
		}

		if (isset($license['valid_to']) && (int) $license['valid_to'] > 0) {
			update_option('bring_fraktguiden_pro_valid_to', (int) $license['valid_to']);
		}

		return $state;
	}

	/**
	 * Ping the licensing server
	 *
	 * A shop whose site URL does not parse has no domain of its own to send.
	 * The server reads the domain before it reads the action, so the ping
	 * sends the host of the request instead.
	 *
	 * @return void
	 */
	public function ping()
	{
		$this->request(
			[
				'action'  => 'ping',
				'domain'  => $_SERVER['HTTP_HOST'] ?? 'unknown',
				'version' => Bring_Fraktguiden::VERSION,
			]
		);
	}
}
