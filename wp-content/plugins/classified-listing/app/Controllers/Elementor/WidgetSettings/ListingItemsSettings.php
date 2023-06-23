<?php
/**
 * Main Elementor ListingCategoryBox Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Elementor\WidgetSettings;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Rtcl\Abstracts\ElementorWidgetBase;
use Rtcl\Helpers\Functions;
use Elementor\Group_Control_Border;
use Rtcl\Resources\Options;
use Elementor\Group_Control_Image_Size;
use Rtcl\Controllers\Elementor\ELWidgetsTraits\{
	ListingStyleTrait,
	ListingWrapperTrait,
	ListingPromotionFieldsTrait,
	ListingResponsiveControlTrait,
	ListingContentVisibilityTrait
};

/**
 * ListingCategoryBox Class
 */
class ListingItemsSettings extends ElementorWidgetBase {
	/**
	 * Content visiblity field.
	 */
	use ListingContentVisibilityTrait;
	/**
	 * Responsive control.
	 */
	use ListingResponsiveControlTrait;
	/**
	 * Item Wrapper Control.
	 */
	use ListingWrapperTrait;
	/**
	 * Promotion Section.
	 */
	use ListingPromotionFieldsTrait;
	/**
	 * Listing style or view related trait
	 */
	use ListingStyleTrait;
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {

		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_image_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Image Wrapper', 'classified-listing' ),
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Image Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_image_mobile_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .listing-item .listing-thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_title',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Title', 'classified-listing' ),
				'condition' => [
					'rtcl_show_title' => 'yes',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_title_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .listing-item  .item-content  .rtcl-listing-title',
			],
			[
				'label'      => __( 'Title Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_title_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .listing-item .item-content .listing-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			[
				'mode' => 'tabs_start',
				'id'   => 'title_tabs_start',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .listing-item .rtcl-listing-title a' => 'color: {{VALUE}}' ],
			],
			[
				'mode' => 'tab_end',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_title_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_title_color_hover',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .listing-item .rtcl-listing-title a:hover' => 'color: {{VALUE}}' ],
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
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_meta',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Meta', 'classified-listing' ),
			],

			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_meta_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-meta-data li,{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtcl-list-view .category a,{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtcl-grid-view .category a',
			],
			[
				'label'      => __( 'Meta Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_meta_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .rtcl-listing-meta-data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'tabs_start',
				'id'   => 'meta_tabs_start',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_meta_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_icon_color',
				'label'     => __( 'Meta Icon Color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-color: {{VALUE}}' ],
				'selectors' => [ '{{WRAPPER}} .rtcl-listing-meta-data li i' => 'color: {{VALUE}}' ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_category_color',
				'label'     => __( 'Category Color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .rtcl.rtcl-elementor-widget .category a' => 'color: {{VALUE}}' ],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_meta_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_hover_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-hover-color: {{VALUE}}',
					'{{WRAPPER}} .listing-item:hover .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_hover_icon_color',
				'label'     => __( 'Meta Icon Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-hove-color: {{VALUE}}',
					'{{WRAPPER}} .listing-item:hover .rtcl-listing-meta-data li i' => 'color: {{VALUE}}',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_category_color_hover',
				'label'     => __( 'Category Color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .rtcl.rtcl-elementor-widget .category a:hover' => 'color: {{VALUE}}' ],
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

			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_description',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Description', 'classified-listing' ),
				'condition' => [ 'rtcl_show_description' => [ 'yes' ] ],

			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Description', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_description_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-short-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_description_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-short-description',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_description_color',
				'label'     => __( 'Short Description Color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .rtcl-short-description' => 'color: {{VALUE}}' ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_description_hover_color',
				'label'     => __( 'On Items Hover Description color', 'classified-listing' ),
				'selectors' => [ '{{WRAPPER}} .listing-item:hover .rtcl-short-description' => 'color: {{VALUE}}' ],
			],

			[
				'mode' => 'section_end',
			],

			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_price',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Price', 'classified-listing' ),
				'condition' => [
					'rtcl_show_price' => [ 'yes' ],
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .listing-item .item-price .rtcl-price',
			],

			[
				'mode'      => 'group',
				'type'      => Group_Control_Typography::get_type(),
				'id'        => 'rtcl_price_unit_label_typo',
				'label'     => __( 'Unit Label Typography', 'classified-listing' ),
				'selector'  => '{{WRAPPER}} .listing-item .item-price .rtcl-price-unit-label',
				'condition' => [
					'rtcl_show_price_unit' => [ 'yes' ],
				],
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_unit_label_color',
				'label'     => __( 'Unit Label Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .item-price .rtcl-price-unit-label'       => 'color: {{VALUE}};',
				],
				'condition' => [
					'rtcl_show_price_unit' => [ 'yes' ],
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Price padding', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_amount_wrapper_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}  .listing-item .item-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Price Margin', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_amount_wrapper_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}  .listing-item .item-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_amount_bg_color',
				'label'      => __( 'Background Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings .listing-item .item-price' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
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
									'value'    => [ 'style-3' ],
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
									'operator' => 'in',
									'value'    => [ 'style-5' ],
								],
							],
						],
					],
				],
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_amount_text_color',
				'label'     => __( 'Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-price'       => 'color: {{VALUE}};',
				],
			],

			[
				'mode' => 'section_end',
			],

			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_badge_section',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Badge ', 'classified-listing' ),
			],
			[
				'label'      => __( 'padding', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_badge_wrapper_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-listing-badge-wrap .badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'label'      => __( 'Margin', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_badge_wrapper_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings .listing-item .rtcl-listing-badge-wrap .badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			// [
			// 	'mode'     => 'group',
			// 	'type'     => Group_Control_Typography::get_type(),
			// 	'id'       => 'rtcl_badge_sold_typo',
			// 	'label'    => __( 'Sold Out Typography', 'classified-listing' ),
			// 	'selector' => '{{WRAPPER}}  .rtcl-sold-out',
			// ],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_sold_out_bg_color',
				'label'     => __( 'Sold Out Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-sold-out ' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_sold_out_text_color',
				'label'     => __( 'Sold Out Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-sold-out ' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_badge_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .listing-item  .item-content .badge',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_bg_color',
				'label'     => __( 'Badge Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .badge' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_badge_text_color',
				'label'     => __( 'Badge Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .badge' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_top_badge_bg_color',
				'label'     => __( 'Top Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .rtcl-badge-_top' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_top_badge_text_color',
				'label'     => __( 'Top Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .rtcl-badge-_top' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_featured_badge_bg_color',
				'label'     => __( 'Featured Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item.is-featured .listing-thumb:after, {{WRAPPER}} .listing-item.is-featured .rtcl-badge-featured ' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_featured_badge_text_color',
				'label'     => __( 'Featured Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings .listing-item.is-featured .listing-thumb:after, {{WRAPPER}} .listing-item.is-featured .rtcl-badge-featured' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_new_badge_bg_color',
				'label'     => __( 'New Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .rtcl-badge-new' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_new_badge_text_color',
				'label'     => __( 'New Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-new' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_popular_badge_bg_color',
				'label'     => __( 'Popular Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .rtcl-badge-popular' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_popular_badge_text_color',
				'label'     => __( 'Popular Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-popular' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_bump_up_badge_bg_color',
				'label'     => __( 'Bump Up Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .listing-item .rtcl-badge-_bump_up' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_bump_up_badge_text_color',
				'label'     => __( 'Bump Up Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings .rtcl-badge-_bump_up' => 'color: {{VALUE}};',
				],
			],

			[
				'mode' => 'section_end',
			],
			
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_pagination',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Pagination', 'classified-listing' ),
				'condition' => [ 'rtcl_listing_pagination' => [ 'yes' ] ],
			],
			[
				'label'      => __( 'Pagination spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_pagination_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-sc-wrapper .pagination ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_bg_color',
				'label'     => __( 'Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .page-item .page-link' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_active_bg_color',
				'label'     => __( 'Active Background Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item .page-link:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
				],
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_text_color',
				'label'     => __( 'Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .page-item .page-link' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_pagination_active_text_color',
				'label'     => __( 'Active Text Color', 'classified-listing' ),
				'selectors' => [
					'{{WRAPPER}} .page-item.active .page-link, {{WRAPPER}} .page-item .page-link:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_pagination_border',
				'selector' => '{{WRAPPER}} .page-link',
			],
			[
				'mode' => 'section_end',
			],
		];
		return apply_filters( 'rtcl_el_listing_widget_style_field', $fields, $this );
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$category_dropdown = $this->taxonomy_list();
		$location_dropdown = $this->taxonomy_list( 'all', 'rtcl_location' );
		$listing_order_by  = [
			'title' => __( 'Title', 'classified-listing' ),
			'date'  => __( 'Date', 'classified-listing' ),
			'ID'    => __( 'ID', 'classified-listing' ),
			'price' => __( 'Price', 'classified-listing' ),
			'views' => __( 'Views', 'classified-listing' ),
			'none'  => __( 'None', 'classified-listing' ),
		];
		$listing_order_by  = apply_filters( 'rtcl_el_listing_order_by', $listing_order_by );

		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_layout',
				'label' => __( 'Layout', 'classified-listing' ),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_layout_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'View', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_listings_view',
				'options' => $this->listings_view(),
				'default' => 'list',
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Style', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'      => 'rtcl-image-selector',
				'id'        => 'rtcl_listings_style',
				'options'   => $this->list_style(),
				'default'   => 'style-1',
				'condition' => [
					'rtcl_listings_view' => 'list',
				],
			],
			[
				'type'      => 'rtcl-image-selector',
				'id'        => 'rtcl_listings_grid_style',
				'options'   => $this->grid_style(),
				'default'   => 'style-1',
				'condition' => [
					'rtcl_listings_view' => 'grid',
				],
			],
			[
				'type'    => Controls_Manager::SELECT,
				'mode'    => 'responsive',
				'id'      => 'rtcl_listings_column',
				'label'   => __( 'Column', 'classified-listing' ),
				'options' => $this->column_number(),
				'default' => '3',
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'condition' => [
					'rtcl_listings_view' => 'grid',
				],
			],
			[
				'mode' => 'section_end',
			],
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'classified-listing' ),
			],
			[
				'type'     => Controls_Manager::SELECT2,
				'id'       => 'rtcl_listings_promotions',
				'label'    => __( 'Promotions', 'classified-listing' ),
				'options'  => Options::get_listing_promotions(),
				'multiple' => true,
			],

			[
				'type'     => Controls_Manager::SELECT2,
				'id'       => 'rtcl_listings_promotions_not_in',
				'label'    => __( 'Promotions Exclude', 'classified-listing' ),
				'options'  => Options::get_listing_promotions(),
				'multiple' => true,
			],

			[
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_listing_types',
				'label'   => __( 'Listing Types', 'classified-listing' ),
				'options' => array_merge(
					[
						'all' => 'All',
					],
					Functions::get_listing_types(), // OR Options::get_default_listing_types().
				),
				'default' => 'all',
			],

			[
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_listings_by_categories',
				'label'       => __( 'Categories', 'classified-listing' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'label_block'    => true,
				'default'     => '',
				'description' => __( 'Start typing category names. If empty then all listings will display.', 'classified-listing' ),
			],

			[
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'rtcl_listings_categories_include_children',
				'label'      => __( 'Include Children Categories', 'classified-listing' ),
				'label_on'   => __( 'On', 'classified-listing' ),
				'label_off'  => __( 'Off', 'classified-listing' ),
				'default'    => '',
				'conditions' => [
					'terms' => [
						[
							'name'     => 'rtcl_listings_by_categories',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],

			],

			[
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_locations',
				'label'       => __( 'Locations', 'classified-listing' ),
				'options'     => $location_dropdown,
				'multiple'    => true,
				'label_block'    => true,
				'default'     => '',
				'description' => __( 'Start typing locations names.', 'classified-listing' ),
			],
			[
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'rtcl_listings_location_include_children',
				'label'      => __( 'Include Inner Location', 'classified-listing' ),
				'label_on'   => __( 'On', 'classified-listing' ),
				'label_off'  => __( 'Off', 'classified-listing' ),
				'default'    => '',
				'conditions' => [
					'terms' => [
						[
							'name'     => 'rtcl_locations',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			],
			[
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_listing_per_page',
				'label'       => __( 'Listing Per Page', 'classified-listing' ),
				'default'     => '10',
				'description' => __( 'Number of listing to display', 'classified-listing' ),
			],
			[
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_listing_pagination',
				'label'     => __( 'Pagination', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => '',
			],
			[
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_orderby',
				'label'   => __( 'Order By', 'classified-listing' ),
				'options' => $listing_order_by,
				'default' => 'date',
			],
			[
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'rtcl_order',
				'label'     => __( 'Sort By', 'classified-listing' ),
				'options'   => [
					'asc'  => __( 'Ascending', 'classified-listing' ),
					'desc' => __( 'Descending', 'classified-listing' ),
				],
				'default'   => 'desc',
				'condition' => [ 'rtcl_orderby!' => [ 'rand' ] ],
			],
			[
				'label'     => __( 'Image Size', 'classified-listing' ),
				'type'      => Group_Control_Image_Size::get_type(),
				'id'        => 'rtcl_thumb_image',
				'exclude' => [ 'custom' ],
				'mode'      => 'group',
				'default'   => 'rtcl-thumbnail',
				'separator' => 'none',
			],
			[
				'id'        	=> 'rtcl_no_listing_text',
				'label' 		=> esc_html__( 'No Listing Text', 'classified-listing' ),
				'type' 			=> Controls_Manager::TEXTAREA,
				'rows' 			=> 10,
				'default' 		=> esc_html__( 'No Listing Found', 'classified-listing' ),
				'placeholder' 	=> esc_html__( 'Type your description here', 'classified-listing' ),
			],
			[
				'mode' => 'section_end',
			],

		];

		return apply_filters( 'rtcl_el_listing_widget_general_field', $fields, $this );
	}
	
	public function widget_button_style_fields(): array {
		$fields = [

			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_action_button',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Button', 'classified-listing' ),
			],
			[
				'mode' => 'tabs_start',
				'id'   => 'button_tabs_start',
			],
			// Tab For Normal view.
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
					'{{WRAPPER}} .rtin-el-button a' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .rtin-el-button a,{{WRAPPER}} .rtin-el-button a .rtcl-icon ' => 'color: {{VALUE}};',
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
				'type'       => Controls_Manager::COLOR,
				'id'         => 'rtcl_button_borger_color',
				'label'      => __( 'Border Color', 'classified-listing' ),
				'selectors'  => [
					'{{WRAPPER}} .rtcl-grid-view.rtcl-style-5-view .rtin-bottom .action-btn a' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .rtin-el-button a:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .rtin-el-button a:hover,{{WRAPPER}} .rtin-el-button a:hover .rtcl-icon ' => 'color: {{VALUE}};',
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
		return apply_filters( 'rtcl_el_listing_items_widget_button_style_field', $fields , $this );
	}

	/**
	 * Set field controlls
	 *
	 * @return array
	 */
	public function widget_fields(): array {
		$fields = array_merge(
			$this->widget_general_fields(),
			$this->listing_content_visibility_fields(),
			$this->pro_notice_fields(),
			$this->widget_listing_wrapper(),
			$this->widget_button_style_fields(),
			$this->widget_style_fields()
		);
		return $fields;
	}

}
