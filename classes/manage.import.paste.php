<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MailsterImportPaste extends MailsterImport {

	protected $slug = 'paste';
	protected $name = 'Paste';

	private $api;

	function init() {}


	public function get_import_part( $import_data ) {

		$raw_data = file_get_contents( $import_data['file'] );
		$data     = maybe_unserialize( $raw_data );
		$limit    = $import_data['performance'] ? 10 : 100;
		$offset   = $import_data['part'] * $limit;

		return array_slice( $data, $offset, $limit );

	}

	public function get_import_data() {

		$raw_data    = esc_textarea( stripslashes( $_POST['data'] ) );
		$header      = null;
		$sample_size = 10;

		$encoding = mb_detect_encoding( $raw_data, 'auto' );

		// single quotes cause problems
		$raw_data = str_replace( '&#039;', "'", $raw_data );
		$raw_data = str_replace( '&quot;', '"', $raw_data );

		$raw_data = trim( $raw_data );

		$total_lines = substr_count( $raw_data, "\n" ) + 1;
		$data        = $this->sanitize_raw_data( $raw_data );

		error_log( print_r( $data, true ) );
		if ( isset( $data['header'] ) ) {
			$total_lines--;
			$header = array_shift( $data );
		}
		$total   = $total_batch = count( $data );
		$removed = $total_lines - $total;

		$filename = wp_tempnam();
		mailster( 'helper' )->file_put_contents( $filename, serialize( $data ) );

		$sample = array_splice( $data, 0, $sample_size );

		return array(
			'file'        => $filename,
			'total'       => $total,
			'removed'     => $removed,
			'header'      => $header,
			'sample'      => $sample,
			'sample_last' => end( $data ),
			'encoding'    => $encoding,
			'insert'      => array(
				'referer' => 'import',
			),
		);

	}


	public function import_options( $data = null ) {
		include MAILSTER_DIR . '/views/manage/method-paste.php';
	}



}
