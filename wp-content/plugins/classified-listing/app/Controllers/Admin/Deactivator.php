<?php

namespace Rtcl\Controllers\Admin;


class Deactivator {

	public function __construct( ){
		register_deactivation_hook( RTCL_ACTIVE_FILE_NAME, array($this, 'deactivate') );
	}

	public static function deactivate() {

		delete_option( 'rewrite_rules' );

		// Un-schedules all previously-scheduled cron jobs
		wp_clear_scheduled_hook('rtcl_hourly_scheduled_events');

	}

}