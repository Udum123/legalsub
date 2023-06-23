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

use Elementor\Controls_Manager;

trait ListingResponsiveControlTrait {
	/**
	 * Set field controlls
	 *
	 * @return array
	 */
	public function listing_responsive_control() {
		$fields = [
			// Responsive Columns.
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_responsive',
				'label'     => __( 'Responsive Columns', 'classified-listing' ),
				'condition' => [ 'rtcl_listings_view' => [ 'grid' ] ],
			],
			[
				'type'    => Controls_Manager::SELECT,
				'mode'    => 'responsive',
				'id'      => 'rtcl_listings_column',
				'label'   => __( 'Column', 'classified-listing' ),
				'options' => $this->column_number(),
				'default' => '3',
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
			],
			[
				'mode' => 'section_end',
			],

		];
		return $fields;
	}

}
