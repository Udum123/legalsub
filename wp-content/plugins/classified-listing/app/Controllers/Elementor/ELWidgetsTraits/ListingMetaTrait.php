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

trait ListingMetaTrait {
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_sec_meta() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_meta',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Meta', 'classified-listing' ),
			),

			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_meta_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-meta-data li,{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtcl-list-view .category a,{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtcl-grid-view .category a',
			),
			array(
				'label'      => __( 'Meta Wrpper Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_meta_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget .rtcl-listing-meta-data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Meta Item Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_meta_item_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtcl-listing-meta-data li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'meta_tabs_start',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_meta_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_icon_color',
				'label'     => __( 'Meta Icon Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-color: {{VALUE}}' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listing-meta-data li i' => 'color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_category_color',
				'label'     => __( 'Category Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl.rtcl-elementor-widget .category a' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_meta_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_hover_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-hover-color: {{VALUE}}',
					'{{WRAPPER}} .listing-item:hover .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_hover_icon_color',
				'label'     => __( 'Meta Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-hove-color: {{VALUE}}',
					'{{WRAPPER}} .listing-item:hover .rtcl-listing-meta-data li i' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_category_color_hover',
				'label'     => __( 'Category Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl.rtcl-elementor-widget .category a:hover' => 'color: {{VALUE}}' ),
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
