<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Bring_Pdf_Collection class
 */
class Bring_Pdf_Collection extends Bring_Label_Collection {

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

		// Set a path to a new file where multiple files will be merged.
		$merged_file_path = $file['file']->get_dir() . '/labels-merged.pdf';

		// Load PDF merging library.
		require FRAKTGUIDEN_PLUGIN_PATH . 'includes/pdfmerger/PDFMerger.php';

		// Initialize merging object.
		$merger = new \PDFMerger\PDFMerger();

		// Go through all the files and merge them into one.
		foreach ( $this->files as $file ) {
			$merger->addPDF( $file['file']->get_path() );
		}

		$merger->merge( 'file', $merged_file_path );

		return $merged_file_path;
	}
}
