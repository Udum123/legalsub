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

trait ListingPromotionFieldsTrait {
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function listing_promotion_section() {
		$fields = array(

			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_promotion_schema',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Promotional Post', 'classified-listing' ),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'promotion_tabs_start',
			),
			// Tab For normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_promotion_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_top_color',
				'label'     => __( 'Top Background Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-top.as-top' => 'background: {{VALUE}};' ),
				'default'   => '#FFFDEA',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_top_border_color',
				'label'     => __( 'Top Border Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-top.as-top' => 'border-color: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_featured_color',
				'label'     => __( 'Featured Background Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-featured' => 'background: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_featured_border_color',
				'label'     => __( 'Featured Border Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-featured' => 'border-color: {{VALUE}};' ),
			),

			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_promotion_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_top_hover_color',
				'label'     => __( 'Top Background Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-top.as-top:hover' => 'background: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_top_border_hover_color',
				'label'     => __( 'Top Border Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-top.as-top:hover' => 'border-color: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_featured_hover_color',
				'label'     => __( 'Featured Background Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-featured:not(.as-top):hover' => 'background: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_promotion_is_featured_hover_border_color',
				'label'     => __( 'Featured Border Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-listings .listing-item.is-featured:not(.as-top):hover' => 'border-color: {{VALUE}};' ),
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
