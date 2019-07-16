<?php

class Bring_Pdf_Collection extends Bring_Label_Collection {
	public function merge() {
		$file = reset( $this->files );
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
