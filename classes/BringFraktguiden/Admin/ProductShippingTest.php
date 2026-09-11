<?php

namespace BringFraktguiden\Admin;

use Bring_Fraktguiden;

/**
 * The shipping test box on the product screen.
 *
 * The box sends the product it sits on. The setup page sends a sample parcel.
 * Both draw the same block and read the same answer.
 */
final class ProductShippingTest
{
	public static function init(): void
	{
		add_action('add_meta_boxes', [self::class, 'add_box']);
		add_action('admin_enqueue_scripts', [self::class, 'enqueue']);
	}

	public static function add_box(string $post_type): void
	{
		if ('product' !== $post_type) {
			return;
		}

		add_meta_box(
			'bfg-shipping-test',
			__('Test Bring shipping', 'bring-fraktguiden-for-woocommerce'),
			[self::class, 'render'],
			'product',
			'normal'
		);
	}

	public static function render(): void
	{
		$product_id = get_the_ID();

		echo '<p>' . esc_html__(
			'Ask Bring what the checkout would show for this product.',
			'bring-fraktguiden-for-woocommerce'
		) . '</p>';

		require dirname(__DIR__, 3) . '/build/templates/admin/parts/shipping-test.php';
	}

	public static function enqueue(string $hook): void
	{
		if (! in_array($hook, ['post.php', 'post-new.php'], true) || 'product' !== get_post_type()) {
			return;
		}

		wp_enqueue_style(
			'bfg-admin-css',
			plugins_url('bring-fraktguiden-for-woocommerce/build/css/admin.css'),
			[],
			Bring_Fraktguiden::VERSION
		);

		wp_enqueue_script(
			'bfg-shipping-test',
			plugins_url('bring-fraktguiden-for-woocommerce/build/js/shipping-test.js'),
			[],
			Bring_Fraktguiden::VERSION,
			true
		);
	}
}
