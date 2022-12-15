<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Labels;

/**
 * Bring_Zpl_Collection class
 */
class Bring_Zpl_Collection extends Bring_Label_Collection {

	/**
	 * Merge
	 *
	 * @return string
	 */
	public function merge() {

		$file = reset( $this->files );

		// Do not try to merge if this is a single file.
		if ( 1 === count( $this->files ) ) {
			return $file['file']->get_path();
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
		}

		// Initialize WP Filesystem.
		WP_Filesystem();
		global $wp_filesystem;

		$content = '';
		// Go through all the files and merge them into one.
		foreach ( $this->files as $file ) {
			$content .= $wp_filesystem->get_contents( $file['file']->get_path() );
		}
		// Set a path to a new file where multiple files will be merged.
		$merged_file_path = $file['file']->get_dir() . '/labels-merged.zpl';
		$wp_filesystem->put_contents( $merged_file_path, $content, FS_CHMOD_FILE );

		return $merged_file_path;
	}
}
