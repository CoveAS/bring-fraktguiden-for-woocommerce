<?php

namespace BringFraktguiden\Admin;

use Automattic\WooCommerce\Admin\PageController;
use Bring_Fraktguiden;
use BringFraktguiden\Fields\Fields;
use BringFraktguiden\Settings\Settings;
use BringFraktguiden\Settings\SettingsRepository;
use BringFraktguiden\Admin\GetStartedSteps;
use BringFraktguiden\Utility\Config;
use Bring_Fraktguiden\Common\Fraktguiden_Helper;

class SettingsPage
{
	public static function init(): void
	{
		add_action('admin_menu', [self::class, 'add_admin_menu']);
		add_action('admin_init', [self::class, 'settings_init']);
		add_action('admin_notices', [self::class, 'admin_notices']);

		add_filter('admin_body_class', [__CLASS__, 'add_admin_body_classes']);
		//		add_filter( 'admin_title', [__CLASS__, 'update_admin_title']);
		add_action('admin_notices', [__CLASS__, 'inject_before_notices'], -9999);
		add_action('admin_notices', [__CLASS__, 'inject_after_notices'], PHP_INT_MAX);

		add_action('admin_enqueue_scripts', __CLASS__ . '::enqueue_admin_styles');
		add_filter('admin_head', __CLASS__ . '::admin_head');

		add_filter('pre_update_option_bring_fraktguiden_for_woocommerce_settings', [__CLASS__, 'process_settings'], 10, 2);
	}

	/**
	 * Run build script in local environment before requiring templates
	 */
	private static function maybe_build(): void
	{
		// Only run in local environment
		if (!defined('BRING_ENVIRONMENT') || BRING_ENVIRONMENT !== 'local') {
			return;
		}

		$plugin_dir = dirname(__DIR__, 3);
		$compile_script = $plugin_dir . '/bin/compile-templates.php';

		if (!file_exists($compile_script)) {
			return;
		}

		// Require the compiler dependencies
		require_once $compile_script;

		// Use BatchCompiler to compile all templates
		$batchCompiler = new \BFG_BatchCompiler($plugin_dir);
		$result = $batchCompiler->compileAll();

		// Show error notice if any compilations failed
		if (!$result->hasErrors()) {
			return;
		}
		$message = sprintf(
			__('BFG Template compilation failed (%d files):', 'bring-fraktguiden-for-woocommerce'),
			$result->failed
		);
		$message .= '<br>' . implode('<br>', $result->getErrorMessages());

		wp_die($message);
	}

	public static function update_admin_title($admin_title)
	{
		if (
			!did_action('current_screen') ||
			!self::is_settings_page()
		) {
			return $admin_title;
		}

		$title = 'Hello world';
		$title = $admin_title;

		/* translators: %1$s: updated title, %2$s: blog info name */
		return sprintf(__('%1$s &lsaquo; %2$s', 'bring-fraktguiden-for-woocommerce'), $title, get_bloginfo('name'));
	}

	public static function add_admin_body_classes($admin_body_class = '')
	{
		if (!self::is_settings_page()) {
			return $admin_body_class;
		}

		$classes = explode(' ', trim($admin_body_class));
		$classes[] = 'bfg-admin-page';
		if (isset($_GET['page'])) {
			$classes[] = 'bfg-admin-page--' . str_replace('_', '-', $_GET['page']);
		}

		$admin_body_class = implode(' ', array_unique($classes));
		return " $admin_body_class ";
	}

