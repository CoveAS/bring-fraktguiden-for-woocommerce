<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden\Common;

use WC_Shipping_Method_Bring;
use WC_Shipping_Zones;

/**
 *  Fraktguiden_System_Info class
 */
class FraktguidenSystemInfo {

	/**
	 * Generate
	 *
	 * @return void
	 */
	public static function generate() {
		global $woocommerce, $wp_version;
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>Fraktguiden - System Info</title>
			<style>
				body {
					font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
					color: #333;
				}

				body, td {
					font-size: 13px;
					line-height: 17px;
				}

				div.main {
					width: 55%;
					margin-left: auto;
					margin-right: auto;
					text-align: center;
				}

				table {
					border-collapse: collapse;
					border: none;
				}

				td, th {
					padding: 3px 6px;
					vertical-align: top;
					border: 1px solid lightgray;
					text-align: left;
				}

				th {
					padding-top: 15px;
					padding-bottom: 15px;
				}

				table.properties td, table.properties th {
					border: 1px solid white;
				}

				table.properties td:first-child {
					background-color: lightgray;
					text-align: right;
					width: 130px;
				}

				ul {
					margin: 0;
					padding: 0 15px;
				}
				dl {
					margin: 0;
					display: grid;
					grid-template-columns: auto 1fr;

					padding: 2px 4px;
					border: 1px solid #ddd;
					background: #f9f9f9;
					margin-bottom: 0.5rem;
					border-radius: 3px;
				}
				dt {
					/*font-weight: 600;*/
					color: #999;
				}
				dd {
					margin-left: 0.5rem;
				}
				.shippping-zone__location-name {
					color: #666;
				    font-weight: 400;
				}
				.success {
					color: #090;
				}
				.error {
					color: #900;
				}
			</style>
		</head>
		<body>
		<div class="main">
			<h1>System Info</h1>
			<table>
				<?php
				$bfg_plugin_data = get_plugin_data( dirname( __DIR__ ) . '/bring-fraktguiden-for-woocommerce/bring-fraktguiden-for-woocommerce.php' );
				$bfg_options     = get_option( 'woocommerce_' . WC_Shipping_Method_Bring::ID . '_settings' );

				self::create_header( 'WordPress ' . $wp_version );
				self::create_row( 'active_plugins', self::create_active_plugins_info() );

				self::create_header( 'WooCommerce ' . $woocommerce->version );
				self::create_row( 'base_country', $woocommerce->countries->get_base_country() );
				self::create_row( 'woocommerce_dimension_unit', get_option( 'woocommerce_dimension_unit' ) );
				self::create_row( 'woocommerce_weight_unit', get_option( 'woocommerce_weight_unit' ) );
				self::create_row( 'woocommerce_currency', get_option( 'woocommerce_currency' ) );
				self::create_row( 'shipping_countries', self::create_shipping_countries( $woocommerce->countries->get_shipping_countries() ) );
				self::create_row( 'shipping_zones', self::create_shipping_zones() );
				self::create_row( 'mybring_authentication', self::create_authenication() );

				self::create_header( $bfg_plugin_data['Name'] . ' ' . $bfg_plugin_data['Version'] );
				self::generate_fraktguiden_options( $bfg_options );
				?>
			</table>
		</div>

		<?php self::generate_script(); ?>

		</body>
		</html>
		<?php
		die();
	}

	/**
	 * Create header
	 *
	 * @param  string $header_text Header text.
	 * @return void
	 */
	private static function create_header( $header_text ) {
		?>
		<thead>
			<tr>
				<th colspan="2"><?php echo esc_html( $header_text ); ?></th>
			</tr>
		</thead>
		<?php
	}


	/**
	 * Create header
	 *
	 * @param  string $header_text Header text.
	 * @return void
	 */
	private static function create_authenication() {
		$mybring_authentication = get_option( 'mybring_authentication' );
		var_dump($mybring_authentication);
		if ( empty( $mybring_authentication ) ) {
			return esc_html__( 'No authentication data', 'bring-fraktguiden-for-woocommerce' );
		}
		$format = '<span class="mybring_authentication %s">%s</span>';
		return sprintf(
			$format,
			$mybring_authentication['authenticated'] ? 'success' : 'error',
			esc_html( $mybring_authentication['message'] )
		);
	}

