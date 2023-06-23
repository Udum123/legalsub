<?php
/**
 * Main Elementor ELFilterHooks Class
 *
 * The main class that filter the functionality.
 *
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Elementor\Hooks;

use Elementor\Controls_Manager;
use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;

/**
 * ELFilterHooks class
 */
class ELFilterHooks {
	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter('rtcl_el_search_general_fields', [__CLASS__, 'search_general_fields'], 10, 2);
	}

	/**
	 * Search functionality
	 *
	 * @param [type] $fields prev fields
	 * @param [type] $obj class object
	 *
	 * @return array
	 */
	public static function search_general_fields($fields, $obj) {
		$new_fields = [];
		if ('geo' === Functions::location_type()) {
			$new_fields[] = [
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'geo_location_range',
				'label'     => __('Radius Search', 'classified-listing'),
				'label_on'  => __('On', 'classified-listing'),
				'label_off' => __('Off', 'classified-listing'),
				'default'   => '',
				'condition' => [
					'location_field' => 'yes',
				],
			];
		}

		return $obj->insert_new_controls('location_field', $new_fields, $fields, true);
	}
}
