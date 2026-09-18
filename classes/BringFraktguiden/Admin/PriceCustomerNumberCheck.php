<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Settings\SettingsMigration;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Proves on a settings save that Bring accepts the customer number.
 *
 * Bring answers some accounts with an error when a rate query carries their
 * customer number. The shop only finds out by asking, so the check runs one
 * rate query from the postal code the shop ships from.
 */
final class PriceCustomerNumberCheck
{
	/** The saved fields that make the answer of Bring change. */
	private const FIELDS = ['use_customer_number_to_get_prices', 'mybring_customer_number'];

	/** The setting the check turns off. */
	private const SETTING = 'use_customer_number_to_get_prices';

	/** Set when the check turned the setting off. The notice reads it. */
	public const OPTION = 'bring_fraktguiden_price_customer_number_unsupported';

	public static function init(): void
	{
		// A save that changes nothing never reaches update_option_, and the
		// shop owner may press Save with the box already ticked.
		add_filter('pre_update_option_' . SettingsMigration::PLUGIN_OPTION, [self::class, 'before_save'], 12, 2);
	}

	/**
	 * Run the check on the settings the shop owner is about to save.
	 *
	 * @param mixed $value     The settings about to be saved.
	 * @param mixed $old_value The settings before the save.
	 *
	 * @return mixed The settings to save, with the setting off when Bring refuses the number.
	 */
	public static function before_save($value, $old_value)
	{
		// The check saves the settings again, which fires this filter a second time.
		static $running = false;

		if ($running || ! is_array($value)) {
			return $value;
		}

		// Other code writes this option too. Only a submitted settings form
		// names the fields it rendered, and only that is worth an API call.
		if (! array_intersect(self::FIELDS, array_map('sanitize_key', (array) ($_POST['bfg_rendered'] ?? [])))) {
			return $value;
		}

		// The helper read the settings before the save, so it still holds the old ones.
		Fraktguiden_Helper::$options = $value;

		if (! Fraktguiden_Helper::price_customer_number()) {
			update_option(self::OPTION, '');

			return $value;
		}

		// A blank from_zip falls back to the store address, the way a rate
		// query falls back. Without either the query fails for its own reason,
		// which says nothing about the customer number.
		$postcode = (string) (Fraktguiden_Helper::get_option('from_zip') ?: get_option('woocommerce_store_postcode', ''));

		if (! $postcode) {
			return $value;
		}

		$running = true;

		try {
			$result = ShippingTest::run(
				ShippingTest::sample_product(),
				(string) Fraktguiden_Helper::get_option('from_country'),
				$postcode
			);
		} finally {
			$running = false;
		}

		// A note means the test passed only after it dropped the customer number.
		update_option(self::OPTION, $result->note ? 'yes' : '');

		if ($result->note) {
			$value[self::SETTING] = 'no';
		}

		return $value;
	}
}
