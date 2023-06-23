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
	Group_Control_Typography,
	Group_Control_Border

};

trait ListingActionBtnTrait {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_action_button() {
		$fields = [

			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_action_button',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Button', 'classified-listing' ),
			],
			[
				'mode'       => 'group',
				'type'       => Group_Control_Typography::get_type(),
				'id'         => 'rtcl_button_typo',
				'label'      => __( 'Button Typography', 'classified-listing' ),
				'selector'   => '{{WRAPPER}} .rtcl-list-view .rtin-details-button',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-2' ],
								],
							],
						],
					],
				],
			],
			[
				'mode' => 'tabs_start',
				'id'   => 'button_tabs_start',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_details_button_bg_color',
				'label'      => __( 'Details Button Background Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button' => 'background-color: {{VALUE}};',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-2' ],
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_details_button_text_color',
				'label'      => __( 'Details Button Text Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button' => 'color: {{VALUE}};',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-2' ],
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_button_bg_color',
				'label'      => __( 'Background Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtin-el-button a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-list-view.rtcl-style-5-view .rtin-el-button a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtin-el-button a' => 'background-color: {{VALUE}};',

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
									'value'    => [ 'style-1' ],
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'grid' ],
								],
								[
									'name'     => 'rtcl_listings_grid_style',
									'operator' => '!=',
									'value'    => '',
								],
							],
						],
					],
				],
			],

			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_button_text_color',
				'label'      => __( 'Button Text Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtin-el-button a,{{WRAPPER}} .rtin-el-button a .rtcl-icon ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button,{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button a .rtcl-icon ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom  a' => 'color: {{VALUE}};',
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
									'value'    => [ 'style-1' ],
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'grid' ],
								],
								[
									'name'     => 'rtcl_listings_grid_style',
									'operator' => '!=',
									'value'    => '',
								],
							],
						],
					],
				],
			],
			[
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'         => 'rtcl_button_border_color',
				'label'      => __( 'Border', 'classified-listing' ),
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
						'default' => '#e1e1e1',
					],
				],
				'selector'  => '{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'grid' ],
								],
								[
									'name'     => 'rtcl_listings_grid_style',
									'operator' => 'in',
									'value'    => [ 'style-5' ],
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_action_button_text_color',
				'label'      => __( 'Action Button Text Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-meta-buttons-withtext .rtcl-text-el-button a' => 'color: {{VALUE}};',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-5' ],
								],
							],
						],
					],
				],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_details_button_bg_hover_color',
				'label'      => __( 'Details Button Background Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button:hover' => 'background-color: {{VALUE}};',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-2' ],
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_details_button_text_hover_color',
				'label'      => __( 'Details Button Text Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-elementor-widget .rtin-details-button:hover' => 'color: {{VALUE}};',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-2' ],
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_button_bg_hover_color',
				'label'      => __( 'Background Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtin-el-button a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button:hover a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-list-view.rtcl-style-5-view .rtin-el-button a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a:hover' => 'background-color: {{VALUE}};',
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
									'value'    => [ 'style-1' ],
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'grid' ],
								],
								[
									'name'     => 'rtcl_listings_grid_style',
									'operator' => '!=',
									'value'    => '',
								],
							],
						],
					],
				],
			],

			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_button_hover_text_color',
				'label'      => __( 'Text Color In hover', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtin-el-button a:hover,{{WRAPPER}} .rtin-el-button a:hover .rtcl-icon ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button:hover,{{WRAPPER}} .rtcl-meta-buttons-wrap .rtcl-el-button:hover a .rtcl-icon ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a:hover' => 'color: {{VALUE}};',
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
									'value'    => [ 'style-1' ],
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'grid' ],
								],
								[
									'name'     => 'rtcl_listings_grid_style',
									'operator' => '!=',
									'value'    => '',
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_button_hover_border_color',
				'label'      => __( 'Border Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a:hover' => 'border-color: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => [ 'grid' ],
								],
								[
									'name'     => 'rtcl_listings_grid_style',
									'operator' => 'in',
									'value'    => [ 'style-5' ],
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_action_button_hover_text_color',
				'label'      => __( 'Action Button Hover Text Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-meta-buttons-withtext .rtcl-text-el-button a:hover' => 'color: {{VALUE}};',
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
									'operator' => 'in',
									'value'    => [ 'style-1', 'style-5' ],
								],
							],
						],
					],
				],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode' => 'tabs_end',
			],

			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}
}
