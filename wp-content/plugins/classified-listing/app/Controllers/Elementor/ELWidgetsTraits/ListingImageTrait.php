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

use Elementor\{
	Controls_Manager,
	Group_Control_Border,
	Group_Control_Image_Size,
	Group_Control_Typography
};

trait ListingImageTrait {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_image_wrapper() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_image_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Image Wrapper', 'classified-listing' ),
				'condition' => ['rtcl_show_image' => ['yes']],
			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Image Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_image_mobile_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .listing-item .listing-thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

}
