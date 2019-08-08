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
		$file       = reset( $this->files );
		$merge_file = $file['file']->get_path();

		if ( 1 !== count( $this->files ) ) {
			require_once FRAKTGUIDEN_PLUGIN_PATH . '/includes/pdfmerger/PDFMerger.php';

			$merge_file = $file['file']->get_dir() . '/labels-merged.pdf';
			$merger     = new PDFMerger();

			foreach ( $this->files as $file ) {
				$merger->addPDF( $file['file']->get_path() );
			}

			$merger->merge( 'file', $merge_file );
		}

		return $merge_file;
	}
}
