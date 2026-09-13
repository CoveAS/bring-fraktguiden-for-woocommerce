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
	 * Curl request
	 *
	 * @param array $data GET parameters.
	 *
	 * @return boolean
	 */
	public function curl_request($data)
	{
		$query_string = http_build_query($data);

		// Get cURL resource.
		$handle = curl_init();

		$base = defined('BRING_LICENSE_URL') ? BRING_LICENSE_URL : 'https://bringfraktguiden.no/license-check.php';
		$url  = $base.'?'.$query_string;

		// Set some options - we are passing in a useragent too here.
		curl_setopt_array(
			$handle,
			[
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_URL            => $url,
				CURLOPT_USERAGENT      => 'Bring plugin @ '.get_site_url(),
			]
		);

		// Send the request & save response to $resp.
		$content = curl_exec($handle);

		// Get the HTTP code.
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		// Close request to clear up some resources.
		curl_close($handle);

		// handle error; error output.
		if (200 !== $code) {
			return false;
		}

		$data = json_decode($content, true);

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
			$this->curl_request($this->request_data($key ? 'check_key' : 'check_license', $key)),
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

		// A shop that never typed a key learns its key from the domain check.
		if (!self::get_key() && !empty($license['key'])) {
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
		$this->curl_request(
			[
				'action'  => 'ping',
				'domain'  => $_SERVER['HTTP_HOST'] ?? 'unknown',
				'version' => Bring_Fraktguiden::VERSION,
			]
		);
	}
}
