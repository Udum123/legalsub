<?php

namespace Rtcl\Controllers\Admin;


class EmailSettings {

	public function __construct() {
		add_action( 'wp_mail_failed', array( $this, 'log_mailer_errors' ) );
	}


	function log_mailer_errors( $mailer ) {
		if ( $mailer ) {
			$fn = ABSPATH . '/wp-content/mail.log'; // say you've got a mail.log file in your server root
			$fp = fopen( $fn, 'a' );
			fputs( $fp, "Mailer Error: " . print_r( $mailer, true ) . "\n" );
			fclose( $fp );
		}
	}

	/**
	 * @param $content_type
	 *
	 * @return string
	 */
	static function set_html_mail_content_type( $content_type ) {
		return 'text/html';
	}


}