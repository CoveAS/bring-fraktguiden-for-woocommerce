<?php

namespace BringFraktguiden\Development;

class StateSelector
{


	public static function getStateKeys(): array
	{
		return array_keys(self::getStates());
	}

	public static function getStates(): array
	{
		global $bring_develop_config;
		$api_only = [];
		foreach ($bring_develop_config as $key => $value) {
			if (!str_starts_with($key, 'mybring_')) {
				continue;
			}
			$api_only[$key] = $value;
		}
		$pro = $bring_develop_config;
		$pro['pro_enabled'] = 'yes';
		$pro['test_mode'] = 'no';

		$trial = $pro;
		$trial['pro_activated_on'] = time();

		$pro_test = $pro;
		$pro_test['test_mode'] = 'yes';

		$expired = $pro;

		$pirate = $pro;
		$pirate['pro_activated_on'] = time() + 999999999;

		return [
			'fresh' => [],
			'api'   => $api_only,
			'pro'   => $pro,
			'trial' => $trial,
			'pro_test' => $pro_test,
			'expired' => $expired,
			'pirate' => $pirate,
			'free' => $bring_develop_config,
		];
	}

	public static function setup(): void
	{
		add_action('admin_bar_menu', function ($wp_admin_bar) {
			foreach (self::getStateKeys() as $state) {
				self::button($wp_admin_bar, $state);
			}
		}, 100);
		add_action('admin_init', function () {
			global $wp_query;
			if (!isset($_GET['bring-state-select'])) {
				return;
			}
			// Redirect back to the original page
			$stateKey = filter_input(INPUT_GET, 'bring-state-select', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			if (!in_array($stateKey, self::getStateKeys())) {
				$wp_query->set_404();
				status_header(404);
				wp_die($stateKey);
			}

			$state = self::getStates()[$stateKey];
//			ray($state, get_option('woocommerce_bring_fraktguiden_services'));
//			ray(get_option('woocommerce_bring_fraktguiden_settings'));
			if ($stateKey === 'fresh') {
				delete_option('woocommerce_bring_fraktguiden_settings');
			} else {
				update_option('woocommerce_bring_fraktguiden_settings', $state);
			}

			if ($stateKey === 'pro') {
				update_option('bring_fraktguiden_pro_valid_to', time() + 999999);
			} else {
				delete_option('bring_fraktguiden_pro_valid_to');
			}

			$referer = $_SERVER['HTTP_REFERER'] ?? null;
			if ($referer) {
				wp_redirect($referer);
				die;
			}
			wp_redirect(admin_url());
			die;
		});
	}

	private static function button($wp_admin_bar, string $state): void
	{
		$wp_admin_bar->add_node(array(
			'id' => 'bring_state_' . $state,
			'title' => ucfirst($state),
			'href' => admin_url('?bring-state-select=' . $state),
			'meta' => array(
				'class' => 'bring-state-button bring-state-button--' . $state,
			)
		));
	}
}