	public static function add_admin_menu(): void
	{
		global $submenu;
		add_menu_page(
			__('Bring Fraktguiden Settings', 'bring-fraktguiden-for-woocommerce'),
			'Bring Fraktguiden',
			'manage_options',
			'bring_fraktguiden_home',
			[self::class, 'home_page'],
			'dashicons-admin-generic',
			99
		);
		add_submenu_page(
			'bring_fraktguiden_home',
			__('Booking', 'bring-fraktguiden-for-woocommerce'),
			__('Booking', 'bring-fraktguiden-for-woocommerce'),
			'manage_options',
			'bring_fraktguiden_booking',
			[self::class, 'booking_page']
		);
		add_submenu_page(
			'bring_fraktguiden_home',
			__('Fallback options', 'bring-fraktguiden-for-woocommerce'),
			__('Fallback options', 'bring-fraktguiden-for-woocommerce'),
			'manage_options',
			'bring_fraktguiden_fallback',
			[self::class, 'fallback_page']
		);
		add_submenu_page(
			'bring_fraktguiden_home',
			__('Settings', 'bring-fraktguiden-for-woocommerce'),
			__('Settings', 'bring-fraktguiden-for-woocommerce'),
			'manage_options',
			'bring_fraktguiden_settings',
			[self::class, 'settings_page']
		);
		if (defined('BRING_ENVIRONMENT') && BRING_ENVIRONMENT === 'local') {
			add_submenu_page(
				'bring_fraktguiden_home',
				__('Kitchen Sink', 'bring-fraktguiden-for-woocommerce'),
				__('Kitchen Sink', 'bring-fraktguiden-for-woocommerce'),
				'manage_options',
				'bring_fraktguiden_kitchen_sink',
				[self::class, 'kitchen_sink_page']
			);
		}
		$submenu['bring_fraktguiden_home'][0][0] = __('Home', 'bring-fraktguiden-for-woocommerce');
	}

	public static function home_page(): void
	{
		self::maybe_build();

		$sub_page = $_GET['sub-page'] ?? '';
		if ($sub_page === 'service-wizard') {
			$country_code = WC()->countries?->get_base_country();
			$country = WC()->countries?->countries[$country_code] ?? null;
			$settings_url = Fraktguiden_Helper::get_settings_url();
			require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/service-wizard.php';
			return;
		}

		$steps = (new GetStartedSteps)->build();
		$stepCount = count($steps);
		$stepsCompleted = array_reduce($steps, fn($carry, $step) => $carry + ($step->completed ? 1 : 0), 0);
		$nextStep = null;
		/** @var Step $step */
		foreach ($steps as $step) {
			if (!$step->completed) {
				$nextStep = $step;
				break;
			}
		}
		require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/home.php';
	}

	public static function settings_page(): void
	{
		self::maybe_build();

		$fields = Fields::instance();
		$currency = get_option('woocommerce_currency');
		require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/settings.php';
	}

	public static function booking_page(): void
	{
		self::maybe_build();

		$fields = Fields::instance();
		$currency = get_option('woocommerce_currency');

		// Country select data
		$countries = WC()->countries?->get_countries() ?: [];
		$base_country = WC()->countries?->get_base_country() ?: '';

		// Order status after booking
		$order_statuses = wc_get_order_statuses();
		$saved_booking_status = Fraktguiden_Helper::get_option('auto_set_status_after_booking_success');
		$booking_status_value = !empty($saved_booking_status) ? $saved_booking_status : 'wc-bring-shipment';
		$booking_status_options = array_merge(
			['none' => __('None', 'bring-fraktguiden-for-woocommerce')],
			$order_statuses
		);

		// Order status after printing
		$saved_print_status = Fraktguiden_Helper::get_option('auto_set_status_after_print_label_success');
		$print_status_value = !empty($saved_print_status) ? $saved_print_status : 'none';
		$print_status_options = array_merge(
			['none' => __('None', 'bring-fraktguiden-for-woocommerce')],
			$order_statuses
		);

		// Package type options
		$saved_package_type = Fraktguiden_Helper::get_option('booking_home_delivery_package_type');
		$package_type_value = !empty($saved_package_type) ? $saved_package_type : 'hd_eur';
		$package_type_options = [
			'hd_eur' => 'HD_EUR_PALLET',
			'hd_half' => 'HD_HALF_PALLET',
			'hd_quarter' => 'HD_QUARTER_PALLET',
			'hd_loose' => 'HD_SPECIAL_PALLET',
		];

		require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/booking.php';
	}

	public static function fallback_page(): void
	{
		self::maybe_build();

		$fields = Fields::instance();
		$currency = get_option('woocommerce_currency');
		require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/fallback-options.php';
	}

	public static function kitchen_sink_page(): void
	{
		self::maybe_build();

		require_once dirname(__DIR__, 3) . '/build/templates/admin/pages/kitchen-sink.php';
	}

