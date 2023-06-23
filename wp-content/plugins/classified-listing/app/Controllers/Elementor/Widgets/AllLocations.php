<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Main Elementor ListingCategoryBox Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Elementor\Widgets;

use Rtcl\Controllers\Elementor\WidgetSettings\AllLocationsSettings;
use Rtcl\Helpers\Functions;

/**
 * Elementor AllLocations Widget.
 *
 * Elementor widget.
 *
 * @since 1.0.0
 */
class AllLocations extends AllLocationsSettings {

	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->rtcl_name     = __( 'All Locations', 'classified-listing' );
		$this->rtcl_base     = 'rtcl-all-locations';
		$this->rtcl_category = 'rtcl-elementor-widgets'; // Category /@dev.
		$this->rtcl_icon     = 'rtcl-el-custom';
		parent::__construct( $data, $args );
	}

	/**
	 * Widget result.
	 *
	 * @param [array] $data array of query.
	 * @return array
	 */
	public function widget_results( $data ) {
		$term_args = array(
			'taxonomy'   => 'rtcl_location',
			'orderby'    => ! empty( $data['rtcl_orderby'] ) ? $data['rtcl_orderby'] : 'date',
			'order'      => ! empty( $data['rtcl_order'] ) ? $data['rtcl_order'] : 'desc',
			'hide_empty' => ! empty( $data['rtcl_hide_empty'] ) ? 1 : 0,
		);

		if ( 'custom' === $data['rtcl_orderby'] ) {
			$term_args['orderby']  = 'meta_value_num';
			$term_args['meta_key'] = '_rtcl_order';
		}

		if ( 'selected' == $data['rtcl_location_display_rule'] && ! empty( $data['rtcl_location'] ) ) {
			$term_args['include'] = ! empty( $data['rtcl_location'] ) ? $data['rtcl_location'] : array();
		} elseif ( 'selected' == $data['rtcl_location_display_rule'] && empty( $data['rtcl_location'] ) ) {
			return array();
		}
		$terms = get_terms( $term_args );
		if ( ! is_wp_error( $terms ) && 'all' == $data['rtcl_location_display_rule'] && $data['rtcl_location_limit'] ) {
			$number = $data['rtcl_location_limit'];
			$terms  = array_slice( $terms, 0, $number );
		}
		return $terms;
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		$view     = isset( $settings['rtcl_location_view'] ) ? $settings['rtcl_location_view'] : 'grid';
		$style    = 'style-1';
		if ( 'grid' == $view ) {
			$style = isset( $settings['rtcl_location_grid_style'] ) ? $settings['rtcl_location_grid_style'] : 'style-1';
			if ( ! in_array( $style, array_keys( $this->location_box_grid_style() ) ) ) {
				$style = 'style-1';
			}
		}
		$data  = array(
			'template'              => 'elementor/all-locations/' . $view . '/' . $style,
			'view'                  => $view,
			'style'                 => $style,
			'settings'              => $settings,
			'terms'                 => $this->widget_results( $settings ),
			'default_template_path' => null,
		);

		$data = apply_filters( 'rtcl_el_location_boxes_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}


}
