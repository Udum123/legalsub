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

trait ListingTitleTrait {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_sec_title() {
		$fields = array(

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_title',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Title', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_title' => 'yes',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_title_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .listing-item  .item-content  .rtcl-listing-title',
			),
			array(
				'label'      => __( 'Title Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_title_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .listing-item .item-content .listing-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'mode' => 'tabs_start',
				'id'   => 'title_tabs_start',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .listing-item .rtcl-listing-title a' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color_hover',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .listing-item .rtcl-listing-title a:hover' => 'color: {{VALUE}}' ),
			),

			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),

			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
}
