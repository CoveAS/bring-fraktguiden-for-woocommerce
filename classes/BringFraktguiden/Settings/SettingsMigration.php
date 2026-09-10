<?php

namespace BringFraktguiden\Settings;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Keeps the plugin settings option in step with the WooCommerce shipping method option.
 *
 * WooCommerce owns woocommerce_bring_fraktguiden_settings through WC_Settings_API and keeps
 * writing keys such as enabled and services to it. The plugin reads and writes
 * bring_fraktguiden_for_woocommerce_settings instead. The sync is one way, from WooCommerce
 * into the plugin option, so an older plugin version still finds working settings after a
 * roll back.
 *
 * A snapshot of the WooCommerce option records the state at the last sync. Only a key whose
 * value differs from the snapshot moves forward, so a WooCommerce write never reverts a
 * setting saved through the plugin.
 */
class SettingsMigration
{
	const WOO_OPTION = 'woocommerce_bring_fraktguiden_settings';

	const PLUGIN_OPTION = 'bring_fraktguiden_for_woocommerce_settings';

	const SNAPSHOT_OPTION = 'bring_fraktguiden_settings_snapshot';

	private static bool $synced = false;

	public static function init(): void
	{
		add_action('add_option_' . self::WOO_OPTION, [self::class, 'copy_forward']);
		add_action('update_option_' . self::WOO_OPTION, [self::class, 'copy_forward']);
	}

	/**
	 * Sync at most once per request, and only when the WooCommerce option left the snapshot.
	 */
	public static function run_once(): void
	{
		if (self::$synced) {
			return;
		}
		self::$synced = true;

		if (self::woo_settings() === self::array_option(self::SNAPSHOT_OPTION)) {
			return;
		}
		self::copy_forward();
	}

	/**
	 * Copy every WooCommerce value that changed since the last sync into the plugin option.
	 */
	public static function copy_forward(): void
	{
		$woo = self::woo_settings();
		$snapshot = self::array_option(self::SNAPSHOT_OPTION);
		$settings = self::array_option(self::PLUGIN_OPTION);

		foreach ($woo as $key => $value) {
			if (array_key_exists($key, $snapshot) && $snapshot[$key] === $value) {
				continue;
			}
			$settings[$key] = $value;
		}

		update_option(self::PLUGIN_OPTION, $settings, true);
		update_option(self::SNAPSHOT_OPTION, $woo, false);

		self::$synced = true;

		// ponytail: the Settings singleton keeps the values it was built from. A sync that runs
		// after that build leaves it stale for the rest of the request. This only happens when
		// WooCommerce saves the shipping method, and that request redirects. Give Settings a
		// reset method if a later caller needs fresh values in the same request.
		Fraktguiden_Helper::$options = null;
	}

	private static function woo_settings(): array
	{
		return self::array_option(self::WOO_OPTION);
	}

	private static function array_option(string $option): array
	{
		$value = get_option($option, []);

		return is_array($value) ? $value : [];
	}
}
