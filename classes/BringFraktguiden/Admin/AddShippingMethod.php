<?php

namespace BringFraktguiden\Admin;

/**
 * The form of the setup page that adds the Bring method to shipping zones.
 */
class AddShippingMethod
{
	public const ACTION = 'bfg_add_shipping_method';

	/** The query argument that carries the count back to the setup page. */
	public const ADDED = 'bfg-zones-added';

	public static function init(): void
	{
		add_action('admin_post_' . self::ACTION, [self::class, 'handle']);
	}

	public static function handle(): void
	{
		if (! current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('You may not change the shipping zones.', 'bring-fraktguiden-for-woocommerce'), 403);
		}

		check_admin_referer(self::ACTION);

		$zones = array_map('intval', (array) ($_POST['zones'] ?? []));

		$added = 0;
		foreach ($zones as $zone) {
			$added += ShippingZones::add($zone) ? 1 : 0;
		}

		wp_safe_redirect(add_query_arg(self::ADDED, $added, self::page_url()));
		exit;
	}

	public static function page_url(): string
	{
		return admin_url('admin.php?page=bring_fraktguiden_home');
	}
}
