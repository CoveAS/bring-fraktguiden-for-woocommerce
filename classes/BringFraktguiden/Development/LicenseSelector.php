<?php

namespace BringFraktguiden\Development;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_License;

/**
 * An admin bar menu that puts the shop into one license state.
 *
 * Each state holds the answer the license server would send. The answer runs
 * through Fraktguiden_License::store_answer(), so the real parse decides what
 * the options hold.
 */
class LicenseSelector
{
	/**
	 * The license states, as server answers.
	 *
	 * A state with no valid_to leaves the shop without Pro.
	 *
	 * @return array<string,array{title:string,source:string,license:array}>
	 */
	public static function states(): array
	{
		$domain = wp_parse_url(get_site_url(), PHP_URL_HOST) ?: 'example.test';
		$year   = (int) gmdate('Y');
		$move   = 'https://bringfraktguiden.no/move-license/';

		return [
			'active-domain' => [
				'title'   => 'Active, by domain',
				'source'  => 'domain',
				'license' => [
					'key_state'  => 'ok',
					'key'        => 'BFG1TEST2DOMA1N3',
					'domain'     => $domain,
					'moves_left' => 2,
					'year'       => $year,
					'valid_to'   => time() + (365 * 86400),
				],
			],
			'active-key' => [
				'title'   => 'Active, by key',
				'source'  => 'key',
				'license' => [
					'key_state'  => 'ok',
					'key'        => 'BFG1TEST2KEY34567',
					'domain'     => $domain,
					'moves_left' => 1,
					'year'       => $year,
					'valid_to'   => time() + (30 * 86400),
				],
			],
			'other-domain' => [
				'title'   => 'Held by another domain',
				'source'  => 'key',
				'license' => [
					'key_state'    => 'other_domain',
					'key'          => 'BFG10THER2D0MA1N',
					'other_domain' => 'someone-else.test',
					'moves_left'   => 2,
					'year'         => $year,
					'move_url'     => $move,
				],
			],
			'other-domain-stuck' => [
				'title'   => 'Held by another domain, no moves left',
				'source'  => 'key',
				'license' => [
					'key_state'    => 'other_domain',
					'key'          => 'BFG10THER2D0MA1N',
					'other_domain' => 'someone-else.test',
					'moves_left'   => 0,
					'year'         => $year,
					'move_url'     => $move,
				],
			],
			'unknown' => [
				'title'   => 'Unknown key',
				'source'  => 'key',
				'license' => [
					'key_state' => 'unknown',
					'key'       => 'BFGN0SUCHKEY23456',
					'reason'    => 'No license row holds this key.',
					'year'      => $year,
				],
			],
			'expired' => [
				'title'   => 'Expired',
				'source'  => 'key',
				'license' => [
					'key_state'  => 'ok',
					'key'        => 'BFG1EXP1RED234567',
					'domain'     => $domain,
					'moves_left' => 2,
					'year'       => $year,
					'valid_to'   => time() - 86400,
				],
			],
			'none' => [
				'title'   => 'No license at all',
				'source'  => '',
				'license' => [],
			],
		];
	}

	public static function setup(): void
	{
		add_action('admin_bar_menu', [self::class, 'menu'], 100);
		add_action('admin_init', [self::class, 'handle']);
	}

	public static function menu($wp_admin_bar): void
	{
		$wp_admin_bar->add_node(
			[
				'id'    => 'bring_license',
				'title' => 'Select license',
			]
		);

		foreach (self::states() as $key => $state) {
			$wp_admin_bar->add_node(
				[
					'id'     => 'bring_license_'.$key,
					'title'  => $state['title'],
					'parent' => 'bring_license',
					'href'   => wp_nonce_url(admin_url('?bring-license-select='.$key), 'bring-license-select'),
				]
			);
		}
	}

	public static function handle(): void
	{
		$key = filter_input(INPUT_GET, 'bring-license-select', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		if (!$key) {
			return;
		}

		check_admin_referer('bring-license-select');

		$states = self::states();

		if (!isset($states[$key])) {
			wp_die('Unknown license state: '.esc_html($key));
		}

		self::apply($states[$key]);

		wp_safe_redirect($_SERVER['HTTP_REFERER'] ?? admin_url());
		die;
	}

	/**
	 * Put the shop into one license state.
	 *
	 * store_answer() writes bring_fraktguiden_pro_valid_to only when the answer
	 * carries a valid_to. So this clears the option first, and a state without
	 * a valid_to leaves the shop without Pro.
	 *
	 * The state without a license also clears the trial start date. The shop
	 * then shows the free state, not the trial state.
	 *
	 * @param array $state One entry of self::states().
	 */
	protected static function apply(array $state): void
	{
		delete_option('bring_fraktguiden_pro_valid_to');
		Fraktguiden_Helper::update_option('license_key', '');
		Fraktguiden_Helper::update_option('pro_enabled', 'yes');

		if (!$state['license']) {
			delete_option(Fraktguiden_License::STATE_OPTION);
			Fraktguiden_Helper::update_option('pro_activated_on', '');
			return;
		}

		Fraktguiden_License::get_instance()->store_answer(
			['data' => ['license' => $state['license']]],
			$state['source']
		);
	}
}
