<?php

namespace BringFraktguiden\Admin;

use Automattic\WooCommerce\Admin\PageController;
use BringFraktguiden\Settings\SettingsRepository;

class SettingsPage
{
    public static function init(): void
	{
        add_action('admin_menu', [self::class, 'add_admin_menu']);
        add_action('admin_init', [self::class, 'settings_init']);
        add_action('admin_notices', [self::class, 'admin_notices']);

		add_filter( 'admin_body_class', [__CLASS__, 'add_admin_body_classes']);
//		add_filter( 'admin_title', [__CLASS__, 'update_admin_title']);
		add_action( 'admin_notices', [__CLASS__, 'inject_before_notices'], -9999 );
		add_action( 'admin_notices', [__CLASS__, 'inject_after_notices'], PHP_INT_MAX );
    }

	public static function update_admin_title( $admin_title ) {
		if (
			! did_action( 'current_screen' ) ||
			! self::is_settings_page()
		) {
			return $admin_title;
		}

		$title = 'Hello world';
		$title = $admin_title;

		/* translators: %1$s: updated title, %2$s: blog info name */
		return sprintf( __( '%1$s &lsaquo; %2$s', 'bring-fraktguiden-for-woocommerce' ), $title, get_bloginfo( 'name' ) );
	}

	public static function add_admin_body_classes( $admin_body_class = '' ) {
		if ( ! self::is_settings_page() ) {
			return $admin_body_class;
		}

		$classes   = explode( ' ', trim( $admin_body_class ) );
		$classes[] = 'bfg-admin-page';

		$admin_body_class = implode( ' ', array_unique( $classes ) );
		return " $admin_body_class ";
	}

    public static function add_admin_menu(): void
	{
        add_menu_page(
            'Bring Fraktguiden Settings',
            'Bring Fraktguiden',
            'manage_options',
            'bring_fraktguiden_settings',
            [self::class, 'settings_page'],
            'dashicons-admin-generic',
            99
        );
    }

    public static function settings_page(): void
	{
		require_once dirname(__DIR__, 3) . '/templates/admin/settings.php';
    }

	/**
	 * Returns true if the required options are set
	 *
	 * @return boolean
	 */
	public static function is_valid_for_use() {

		return $weight_unit && $dimensions_unit && $currency;
	}

    public static function settings_init(): void
	{
        register_setting('bring_fraktguiden_plugin_page', 'bring_fraktguiden_settings');

        add_settings_section(
            'bring_fraktguiden_plugin_page_section',
            __('Your section description', 'wordpress'),
            [self::class, 'settings_section_callback'],
            'bring_fraktguiden_plugin_page'
        );

        add_settings_field(
            'bring_fraktguiden_checkbox_field_0',
            __('Pro enabled', 'wordpress'),
            [self::class, 'checkbox_field_0_render'],
            'bring_fraktguiden_plugin_page',
            'bring_fraktguiden_plugin_page_section'
        );
    }

    public static function checkbox_field_0_render(): void
	{
		$options = SettingsRepository::instance('bring_fraktguiden_for_woocommerce_settings');

        $options = get_option('bring_fraktguiden_for_woocommerce_settings', []);
		$pro_enabled = $options['bring_fraktguiden_checkbox_field_0'] ?? 0;
        ?>
        <input type='checkbox' name='bring_fraktguiden_settings[bring_fraktguiden_checkbox_field_0]' <?php checked($pro_enabled, 1); ?> value='1'>
        <?php
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
		if ( ! self::is_settings_page() ) {
			return;
		}


		// Wrap the notices in a hidden div to prevent flickering before
		// they are moved elsewhere in the page by WordPress Core.
		echo '<div class="bfg__notice-list-hide">';

		if ( PageController::is_admin_page() ) {
			// Capture all notices and hide them. WordPress Core looks for
			// `.wp-header-end` and appends notices after it if found.
			// https://github.com/WordPress/WordPress/blob/f6a37e7d39e2534d05b9e542045174498edfe536/wp-admin/js/common.js#L737 .
			echo '<div class="wp-header-end" id="bfg__notice-catcher"></div>';
		}
	}

	/**
	 * Runs after admin notices and closes div.
	 */
	public static function inject_after_notices(): void
	{
		if ( ! self::is_settings_page() ) {
			return;
		}

		// Close the hidden div used to prevent notices from flickering before
		// they are inserted elsewhere in the page.
		echo '</div>';
	}

	private static function is_settings_page(): bool
	{
		return isset( $_GET['page'] ) && 'bring_fraktguiden_settings' === $_GET['page'];
	}
}
