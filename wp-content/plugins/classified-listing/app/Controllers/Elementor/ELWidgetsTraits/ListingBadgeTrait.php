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

trait ListingBadgeTrait {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_badge_section() {
		$fields = array(

			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_badge_section',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Badge ', 'classified-listing' ),
				'condition' => ['rtcl_show_labels' => ['yes']],
			),
			array(
				'label'      => __( 'padding', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_badge_wrapper_padding',
				'mode'       => 'responsive',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-listing-badge-wrap .badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_badge_wrapper_spacing',
				'label'      => __( 'Badge Spacing', 'classified-listing' ),
				'mode'       => 'responsive',
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
					'%'  => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '4',
				],
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-listing-badge-wrap' => 'gap: {{SIZE}}{{UNIT}};',
				),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_sold_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Sold Out', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
		
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_sold_out_bg_color',
				'label'     => __( 'Sold Out Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-sold-out ' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_sold_out_text_color',
				'label'     => __( 'Sold Out Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-sold-out ' => 'color: {{VALUE}};',
				),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Default Badge', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_badge_typo',
				'label'    => __( 'Badge Default Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-badge-wrap .badge, {{WRAPPER}} .listing-item  .item-content .badge',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_bg_color',
				'label'     => __( 'Badge Default Background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .badge' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .badge' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_text_color',
				'label'     => __( 'Badge Default Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .badge' => 'color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .badge' => 'color: {{VALUE}};',
				),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_top_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Top', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_top_badge_bg_color',
				'label'     => __( 'Top Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-_top' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .rtcl-badge-_top' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_top_badge_text_color',
				'label'     => __( 'Top Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-_top' => 'color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .rtcl-badge-_top' => 'color: {{VALUE}};',
				),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_featured_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Featured', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_featured_badge_bg_color',
				'label'     => __( 'Featured Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-featured ' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .listing-item.is-featured .listing-thumb:after, {{WRAPPER}} .listing-item.is-featured .rtcl-badge-featured ' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_featured_badge_text_color',
				'label'     => __( 'Featured Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-featured' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-listings .listing-item.is-featured .listing-thumb:after, {{WRAPPER}} .listing-item.is-featured .rtcl-badge-featured' => 'color: {{VALUE}};',
				),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_new_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'New', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_new_badge_bg_color',
				'label'     => __( 'New Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-new' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .rtcl-badge-new' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_new_badge_text_color',
				'label'     => __( 'New Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-new' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-new' => 'color: {{VALUE}};',
				),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_popular_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Popular', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_popular_badge_bg_color',
				'label'     => __( 'Popular Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-popular' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .rtcl-badge-popular' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_popular_badge_text_color',
				'label'     => __( 'Popular Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-popular' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-popular' => 'color: {{VALUE}};',
				),
			),
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_badge_bump_up_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Bump Up', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_bump_up_badge_bg_color',
				'label'     => __( 'Bump Up Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-_bump_up' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .listing-item .rtcl-badge-_bump_up' => 'background-color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_bump_up_badge_text_color',
				'label'     => __( 'Bump Up Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-badge-wrap .rtcl-badge-_bump_up' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-_bump_up' => 'color: {{VALUE}};',
				),
			),

			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function badge_visibility() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Badge Visibility ', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_new',
				'label'     => __( 'Show New?', 'classified-listing' ),
				'label_on'  => __( 'Show', 'classified-listing' ),
				'label_off' => __( 'Hide', 'classified-listing' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Badge New ', 'classified-listing'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_top',
				'label'     => __( 'Show Top?', 'classified-listing' ),
				'label_on'  => __( 'Show', 'classified-listing' ),
				'label_off' => __( 'Hide', 'classified-listing' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Badge Top ', 'classified-listing'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_featured',
				'label'     => __( 'Show Featured?', 'classified-listing' ),
				'label_on'  => __( 'Show', 'classified-listing' ),
				'label_off' => __( 'Hide', 'classified-listing' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Badge Featured ', 'classified-listing'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_popular',
				'label'     => __( 'Show Popular?', 'classified-listing' ),
				'label_on'  => __( 'Show', 'classified-listing' ),
				'label_off' => __( 'Hide', 'classified-listing' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Badge Popular ', 'classified-listing'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_bump_up',
				'label'     => __( 'Show Bump Up?', 'classified-listing' ),
				'label_on'  => __( 'Show', 'classified-listing' ),
				'label_off' => __( 'Hide', 'classified-listing' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Badge Bump Up ', 'classified-listing'),
			),
			array(
				'mode' => 'section_end',
			),

		);
		return $fields;
	}


}
