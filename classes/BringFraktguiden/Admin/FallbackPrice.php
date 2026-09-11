<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguiden\Settings\Settings;

/**
 * The price the checkout shows when Bring gives no price.
 *
 * Three cases have their own settings on the Fallback Options page. The setup
 * page asks for one answer and writes it into all four.
 *
 * A case still at its default value counts as unanswered. So a shop that
 * changed nothing reads as UNDECIDED, and a new case added later asks nobody
 * to answer again.
 *
 * The setup page shows no answer of its own. It reads the settings and says
 * which of the four states they are in.
 */
final class FallbackPrice
{
	public const ACTION = 'bfg_fallback_price';

	/** No answer yet. The shop has never been asked. */
	public const UNDECIDED = 'undecided';

	/** Both cases charge the same price. */
	public const PRICE = 'price';

	/** Both cases show no shipping. */
	public const NO_SHIPPING = 'no-shipping';

	/** The cases were set apart on the Fallback Options page. */
	public const CUSTOM = 'custom';

	/** The value of a rate setting that shows no shipping at all. */
	private const NONE = '0';

	/**
	 * Where the answer itself is kept.
	 *
	 * The settings ship with the no shipping value, so an owner who answers
	 * "show no shipping" writes nothing new. This option tells that owner apart
	 * from one who never answered.
	 */
	private const ANSWER_OPTION = 'bring_fraktguiden_fallback_answer';

	/** The settings the answer writes, one pair per case. */
	private const CASES = [
		['no_connection_rate_id', 'no_connection_flat_rate'],
		['exception_rate_id', 'exception_flat_rate'],
		['alt_flat_rate_id', 'alt_flat_rate'],
	];

	private function __construct(
		public readonly string $state,
		public readonly float $price,
	) {
	}

	public static function init(): void
	{
		add_action('admin_post_' . self::ACTION, [self::class, 'handle']);
	}

	/** What the settings say today. */
	public static function current(): self
	{
		$rates = [];
		$prices = [];

		foreach (self::CASES as [$rate_key, $price_key]) {
			$rate = Fraktguiden_Helper::get_option($rate_key);
			$price = Fraktguiden_Helper::get_option($price_key);

			if (self::untouched($rate_key, $rate) && self::untouched($price_key, $price)) {
				continue;
			}

			$rates[] = (string) $rate;
			$prices[] = (float) $price;
		}

		// Every case still holds its default, which shows no shipping.
		if (! $rates) {
			$answered = self::NO_SHIPPING === get_option(self::ANSWER_OPTION);

			return new self($answered ? self::NO_SHIPPING : self::UNDECIDED, 0.0);
		}

		if (count(array_unique($rates)) > 1) {
			return new self(self::CUSTOM, 0.0);
		}

		if (self::NONE === $rates[0]) {
			return new self(self::NO_SHIPPING, 0.0);
		}

		if (count(array_unique($prices)) > 1) {
			return new self(self::CUSTOM, 0.0);
		}

		return new self(self::PRICE, $prices[0]);
	}

	/** Does the setting still hold the value it shipped with? */
	private static function untouched(string $key, mixed $value): bool
	{
		// An unsaved setting reads as false.
		if (false === $value) {
			return true;
		}

		$setting = Settings::instance()->get($key);
		if (! $setting) {
			return true;
		}

		// A price saved as 0.00 is the same price as the default 0, so both
		// sides go through the type of the setting before the comparison.
		return $setting->sanitize($value) === $setting->sanitize($setting->data['default'] ?? '');
	}

	/** Has the shop owner answered? */
	public function decided(): bool
	{
		return self::UNDECIDED !== $this->state;
	}

	public static function handle(): void
	{
		if (! current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('You may not change the Bring settings.', 'bring-fraktguiden-for-woocommerce'), 403);
		}

		check_admin_referer(self::ACTION);

		$answer = (string) ($_POST['bfg_fallback_answer'] ?? '');
		$price = (float) str_replace(',', '.', (string) ($_POST['bfg_fallback_price'] ?? '0'));

		// The custom state has a radio of its own, and it writes nothing. Only
		// the two real answers touch the settings.
		if (in_array($answer, [self::PRICE, self::NO_SHIPPING], true)) {
			self::save($answer === self::PRICE, max(0.0, $price));
		}

		wp_safe_redirect(admin_url('admin.php?page=bring_fraktguiden_home'));
		exit;
	}

	/** Write one answer into both cases. */
	public static function save(bool $charge, float $price): void
	{
		$rate = $charge ? self::service() : self::NONE;

		update_option(self::ANSWER_OPTION, $charge ? self::PRICE : self::NO_SHIPPING);

		foreach (self::CASES as [$rate_key, $price_key]) {
			Fraktguiden_Helper::update_option($rate_key, $rate);
			Fraktguiden_Helper::update_option($price_key, $charge ? (string) $price : '0');
		}
	}

	/**
	 * The Bring service the fallback rate carries.
	 *
	 * The checkout shows the label, not the service, so this only names the
	 * service a later booking uses. The first service the shop sells fits best.
	 */
	private static function service(): string
	{
		$all = Fraktguiden_Helper::get_all_services();

		foreach ((array) Fraktguiden_Helper::get_option('services') as $service) {
			if (isset($all[$service])) {
				return (string) $service;
			}
		}

		return (string) array_key_first($all);
	}
}
