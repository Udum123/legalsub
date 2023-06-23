<?php

namespace Rtcl\Controllers\Admin;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclLicense;

class LicensingController {
	function __construct() {
		add_action('init',[&$this,'init_licensing']);
	} 

	public function init_licensing() {
		$licenses = apply_filters( 'rtcl_licenses', array() );
		if ( ! Functions::check_license() || empty( $licenses ) ) {
			return;
		}
		add_filter( 'rtcl_tools_settings_options', array( $this, 'tools_settings_add_licensing' ) );
		foreach ( array_reverse($licenses) as $license ) {
			$this->add_license( $license );
		}
	}

	public function tools_settings_add_licensing( $options ) {
		$license = array(
			'licensing_section' => array(
				'title' => esc_html__( 'Licensing', 'classified-listing' ),
				'type'  => 'title',
			),
		);

		return array_merge( $license, $options );
	}

	private function add_license( $license ) {
		$license = wp_parse_args(
			$license,
			array(
				'settings' => array(),
				'api_data' => array(),
			)
		);
		if ( empty( $license['plugin_file'] ) || empty( $license['api_data'] ) || empty( $license['settings'] ) ) {
			return;
		}
		new RtclLicense( $license['plugin_file'], $license['api_data'], $license['settings'] );
	}
}
