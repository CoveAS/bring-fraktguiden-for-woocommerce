<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Views;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Bring_Booking;
use BringFraktguidenPro\Booking\Labels\Bring_Pdf_Collection;
use BringFraktguidenPro\Booking\Labels\Bring_Zpl_Collection;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Labels class
 */
class Bring_Booking_Labels {

	/**
	 * Create download URL
	 */
	public static function create_download_url( array|string $order_ids ):string {
		if ( is_array( $order_ids ) ) {
			$order_ids = implode( ',', $order_ids );
		}

		return admin_url( 'admin.php?page=bring_download&order_ids=' . $order_ids );
	}

	/**
	 * Open PDF's
	 *
	 * @return void
	 */
	public static function open_pdfs() {
		$page = filter_input( INPUT_GET, 'page' );
		if ( is_admin() && 'bring_book_orders' === $page ) {
			$order_ids = explode(
				',',
				filter_input( INPUT_GET, 'order_ids' )
			);
			foreach ($order_ids as $order_id) {
				$order = wc_get_order($order_id);
				$adapter = new Bring_WC_Order_Adapter($order);
				if ($adapter->is_booked()) {
					continue;
				}
				Bring_Booking::send_booking( $adapter, true );
			}

			static::download_page();
		}
		if ( is_admin() && 'bring_download' === $page ) {
			static::download_page();
		}
	}

	/**
	 * Check if current user role can
	 * access Bring labels
	 */
	public static function check_cap() {
		$current_user = wp_get_current_user();

		// ID 0 is a not an user.
		if ( 0 === $current_user->ID ) {
			return false;
		}

		$required_caps = apply_filters(
			'bring_booking_capabilities',
			[
				'administrator',
				'manage_woocommerce',
				'warehouse_team',
				'bring_labels',
			]
		);

		// Check user against required roles/caps.
		foreach ( $required_caps as $cap ) {
			if ( user_can( $current_user->ID, $cap ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Download page
	 */
	public static function download_page() {
		// Check if user can see the labels.
		if ( ! self::check_cap() ) {
			wp_die(
				sprintf(
					'<div class="notice error"><p><strong>%s</strong></p></div>',
					esc_html( __( 'Sorry, Labels are only available for Administrators, Warehouse Teams and Store Managers. Please contact the administrator to enable access.', 'bring-fraktguiden-for-woocommerce' ) )
				),
				esc_html( __( 'Insufficient permissions', 'bring-fraktguiden-for-woocommerce' ) )
			);
		}

		$order_ids = filter_input( INPUT_GET, 'order_ids' );

		$printed_orders = [];

		if ( empty( $order_ids ) ) {
			esc_html_e( 'Order ID is missing.', 'bring-fraktguiden-for-woocommerce' );

			return;
		}

		// Require classes.
		require_once dirname( __DIR__ ) . '/labels/class-bring-label-collection.php';
		require_once dirname( __DIR__ ) . '/labels/class-bring-pdf-collection.php';
		require_once dirname( __DIR__ ) . '/labels/class-bring-zpl-collection.php';

		$order_ids = explode( ',', $order_ids );

		$zpl_collection = new Bring_Zpl_Collection();
		$pdf_collection = new Bring_Pdf_Collection();

		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				continue;
			}

			$printed_orders[] = $order_id;

			$adapter = new Bring_WC_Order_Adapter( $order );

			// Get the booking consignments from the adapter.
			$consignments = $adapter->get_booking_consignments();

			foreach ( $consignments as $consignment ) {
				// Get the label file.
				$file = $consignment->get_label_file();

				// Try to download the file if it doesn't exist.
				if ( ! $file->exists() && ! $file->download() ) {
					continue;
				}

				if ( 'zpl' === $file->get_ext() ) {
					$zpl_collection->add( $order_id, $file );
				} else {
					$pdf_collection->add( $order_id, $file );
				}
			}
		}

		Fraktguiden_Helper::update_option( 'printed_orders', $printed_orders );

		if ( $pdf_collection->is_empty() && $zpl_collection->is_empty() ) {
			esc_html_e( 'No files to download.', 'bring-fraktguiden-for-woocommerce' );

			return;
		}

		// If there are more than 1 ZPL file or a combination of zpl and pdf.
		if ( ! $pdf_collection->is_empty() && ! $zpl_collection->is_empty() ) {
			echo '<h3>' . esc_html( __( 'Downloads', 'bring-fraktguiden-for-woocommerce' ) ) . '</h3><ul><li>';
			self::render_download_link( $zpl_collection->get_order_ids(), __( 'Merged ZPL labels', 'bring-fraktguiden-for-woocommerce' ) );
			echo '</li><li>';
			self::render_download_link( $pdf_collection->get_order_ids(), __( 'Merged PDF labels', 'bring-fraktguiden-for-woocommerce' ) );
			echo '</li></ul>';
		} elseif ( ! $pdf_collection->is_empty() ) {
			$merge_file = $pdf_collection->merge();
			static::render_file_content( $merge_file );
		} elseif ( ! $zpl_collection->is_empty() ) {
			$merge_file = $zpl_collection->merge();
			static::render_file_content( $merge_file );
		}
	}

	/**
	 * Render file content
	 *
	 * @param string $file File.
	 */
	public static function render_file_content( $file ) {
		$filename = $file;

		if ( '.' === substr( $filename, - 1 ) ) {
			$filename .= 'pdf';
		}

//		header( 'Content-Length: ' . filesize( $file ) );

		$content_type = 'Content-type: application/octet-stream';

		if ( preg_match( '/\.pdf$/', $filename ) ) {
			$content_type = 'Content-type: application/pdf';
		}

		header( $content_type );
		header( 'Content-disposition: inline; filename=' . basename( $filename ) );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );

		// Workaround for Chrome's inline pdf viewer.
		@ob_clean();
		flush();

		readfile( $file );
		die;
	}

	/**
	 * Render download link
	 *
	 * @param array  $order_ids Order IDs.
	 * @param string $name      Name.
	 */
	public static function render_download_link( array $order_ids, string $name ): void
	{
		printf(
			'<li><a href="%s" target="_blank">%s</a></li>',
			esc_attr( static::create_download_url( $order_ids ) ),
			esc_html( $name )
		);
	}
}
