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
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

trait ListingWrapperTrait {
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_listing_wrapper() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_listing_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Item Wrapper', 'classified-listing' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_wrapper_bg_color',
				'label'     => __( 'Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget
				.listing-item' => 'background-color: {{VALUE}};',
				],
			],
			[
				'label'    => __( 'Box Shadow', 'classified-listing' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_listing_wrapper_box_shadow',
				'selector' => '{{WRAPPER}} .rtcl.rtcl-elementor-widget
				.listing-item',
			],
			[
				'label'    => __( 'Hover Box Shadow', 'classified-listing' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_listing_wrapper_hover_box_shadow',
				'selector' => '{{WRAPPER}}  .rtcl.rtcl-elementor-widget
				.listing-item:hover',
			],
			[
				'label'      => __( 'Wrapper Spacing', 'classified-listing' ),
				'mode'       => 'responsive',
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget .listing-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'list' ],
								],
								[
									'name'     => 'rtcl_listings_style',
									'operator' => '!in',
									'value'    => [ 'style-4' ],
								],
							],
						],
					],
				],
			],

			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_wrapper_gutter_spacing',
				'label'      => __( 'Gutter Spacing', 'classified-listing' ),
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
					'size' => '30',
				],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-grid-view' => 'grid-column-gap: {{SIZE}}{{UNIT}};grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-list-view .listing-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			],

			[
				'label'      => __( 'Content Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_content_wrapper_spacing',
				'mode'       => 'responsive',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}  .listing-item .item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'classified-listing' ),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.05)',
					],
				],
				'selector'       => '{{WRAPPER}} .rtcl .rtcl-list-view .listing-item, {{WRAPPER}} .rtcl .rtcl-grid-view .listing-item',

			],
			[
				'mode' => 'section_end',
			],
		];
		return apply_filters( 'el_widget_listing_wrapper_settings_fields', $fields, $this );
	}

}
