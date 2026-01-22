<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguiden\Settings\Settings;
use BringFraktguiden\Settings\SettingsRepository;
use WC_Shipping_Zones;

class GetStartedSteps
{

	public function build()
	{
		$steps = [];

		if (! class_exists('WC_Shipping_Zones')) {
			return $steps;
		}

		$zones = WC_Shipping_Zones::get_zones();

		$added = false;
		foreach ($zones as $zone) {
			foreach ($zone['shipping_methods'] as $shipping_method) {
				if (
					$shipping_method instanceof \WC_Shipping_Method_Bring
				) {
					$added = true;
					break;
				}
			}
		}

		$steps [] = new Step(
			label: __('Add shipping method', 'bring-fraktguiden-for-woocommerce'),
			description: __('Add the Bring method to your shipping zone', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=wc-settings&tab=shipping'),
			actionText: __('Configure shipping zone', 'bring-fraktguiden-for-woocommerce'),
			completed: $added,
		);

		$steps [] = new Step(
			label: __('Select shipping services', 'bring-fraktguiden-for-woocommerce'),
			description: __('Choose which Bring services to offer', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_home&sub-page=service-wizard'),
			actionText: __('Select services', 'bring-fraktguiden-for-woocommerce'),
			completed: !empty(Fraktguiden_Helper::get_option('services')),
		);

		$has_api_credentials = !empty(Fraktguiden_Helper::get_option('mybring_api_uid'))
			&& !empty(Fraktguiden_Helper::get_option('mybring_api_key'));

		$steps [] = new Step(
			label: __('API conversion', 'bring-fraktguiden-for-woocommerce'),
			description: __('Connect your Bring API credentials', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_settings'),
			actionText: __('Connect API', 'bring-fraktguiden-for-woocommerce'),
			completed: $has_api_credentials,
		);

		$fallback = false;
		$fallback_settings = [
			'fallback'
		];
		Settings::instance();
		foreach ($fallback_settings as $setting) {
			$value = false;//Settings::instance()->pro_enabled;
			if ($value) {
				$fallback = true;
				break;
			}
		}

		$steps [] = new Step(
			label: __('Set up fallback rates', 'bring-fraktguiden-for-woocommerce'),
			description: __('Configure backup shipping rates', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_fallback'),
			actionText: __('Configure fallback options', 'bring-fraktguiden-for-woocommerce'),
			completed: $fallback,
		);

		$steps [] = new Step(
			label: __('Test shipping', 'bring-fraktguiden-for-woocommerce'),
			description: __('Test with a sample product', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('edit.php?post_type=product'),
			actionText: __('Test shipping', 'bring-fraktguiden-for-woocommerce'),
			completed: false,
		);

		$pro_enabled = Fraktguiden_Helper::get_option('pro_enabled') === 'yes';
		$has_valid_license = Fraktguiden_Helper::valid_license();

		$steps [] = new Step(
			label: __('Go live', 'bring-fraktguiden-for-woocommerce'),
			description: __('Activate shipping on your store', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_settings'),
			actionText: __('Go live', 'bring-fraktguiden-for-woocommerce'),
			completed: $pro_enabled && $has_valid_license,
		);

		return $steps;

	}
}
