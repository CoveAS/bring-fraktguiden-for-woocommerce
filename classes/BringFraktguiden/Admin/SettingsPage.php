<?php

namespace BringFraktguiden\Admin;

use Automattic\WooCommerce\Admin\PageController;
use Bring_Fraktguiden;
use BringFraktguiden\Fields\Fields;
use BringFraktguiden\Settings\Settings;
use BringFraktguiden\Settings\SettingsRepository;
use BringFraktguiden\Utility\Config;

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

		add_filter('pre_update_option_bring_fraktguiden_for_woocommerce_settings', [__CLASS__, 'process_settings'], 10, 2);
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
		$submenu['bring_fraktguiden_home'][0][0] = __('Home', 'bring-fraktguiden-for-woocommerce');
	}

	public static function home_page(): void
	{
		$sub_page = $_GET['sub-page'] ?? '';
		if ($sub_page === 'service-wizard') {
			require_once dirname(__DIR__, 3) . '/templates/admin/service-wizard.php';
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
		require_once dirname(__DIR__, 3) . '/templates/admin/home.php';
	}

	public static function settings_page(): void
	{
		$fields = Fields::instance();
		$currency = get_option( 'woocommerce_currency' );
		require_once dirname(__DIR__, 3) . '/templates/admin/settings.php';
	}

	public static function booking_page(): void
	{
		$fields = Fields::instance();
		$currency = get_option( 'woocommerce_currency' );
		require_once dirname(__DIR__, 3) . '/templates/admin/booking.php';
	}

	public static function fallback_page(): void
	{
		$fields = Fields::instance();
		$currency = get_option( 'woocommerce_currency' );
		require_once dirname(__DIR__, 3) . '/templates/admin/fallback-options.php';
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

	public static function enqueue_admin_styles($hook): void
	{
		$pages = [
			'bring-fraktguiden_page_bring_fraktguiden_settings',
			'bring-fraktguiden_page_bring_fraktguiden_fallback',
			'bring-fraktguiden_page_bring_fraktguiden_booking',
			'toplevel_page_bring_fraktguiden_home',
		];
		if (! in_array($hook, $pages)) {
			return;
		}
		$dir = dirname(__DIR__, 2);
		wp_enqueue_style(
			'bring_fraktguiden_admin_css',
			plugin_dir_url($dir) . 'assets/css/bring-fraktguiden-admin-pages.css',
			[],
			Bring_Fraktguiden::VERSION
		);
		wp_enqueue_script(
			'bring-admin-js',
			plugin_dir_url($dir) . '/assets/js/bring-fraktguiden-admin.js',
			[],
			Bring_Fraktguiden::VERSION
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
			$value[$key] = $_POST[$key] ? $setting->sanitize($_POST[$key]) : '';
		}

		return $value;
	}
}