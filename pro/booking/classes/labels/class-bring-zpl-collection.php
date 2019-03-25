<?php

class Bring_Zpl_Collection extends Bring_Label_Collection {
	public function merge() {
		$file = reset( $this->files );
		$merge_file = $file['file']->get_path();
		if ( 1 !== count( $this->files ) ) {
			$merge_file = $file['file']->get_dir() . '/labels-merged.zpl';
			$merge_fh   = fopen( $merge_file, 'w' );
			foreach ( $this->files as $file ) {
				$fh = fopen( $file['file']->get_path(), 'r' );
				while( ( $line = fgets( $fh ) ) !== false ) {
					fputs( $merge_fh, $line );
				}
				fclose( $fh );
			}
			fclose( $merge_fh );
		}
		return $merge_file;
	}
}
