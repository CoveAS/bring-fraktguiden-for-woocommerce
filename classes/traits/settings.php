<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Traits;

use Bring_Fraktguiden\Common\Fraktguiden_Admin_Notices;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use Bring_Fraktguiden\Common\Fraktguiden_Service;

trait Settings
{

	/**
	 * Get setting
	 *
	 * @param string $key Key.
	 * @param string|mixed $default Default.
	 *
	 * @return mixed
	 */
	public function get_setting($key, $default = '')
	{
		return array_key_exists($key, $this->settings) ? $this->settings[$key] : $default;
	}

	/**
	 * Get Price Setting
	 *
	 * @param string $key Key.
	 * @param string|mixed $default Default.
	 *
	 * @return float
	 */
	public function get_price_setting($key, $default = '')
	{
		$price = floatval($this->get_setting($key, $default));
		$price = $this->calculate_excl_vat($price);

		return $price;
	}

	/**
	 * Default settings.
	 *
	 * @return void
	 */
	public function init_form_fields(): void
	{
		$this->form_fields = [
			'services' => [
				'title' => __('Bring products', 'bring-fraktguiden-for-woocommerce'),
				'type' => 'services_table',
				'class' => 'chosen_select',
				'css' => 'width: 400px;',
				'default' => '',
				'options' => Fraktguiden_Helper::get_all_services(),
			],
		];
	}

	/**
	 * Initialize form fields
	 *
	 * @return void
	 */
	public function init_instance_form_fields()
	{
		$this->form_fields = [];
	}

	/**
	 * Display settings in HTML
	 *
	 * @return void
	 */
//	public function admin_options()
//	{
//		echo "Hi there<pre>";
//		var_dump(debug_backtrace());
//		die;
//	}


	/**
	 * Validate services table field
	 *
	 * @param string $key Key.
	 * @param string|null $value Value.
	 *
	 * @return array
	 */
	public function validate_services_table_field($key, $value = null)
	{
		return $this->service_table->validate_services_table_field($key, $value);
	}

	/**
	 * Generate services table HTML
	 *
	 * @return string
	 */
	public function generate_services_table_html()
	{
		return $this->service_table->generate_services_table_html();
	}
}
