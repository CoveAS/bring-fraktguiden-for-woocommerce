<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

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

		// Initialize WP Filesystem.
		WP_Filesystem();
		global $wp_filesystem;

		$file      = reset( $this->files );
		$file_path = $file['file']->get_path();

		// Do not merge if this is a single file.
		if ( 1 === count( $this->files ) ) {
			return $file_path;
		}

		// Set a path to a new file where multiple files will be merged.
		$merged_file_path = $file['file']->get_dir() . '/labels-merged.zpl';

		// Go through all the files and merge them into one.
		foreach ( $this->files as $file ) {
			$fh = $wp_filesystem->get_contents( $file_path );
			$wp_filesystem->put_contents( $merged_file_path, $fh, FS_CHMOD_FILE );
		}

		return $merged_file_path;
	}
}
