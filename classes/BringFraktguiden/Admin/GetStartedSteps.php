<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

class GetStartedSteps
{

	public function build()
	{
		$steps = [];

		if (! class_exists('WC_Shipping_Zones')) {
			return $steps;
		}

		$steps [] = new Step(
			label: __('Add shipping method', 'bring-fraktguiden-for-woocommerce'),
			description: __('Add the Bring method to your shipping zone', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=wc-settings&tab=shipping'),
			actionText: __('Add to shipping zones', 'bring-fraktguiden-for-woocommerce'),
			completed: ShippingZones::any_added(),
			dialog: 'bfg-add-shipping-method',
		);

		$steps [] = new Step(
			label: __('Select shipping services', 'bring-fraktguiden-for-woocommerce'),
			description: __('Choose which Bring services to offer', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_home&sub-page=service-wizard'),
			actionText: __('Select services', 'bring-fraktguiden-for-woocommerce'),
			completed: !empty(Fraktguiden_Helper::get_option('services')),
		);

		$steps [] = new Step(
			label: __('Connect your Bring account', 'bring-fraktguiden-for-woocommerce'),
			description: __('Add your Bring login email and API key', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_settings'),
			actionText: __('Connect account', 'bring-fraktguiden-for-woocommerce'),
			completed: ConnectAccount::connected(),
			form: 'connect-account',
		);

		$steps [] = new Step(
			label: __('Price the carts that fall through', 'bring-fraktguiden-for-woocommerce'),
			description: __('Set a price for carts that are too big, too heavy or too full, or when the Bring API is quiet', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('admin.php?page=bring_fraktguiden_fallback'),
			actionText: __('Answer', 'bring-fraktguiden-for-woocommerce'),
			completed: FallbackPrice::current()->decided(),
			form: 'fallback-price',
		);

		$steps [] = new Step(
			label: __('Test shipping', 'bring-fraktguiden-for-woocommerce'),
			description: __('Ask Bring for the price of a sample parcel', 'bring-fraktguiden-for-woocommerce'),
			action: admin_url('edit.php?post_type=product'),
			actionText: __('Test shipping', 'bring-fraktguiden-for-woocommerce'),
			completed: ShippingTest::passed(),
			form: 'test-shipping',
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
