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
	Group_Control_Border
};

trait ListingPaginationTrait {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_sec_pagination() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_pagination',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Pagination', 'classified-listing' ),
				'condition' => array( 'rtcl_listing_pagination' => array( 'yes' ) ),
			),
			array(
				'label'      => __( 'Pagination spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_pagination_spacing',
				'mode'       => 'responsive',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listings-sc-wrapper .pagination ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_bg_color',
				'label'     => __( 'Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item .page-link' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_active_bg_color',
				'label'     => __( 'Active Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item .page-link:hover' => 'background-color: {{VALUE}};',
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_text_color',
				'label'     => __( 'Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item .page-link' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_active_text_color',
				'label'     => __( 'Active Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item .page-link:hover' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_pagination_border',
				'selector' => '{{WRAPPER}} .page-link, {{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item:hover .page-link, {{WRAPPER}} .page-item.active:hover .page-link',
			),
			array(
				'mode' => 'section_end',
			),

		);
		return $fields;
	}
}