	public static function settings_init(): void
	{
		$admin_settings = Config::get('admin-settings');

		foreach ($admin_settings as $section_key => $section) {
			register_setting(
				'bring_fraktguiden_' . $section_key,
				'bring_fraktguiden_for_woocommerce_settings'
			);
			add_settings_section(
				'bring_fraktguiden_' . $section_key,
				$section['title'] ?? 'No title',
				SectionRenderer::class . '::' . $section_key,
				'bring_fraktguiden_' . $section_key
			);
		}

	}


	public static function settings_section_callback(): void
	{
		echo __('This is a section where you can enable or disable Pro features.', 'wordpress');
	}

	public static function admin_notices(): void
	{
		settings_errors('bring_fraktguiden_messages');
	}

	/**
	 * Runs before admin notices action and hides them.
	 */
	public static function inject_before_notices(): void
	{
		if (!self::is_settings_page()) {
			return;
		}

		// Wrap the notices in a hidden div to prevent flickering before
		// they are moved elsewhere in the page by WordPress Core.
		echo '<div class="bfg__notice-list-hide">';
	}

	/**
	 * Runs after admin notices and closes div.
	 */
	public static function inject_after_notices(): void
	{
		if (!self::is_settings_page()) {
			return;
		}

		// Close the hidden div used to prevent notices from flickering before
		// they are inserted elsewhere in the page.
		echo '</div>';
	}

	private static function is_settings_page(): bool
	{
		return isset($_GET['page']) && str_starts_with($_GET['page'], 'bring_fraktguiden_');
	}

	public static function admin_head(): void
	{
		global $hook_suffix;
		$pages = [
			'bring-fraktguiden_page_bring_fraktguiden_settings',
			'bring-fraktguiden_page_bring_fraktguiden_fallback',
			'bring-fraktguiden_page_bring_fraktguiden_booking',
			'bring-fraktguiden_page_bring_fraktguiden_kitchen_sink',
			'toplevel_page_bring_fraktguiden_home',
		];

		if (!in_array($hook_suffix, $pages)) {
			return;
		}
		$skin = get_user_option('admin_color');

		echo Component::styles($skin);
	}

	public static function enqueue_admin_styles($hook): void
	{
		$pages = [
			'bring-fraktguiden_page_bring_fraktguiden_settings',
			'bring-fraktguiden_page_bring_fraktguiden_fallback',
			'bring-fraktguiden_page_bring_fraktguiden_booking',
			'bring-fraktguiden_page_bring_fraktguiden_kitchen_sink',
			'toplevel_page_bring_fraktguiden_home',
		];
		if (!in_array($hook, $pages)) {
			return;
		}

		// Consolidated admin styles (includes admin-pages.css, admin-home.css, admin.css)
		wp_enqueue_style(
			'bring_fraktguiden_compiled_styles',
			plugins_url('bring-fraktguiden-for-woocommerce/assets/css/compiled-styles.css'),
			[],
			Bring_Fraktguiden::VERSION . '.' . time()
		);

		wp_enqueue_script(
			'bring-admin-js',
			plugins_url('bring-fraktguiden-for-woocommerce/assets/js/bring-fraktguiden-admin.js'),
			['jquery'],
			Bring_Fraktguiden::VERSION,
			true
		);
	}

	public static function process_settings($value, $old_value): array
	{
		$value = isset($old_value) && is_array($old_value) ? $old_value : [];

		// Get the current page
		$page = 'settings';
		if (preg_match('/^bring_fraktguiden_(.*)$/', $_POST['option_page'] ?? '', $matches)) {
			$page = $matches[1];
		}

		// Get the page settings
		$admin_settings = Config::get('admin-settings');
		$pageFieldKeys = array_keys($admin_settings[$page]['fields']);

		$settings = Settings::instance();
		foreach ($pageFieldKeys as $key) {
			$setting = $settings->get($key);
			if ('info' == $setting->type) {
				continue;
			}
			$sanitized = $_POST[$key] ? $setting->sanitize($_POST[$key]) : '';
			if ($setting->type === 'checkbox') {
				$sanitized = $sanitized ? 'yes' : 'no';
			}
			$value[$key] = $sanitized;
		}

		// Handle trial activation date
		if (
			isset($value['pro_enabled']) && $value['pro_enabled'] === 'yes'
			&& empty($value['pro_activated_on'])
		) {
			$value['pro_activated_on'] = time();
		}

		return $value;
	}
}
