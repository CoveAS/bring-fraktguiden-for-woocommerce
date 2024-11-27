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
			label: __('Add shipping method to a shipping zone', 'bring-fraktguiden-for-woocommerce'),
			description: __('Configure a shipping zone and add the Bring Fraktguiden shipping method to the zone.', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url() . 'admin.php?page=wc-settings&tab=shipping',
			actionText: __('Configure shipping zones', 'bring-fraktguiden-for-woocommerce'),
			completed: $added,
		);

		$steps [] = new Step(
			label: __('Select shipping services', 'bring-fraktguiden-for-woocommerce'),
			description: __('Go to the shipping method settings and select which of the bring services you want to show on the checkout page', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_home&sub-page=service-wizard'),
			actionText: __('Select services', 'bring-fraktguiden-for-woocommerce'),
			completed: !empty(Fraktguiden_Helper::get_option('services')),
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
			label: __('API connection', 'bring-fraktguiden-for-woocommerce'),
			description: __('', 'bring-fraktguiden-for-woocommerce'),
			action: '#',
			actionText: __('Authenticate', 'bring-fraktguiden-for-woocommerce'),
			completed: false,
		);

		$steps [] = new Step(
			label: __('Set up fallback rates', 'bring-fraktguiden-for-woocommerce'),
			description: __('Sometimes the API does not return any rates. This could be caused by a myriad of reasons and to mitigate this we recommend having some fallback options available for your customers.', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_fallback'),
			actionText: __('Configure fallback options', 'bring-fraktguiden-for-woocommerce'),
			completed: $fallback,
		);

		$steps [] = new Step(
			label: __('Test shipping for a product', 'bring-fraktguiden-for-woocommerce'),
			description: __('Go to the shipping method settings and select which of the bring services you want to show on the checkout page', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('edit.php?post_type=product'),
			actionText: __('Select services', 'bring-fraktguiden-for-woocommerce'),
			completed: false,
		);

		return $steps;

	}
}
