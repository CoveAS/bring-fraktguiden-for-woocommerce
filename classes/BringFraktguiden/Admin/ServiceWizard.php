<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguiden\Utility\Config;

/**
 * The model behind the service wizard page.
 *
 * The wizard offers a service when the service carries a 'recipient' key in
 * config/services.php. The other keys it reads are 'weight', 'rfid' and
 * 'cross_border'. See the docblock of that file.
 */
class ServiceWizard
{
	public const NONCE = 'bfg_service_wizard';

	/**
	 * Every service the wizard may recommend.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function services(): array
	{
		$services = [];

		foreach (Config::get('services') as $group) {
			foreach ($group['services'] as $code => $service) {
				if (!isset($service['recipient'])) {
					continue;
				}

				$weight = $service['weight'] ?? [0, null];

				$services[] = [
					'code' => (string) $code,
					'name' => $service['productName'],
					'recipient' => $service['recipient'],
					'minWeight' => $weight[0],
					'maxWeight' => $weight[1],
					'domestic' => isset($service['cross_border']) && !$service['cross_border'],
					'rfid' => $service['rfid'] ?? null,
				];
			}
		}

		return $services;
	}

	/**
	 * The services the shop offers today.
	 *
	 * @return array<int, string>
	 */
	public static function active(): array
	{
		$active = Fraktguiden_Helper::get_option('services');

		return is_array($active) ? $active : [];
	}

	/**
	 * Saves the form of the wizard page.
	 *
	 * The save runs on admin_init, because the page callback runs after the
	 * admin screen sent its headers, and the redirect needs them open.
	 */
	public static function maybe_save(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return;
		}

		if (($_GET['page'] ?? '') !== 'bring_fraktguiden_home') {
			return;
		}

		if (($_GET['sub-page'] ?? '') !== 'service-wizard') {
			return;
		}

		self::save();
	}

	/**
	 * Saves the services the user chose, then goes to the settings page.
	 *
	 * The wizard replaces the service list. It does not add to it.
	 */
	private static function save(): void
	{
		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You are not allowed to change the shipping services.', 'bring-fraktguiden-for-woocommerce'));
		}

		check_admin_referer(self::NONCE);

		$known = array_column(self::services(), 'code');
		$chosen = array_map('sanitize_text_field', wp_unslash($_POST['bfg_services'] ?? []));
		$codes = array_values(array_intersect($known, $chosen));

		if (!$codes) {
			return;
		}

		Fraktguiden_Helper::update_option('services', $codes);

		wp_safe_redirect(admin_url('admin.php?page=bring_fraktguiden_settings&bfg-wizard=saved'));
		exit;
	}
}
