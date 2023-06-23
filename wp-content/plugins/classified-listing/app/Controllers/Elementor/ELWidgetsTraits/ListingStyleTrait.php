<?php
/**
 * Trait for listing widget
 *
 * The Elementor builder.
 *
 * @package  Classifid-listing
 * @since    2.0.10
 */

namespace Rtcl\Controllers\Elementor\ELWidgetsTraits;

trait ListingStyleTrait {

	/**
	 * Listings view function
	 *
	 * @return array
	 */
	public function listings_view() {
		return [
			'list' => [
				'title' => esc_html__( 'List View', 'classified-listing' ),
				'url'   => rtcl()->get_assets_uri( "images/el-layout/list-layout.png" ),
			],
			'grid' => [
				'title' => esc_html__( 'Grid View', 'classified-listing' ),
				'url'   => rtcl()->get_assets_uri( "images/el-layout/grid-layout.png" ),
			],
		];
	}

	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function list_style() {
		$style = apply_filters(
			'rtcl_el_listings_list_style',
			[
				'style-1' => [
					'title' => esc_html__( 'Style 1', 'classified-listing' ),
					'url'   => rtcl()->get_assets_uri( "images/el-layout/list-style-01.png" ),
				],
			]
		);
		return $style;
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function grid_style() {
		$style = apply_filters(
			'rtcl_el_listings_grid_style',
			[
				'style-1' => [
					'title' => esc_html__( 'Style 1', 'classified-listing' ),
					'url'   => rtcl()->get_assets_uri( "images/el-layout/grid-style-01.png" ),
				],
			]
		);
		return $style;
	}

}
