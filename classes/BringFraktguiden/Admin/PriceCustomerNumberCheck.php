<?php

namespace BringFraktguiden\Admin;

use BringFraktguiden\Settings\SettingsMigration;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;

/**
 * Proves after a settings save that Bring accepts the customer number.
 *
 * Bring answers some accounts with an error when a rate query carries their
 * customer number. The shop only finds out by asking, so the check runs one
 * rate query from the store postal code to itself.
 */
final class PriceCustomerNumberCheck
{
	/** The saved fields that make the answer of Bring change. */
	private const FIELDS = ['use_customer_number_to_get_prices', 'mybring_customer_number'];

	/** Set when the check turned the setting off. The notice reads it. */
	public const OPTION = 'bring_fraktguiden_price_customer_number_unsupported';

	public static function init(): void
	{
		add_action('update_option_' . SettingsMigration::PLUGIN_OPTION, [self::class, 'after_save'], 10, 2);
	}

	/**
	 * Run the check on the settings the shop owner just saved.
	 *
	 * @param mixed $old_value The settings before the save.
	 * @param mixed $value     The settings after the save.
	 */
	public static function after_save($old_value, $value): void
	{
		// The check saves the settings again, which fires this hook a second time.
		static $running = false;

		if ($running) {
			return;
		}

		// Other code writes this option too. Only a submitted settings form
		// names the fields it rendered, and only that is worth an API call.
		if (! array_intersect(self::FIELDS, array_map('sanitize_key', (array) ($_POST['bfg_rendered'] ?? [])))) {
			return;
		}

		// The helper read the settings before the save, so it still holds the old ones.
		Fraktguiden_Helper::$options = is_array($value) ? $value : [];

		if (! Fraktguiden_Helper::price_customer_number()) {
			update_option(self::OPTION, '');

			return;
		}

		$postcode = (string) Fraktguiden_Helper::get_option('from_zip');

		// Without the store postal code the query fails for its own reason,
		// which says nothing about the customer number.
		if (! $postcode) {
			return;
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
	}
}
