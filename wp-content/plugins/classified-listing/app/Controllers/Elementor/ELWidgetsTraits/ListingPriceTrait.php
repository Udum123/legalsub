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

trait ListingPriceTrait {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_sec_price() {
		$fields = array(

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_price',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Price', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_price' => array( 'yes' ),
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .listing-item .item-price .rtcl-price',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_amount_text_color',
				'label'     => __( 'Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-price'       => 'color: {{VALUE}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_unit_label_typo',
				'label'    => __( 'Price Label Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .listing-item .item-price .rtcl-price-meta span',
				'condition' => array(
					'rtcl_show_price_unit' => array( 'yes' ),
				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_unit_label_color',
				'label'     => __( 'Price Label Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .listing-item .item-price .rtcl-price-meta span'       => 'color: {{VALUE}};',
				),
				'condition' => array(
					'rtcl_show_price_unit' => array( 'yes' ),
				),
			),

			array(
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_amount_bg_color',
				'label'      => __( 'Background Color', 'classified-listing' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-listings .listing-item .item-price' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'list' ),
								),
								array(
									'name'     => 'rtcl_listings_style',
									'operator' => 'in',
									'value'    => array( 'style-3' ),
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'grid' ),
								),
								array(
									'name'     => 'rtcl_listings_grid_style',
									'operator' => 'in',
									'value'    => array( 'style-5' ),
								),
							),
						),
					),
				),
			),

			array(
				'mode'       => 'responsive',
				'label'      => __( 'Price padding', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_amount_wrapper_padding',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .listing-item .item-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'list' ),
								),
								array(
									'name'     => 'rtcl_listings_style',
									'operator' => 'in',
									'value'    => array( 'style-3' ),
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'grid' ),
								),
								array(
									'name'     => 'rtcl_listings_grid_style',
									'operator' => 'in',
									'value'    => array( 'style-5' ),
								),
							),
						),
					),
				),
			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Price Margin', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_amount_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .listing-item .item-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),

		);
		return $fields;
	}
}
