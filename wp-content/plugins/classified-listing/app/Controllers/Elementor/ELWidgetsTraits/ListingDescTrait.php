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
	Group_Control_Typography
};

trait ListingDescTrait {
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_sec_description() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_description',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Description', 'classified-listing' ),
				'condition' => array( 'rtcl_show_description' => array( 'yes' ) ),

			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Description', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_description_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-short-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_description_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-short-description',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_description_color',
				'label'     => __( 'Short Description Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-short-description' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_description_hover_color',
				'label'     => __( 'On Items Hover Description color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .listing-item:hover .rtcl-short-description' => 'color: {{VALUE}}' ),
			),

			array(
				'mode' => 'section_end',
			),

		);
		return $fields;
	}
}
