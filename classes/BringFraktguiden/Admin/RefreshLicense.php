<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_License;
use Exception;

/**
 * The refresh button of the license status box on the Pro page.
 *
 * The plugin checks the license on its own schedule. The owner presses the
 * button to learn at once that a purchase, a renewal or a move went through.
 */
class RefreshLicense
{
	public const ACTION = 'bfg_refresh_license';

	/** The query argument that carries the result back to the Pro page. */
	public const RESULT = 'bfg-license-checked';

	/** The action the move modal polls while it waits for a move. */
	public const POLL = 'bfg_poll_license';

	public static function init(): void
	{
		add_action('admin_post_' . self::ACTION, [self::class, 'handle']);
		add_action('wp_ajax_' . self::POLL, [self::class, 'poll']);
	}

	public static function handle(): void
	{
		if (! current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('You may not change the Bring settings.', 'bring-fraktguiden-for-woocommerce'), 403);
		}

		check_admin_referer(self::ACTION);

		try {
			Fraktguiden_License::get_instance()->check_license();
			$result = 'yes';
		} catch (Exception $e) {
			$result = 'no';
		}

		wp_safe_redirect(add_query_arg(
			self::RESULT,
			$result,
			admin_url('admin.php?page=bring_fraktguiden_pro')
		));
		exit;
	}

	/**
	 * Check the license and report the state as JSON.
	 *
	 * The move modal calls this while the owner confirms the move on the web
	 * site. The page reloads once the state leaves 'other_domain'.
	 */
	public static function poll(): void
	{
		if (! current_user_can('manage_woocommerce')) {
			wp_send_json_error(['message' => 'forbidden'], 403);
		}

		check_ajax_referer(self::POLL);

		try {
			Fraktguiden_License::get_instance()->check_license();
			$reached = true;
		} catch (Exception $e) {
			$reached = false;
		}

		wp_send_json_success([
			'reached'   => $reached,
			'key_state' => Fraktguiden_License::get_state()['key_state'] ?? '',
		]);
	}
}