	/**
	 * Create row
	 *
	 * @param  string|int $key Key.
	 * @param  string|int $val Value.
	 * @return void
	 */
	private static function create_row( $key, $val ) {
		?>
		<tr>
			<td><?php echo esc_html( $key ); ?></td>
			<?php // The value may contain HTML code. ?>
			<td><?php echo $val; // phpcs:ignore ?></td>
		</tr>
		<?php
	}

	/**
	 * Generate fraktguiden options
	 *
	 * @param  array $options Options.
	 * @return void
	 */
	private static function generate_fraktguiden_options( $options ) {
		$is_pro = class_exists( 'WC_Shipping_Method_Bring_Pro' );
		self::create_row( 'pro', ( $is_pro ? 'yes' : 'no' ) );

		foreach ( $options as $key => $option ) {
			$val = $option;

			if ( 'array' === gettype( $option ) ) {
				$val = '<ul>';

				foreach ( $option as $opt ) {
					$val .= '<li>' . $opt . '</li>';
				}

				$val .= '</ul>';
			}

			if ( 'mybring_api_key' === $key ) {
				$val = '*******';
			}

			self::create_row( $key, $val );
		}

		if ( $is_pro ) {
			self::create_row( 'labels_directory', '' ); // TODO.
		}
	}

	/**
	 * Create active plugin info
	 *
	 * @return string
	 */
	private static function create_active_plugins_info() {
		$plugins_text = '<ul>';
		$plugins      = get_plugins();

		foreach ( $plugins as $key => $plugin ) {
			if ( is_plugin_active( $key ) ) {
				$plugins_text .= '<li>' . $plugin['Name'] . ' ' . $plugin['Version'] . '</li>';
			}
		}

		$plugins_text .= '</ul>';

		return $plugins_text;
	}

	/**
	 * Create shipping countries
	 *
	 * @param  array $countries Countries.
	 * @return string
	 */
	private static function create_shipping_countries( $countries ) {
		$html  = '<div class="shipping-countries">';
		$html .= '<div>';

		$i = 0;

		foreach ( $countries as $country ) {
			if ( 0 === $i ) {
				$html .= '<ul>';
			}

			if ( $i < 5 ) {
				$html .= '<li>' . $country . '</li>';
			}

			if ( 5 === $i ) {
				$html .= '</ul>';
				$html .= '<ul class="js-hidden" style="display: none">';
			}

			if ( $i > 5 ) {
				$html .= '<li>' . $country . '</li>';
			}

			if ( count( $countries ) === $i ) {
				$html .= '</ul>';
			}

			$i++;
		}

		$html .= '</div>';
		$html .= '<div><a href="#" class="js-more">More...</a></div>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Create shipping zones
	 *
	 * @return string
	 */
	private static function create_shipping_zones() {
		$html  = '<div class="shipping-zones">';

		$zones = WC_Shipping_Zones::get_zones();

		foreach ( $zones as $zone ) {
			ob_start();
			require dirname(__DIR__, 2) . '/templates/system-info/shipping-zones.php';
			$html = ob_get_clean();
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Generate script
	 *
	 * @return void
	 */
	private static function generate_script() {
		?>
		<script>
		window.addEventListener( 'load', function () {
			var more_elem = document.querySelector( '.js-more' );
			var hidden_elem = document.querySelector( '.js-hidden' );

			more_elem.addEventListener( 'click', function ( evt ) {
				hidden_elem.style.display = hidden_elem.style.display === 'none' ? '' : 'none';
				more_elem.textContent = hidden_elem.style.display === 'none' ? 'More...' : 'Less';
				evt.preventDefault();
			} );
		})
		</script>
		<?php
	}
}
