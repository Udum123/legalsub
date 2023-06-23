<?php
/**
 * Main Elementor ListingCategoryBox Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Rtcl\Abstracts\ElementorWidgetBase;
use Rtcl\Helpers\Functions;
/**
 * ListingCategoryBox Class
 */
class ListingCategoryBox extends ElementorWidgetBase {

	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		// TODO: Box border Radius need add.
		$this->rtcl_name      = __( 'Listing Category', 'classified-listing' );
		$this->rtcl_base      = 'rtcl-listing-cat-box';
		$this->rtcl_translate = array(
			'cols' => apply_filters(
				'rtcl_listing_cat_box_column',
				array(
					'12' => __( '1 Col', 'classified-listing' ),
					'6'  => __( '2 Col', 'classified-listing' ),
					'4'  => __( '3 Col', 'classified-listing' ),
					'3'  => __( '4 Col', 'classified-listing' ),
					'2'  => __( '6 Col', 'classified-listing' ),
				)
			),
		);
		parent::__construct( $data, $args );
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'rtcl-public' );
	}
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields() : array {

		$category_dropdown = $this->taxonomy_list( 'parent' );
		$fields            = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'classified-listing' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_cats_style',
				'label'   => __( 'Style', 'classified-listing' ),
				'options' => $this->cat_box_style(),
				'default' => 'style-1',
			),
			array(
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_cats',
				'label'       => __( 'Categories', 'classified-listing' ),
				'options'     => $category_dropdown,
				'multiple'    => true,
				'description' => __( 'Start typing category names. If empty then all parent categories will be displayed', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_category_limit',
				'label'       => __( 'Category Limit', 'classified-listing' ),
				'default'     => '10',
				'description' => __( 'How Many Category will Display ?', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_sub_category',
				'label'     => __( 'Display Sub Category', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
				'condition' => array( 'rtcl_cats_style' => array( 'style-2' ) ),
			),

			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_sub_category_limit',
				'label'       => __( 'Sub Category Limit', 'classified-listing' ),
				'default'     => '5',
				'description' => __( 'How Many Child Category will Display ?', 'classified-listing' ),
				'condition'   => array(
					'rtcl_show_sub_category' => array( 'yes' ),
					'rtcl_cats_style'        => array( 'style-2' ),
				),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_pad_counts',
				'label'     => __( 'Counts Include Children', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_orderby',
				'label'   => __( 'Order By', 'classified-listing' ),
				'options' => array(
					'none'    => __( 'None', 'classified-listing' ),
					'term_id' => __( 'ID', 'classified-listing' ),
					'date'    => __( 'Date', 'classified-listing' ),
					'name'    => __( 'Title', 'classified-listing' ),
					'count'   => __( 'Count', 'classified-listing' ),
					'custom'  => __( 'Custom Order', 'classified-listing' ),
				),
				'default' => 'name',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_order',
				'label'   => __( 'Sort By', 'classified-listing' ),
				'options' => array(
					'asc'  => __( 'Ascending', 'classified-listing' ),
					'desc' => __( 'Descending', 'classified-listing' ),
				),
				'default' => 'asc',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_hide_empty',
				'label'       => __( 'Hide Empty', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => '',
				'description' => __( 'Hide Categories that has no listings. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_image',
				'label'       => __( 'Show Icon/Image', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Icon/Image. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_category_title',
				'label'       => __( 'Show Title', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Category Title. Default: On', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'rtcl_icon_type',
				'label'     => __( 'Icon Type', 'classified-listing' ),
				'options'   => array(
					'image' => __( 'Image', 'classified-listing' ),
					'icon'  => __( 'Icon', 'classified-listing' ),
				),
				'default'   => 'icon',
				'condition' => array( 'rtcl_show_image' => array( 'yes' ) ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_count',
				'label'       => __( 'Listing Counts', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Counts. Default: On', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::TEXT,
				'id'        => 'display_text_after_count',
				'label'     => __( 'Text After Count ', 'classified-listing' ),
				'default'   => 'Ads',
				'condition' => array(
					'rtcl_show_count' => 'yes',
				),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_description',
				'label'       => __( 'Category Description', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Description. Default: On', 'classified-listing' ),
			),

			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_content_limit',
				'label'       => __( 'Description Word Limit', 'classified-listing' ),
				'default'     => '12',
				'description' => __( 'Number of Words to display', 'classified-listing' ),
				'condition'   => array( 'rtcl_description' => array( 'yes' ) ),
			),

			array(
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'rtcl_cat_box_alignment',
				'label'     => __( 'Content alignment', 'classified-listing' ),
				'options'   => $this->alignment_options(),
				'default'   => 'center',
				'condition' => array( 'rtcl_cats_style' => 'style-1' ),
			),
			array(
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'rtcl_cat_box_style_2_alignment',
				'label'     => __( 'Content alignment', 'classified-listing' ),
				'options'   => $this->alignment_options(),
				'default'   => 'left',
				'condition' => array( 'rtcl_cats_style' => 'style-2' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_equal_height',
				'label'     => __( 'Equal Height', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => '',
			),
			array(
				'mode' => 'section_end',
			),

			// Responsive Columns.
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_responsive',
				'label' => __( 'Number of Responsive Columns', 'classified-listing' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_col_xl',
				'label'   => __( 'Desktops: >1199px', 'classified-listing' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_col_lg',
				'label'   => __( 'Desktops: >991px', 'classified-listing' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_col_md',
				'label'   => __( 'Tablets: >767px', 'classified-listing' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_col_sm',
				'label'   => __( 'Phones: >575px', 'classified-listing' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '6',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_col_mobile',
				'label'   => __( 'Small Phones: <576px', 'classified-listing' ),
				'options' => $this->rtcl_translate['cols'],
				'default' => '12',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'rtcl_el_listing_category_widget_general_field', $fields, $this );
	}

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function cat_box_style() {
		$style = apply_filters(
			'rtcl_el_category_box_style',
			array(
				'style-1' => __( 'Style 1', 'classified-listing' ),
			)
		);

		return $style;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields() : array {
		$fields = array(
			// Style Tab.
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Box style', 'classified-listing' ),
			),

			array(
				'label'      => __( 'Gutter pading', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_gutter_padding',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .cat-item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Box pading', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_padding',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .cat-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Box Border Radius', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_cat_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Head Section Pading', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_head_gutter_padding',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'rtcl_cats_style' => 'style-2',
				),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .rtin-head-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Body Section Pading', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_description_gutter_padding',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'rtcl_cats_style' => 'style-2',
				),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .box-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'rtcl_wrapper_tabs_start',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_box_wrapper_tab_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_header',
				'label'     => __( 'Head background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .rtin-head-area' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'header_border_color',
				'label'     => __( 'Header Border Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .rtin-head-area' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_body',
				'label'     => __( 'Body background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'mode'      => 'group',
				'label'     => __( 'Background', 'classified-listing' ),
				'id'        => 'rtcl_background',
				'type'      => Group_Control_Background::get_type(),
				'selector'  => '{{WRAPPER}} .cat-item-wrap .cat-details',
				'condition' => array(
					'rtcl_cats_style' => 'style-1',
				),
			),

			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_border',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						),
					),
					'color'  => array(
						'default' => 'rgba(0, 0, 0, 0.05)',
					),
				),
				'selector' => '{{WRAPPER}}  .cat-item-wrap .cat-details',
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_box_shadow',
				'selector' => '{{WRAPPER}}  .cat-item-wrap .cat-details',
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_box_wrapper_tab_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_header_hover',
				'label'     => __( 'Head background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap:hover .rtin-head-area' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_background_body_hover',
				'label'     => __( 'Body background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap:hover .box-body' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rtcl_cats_style' => 'style-2',
				),
			),
			array(
				'mode'      => 'group',
				'label'     => __( 'Background', 'classified-listing' ),
				'id'        => 'rtcl_background_hover',
				'type'      => Group_Control_Background::get_type(),
				'selector'  => '{{WRAPPER}} .cat-item-wrap:hover .cat-details',
				'condition' => array(
					'rtcl_cats_style' => 'style-1',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_border_hover',
				'selector' => '{{WRAPPER}}  .cat-item-wrap .cat-details',
			),
			array(
				'label'    => __( 'Box Shadow', 'classified-listing' ),
				'type'     => Group_Control_Box_Shadow::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_hover_box_shadow',
				'selector' => '{{WRAPPER}}  .cat-item-wrap:hover .cat-details',
			),
			array(
				'mode' => 'tab_end',
			),

			array(
				'mode' => 'tabs_end',
			),

			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'rtcl_content_alignment',
				'label'   => __( 'Content Vertical Alignment', 'classified-listing' ),
				'options' => array(
					'none'           => __( 'None', 'classified-listing' ),
					'content-middle' => __( 'Middle', 'classified-listing' ),
				),
				'default' => 'none',
			),

			array(
				'mode' => 'section_end',
			),

			// Image settings.
			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_icon',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Icon And Image', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_image' => 'yes',
				),
			),
			array(
				'label'     => __( 'Icon Area', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_image_area_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details .icon a' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Icon Size', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_font_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details .icon a .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cat-item-wrap .cat-details img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Image Size', 'classified-listing' ),
				'type'      => Group_Control_Image_Size::get_type(),
				'id'        => 'rtcl_icon_image_size',
				'mode'      => 'group',
				'default'   => 'large',
				'separator' => 'none',
				'condition' => array(
					'rtcl_icon_type' => 'image',
				),
			),
			array(
				'label'      => __( 'Border Radius', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_icon_image_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details .icon a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Image Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_image_spacing',
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .icon'  => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}  .cat-item-wrap .image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'image_icon_tabs_start',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'box_icon_tab_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap  .cat-details .icon a .rtcl-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cat-item-wrap  .rtin-sub-cats li i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cat-item-wrap  .rtin-sub-cats li a:hover' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_icon_border',
				'selector' => '{{WRAPPER}} .cat-item-wrap  .cat-details .icon a',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_bg',
				'label'     => __( 'Icon Bg Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap  .cat-details .icon a' => 'background-color: {{VALUE}}',
				),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'box_image_tab_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_hover_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover  .cat-details .icon a .rtcl-icon' => 'color: {{VALUE}}' ),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_icon_hover_border',
				'selector' => '{{WRAPPER}} .cat-item-wrap:hover .cat-details .icon a ',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_bg_hover',
				'label'     => __( 'Icon Bg Hover Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .cat-item-wrap:hover  .cat-details .icon a' => 'background-color: {{VALUE}}',
				),
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

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_title',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Title', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_category_title' => 'yes',
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
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap .cat-details h3, {{WRAPPER}} .cat-item-wrap .cat-details h3 a' => 'color: {{VALUE}}' ),
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
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover .cat-details h3, {{WRAPPER}} .cat-item-wrap:hover .cat-details h3 a' => 'color: {{VALUE}}' ),
			),

			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_title_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details h3',
			),
			array(
				'label'      => __( 'Title Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_title_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .cat-details h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_counter',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Counter', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_count' => 'yes',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'counter_tabs_start',
			),

			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_counter_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_counter_color',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap .cat-details .views' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_counter_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_counter_color_hover',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover .cat-details .views' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_counter_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details .views',
			),
			array(
				'label'      => __( 'Counter Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_counter_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}  .cat-item-wrap .cat-details .views' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),

			array(
				'mode'      => 'section_start',
				'id'        => 'rtcl_sec_short_description',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Short Description', 'classified-listing' ),
				'condition' => array(
					'rtcl_description' => 'yes',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'content_tabs_start',
			),

			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_content_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_content_color_normal',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap .cat-details p' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_content_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_content_color_hover',
				'label'     => __( 'Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .cat-item-wrap:hover .cat-details p' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_content_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .cat-item-wrap .cat-details p',
			),
			array(
				'label'      => __( 'Content Spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_content_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .cat-item-wrap .cat-details p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'rtcl_el_listing_category_widget_style_field', $fields, $this );
	}
	/**
	 * Marge all controlls.
	 *
	 * @return array
	 */
	public function widget_fields() : array {
		$fields = array_merge(
			$this->widget_general_fields(),
			$this->pro_notice_fields(),
			$this->widget_style_fields()
		);
		return $fields;
	}

	/**
	 * Widget result.
	 *
	 * @param [array] $data array of query.
	 * @return array
	 */
	public function widget_results( $data ) {
		
		$args = array(
			'taxonomy'     => rtcl()->category,
			'parent'       => 0,
			'orderby'      => ! empty( $data['rtcl_orderby'] ) ? $data['rtcl_orderby'] : 'title',
			'order'        => ! empty( $data['rtcl_order'] ) ? $data['rtcl_order'] : 'desc',
			'hide_empty'   => ! empty( $data['rtcl_hide_empty'] ) ? 1 : 0,
			'include'      => ! empty( $data['rtcl_cats'] ) ? $data['rtcl_cats'] : array(),
			'hierarchical' => false,
		);
		if ( 'custom' === $data['rtcl_orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_rtcl_order';
		}
		$terms = get_terms( $args );
		if ( ! empty( $data['rtcl_category_limit'] ) ) {
			$number = $data['rtcl_category_limit'];
			$terms  = array_slice( $terms, 0, $number );
		}
		return $terms;
	}



	/**
	 * Elementor Edit mode need some extra js for isotop reinitialize
	 *
	 * @return mixed
	 */
	public function edit_mode_script() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<script>
				(function ($) {
					// equalHeight();
					$(".rtcl-equal-height").each(function () {
					var $equalItemWrap = $(this),
						equalItems = $equalItemWrap.find('.equal-item');
					equalItems.height('auto');
					if ($(window).width() > 767) {
						var maxH = 0;
						equalItems.each(function () {
							var itemH = $(this).outerHeight();
							if (itemH > maxH) {
								maxH = itemH;
							}
						});
						equalItems.height(maxH + 'px');
					} else {
						equalItems.height('auto');
					}

				});
			})(jQuery);
			</script>
			<?php
		}
	}
	/**
	 * Display Output.
	 *
	 * @return void
	 */
	protected function render() {
		wp_enqueue_style( 'rtcl-public' );
		
		$settings = $this->get_settings();
		$terms    = $this->widget_results( $settings );

		$style = isset( $settings['rtcl_cats_style'] ) ? $settings['rtcl_cats_style'] : 'style-1';
		if ( ! in_array( $style, array_keys( $this->cat_box_style() ) ) ) {
			$style = 'style-1';
		}
		$template_style = 'elementor/listing-cat-box/grid-' . $style;
		$data           = array(
			'template'              => $template_style,
			'style'                 => $style,
			'settings'              => $settings,
			'terms'                 => $terms,
			'default_template_path' => null,
		);
		$data           = apply_filters( 'rtcl_el_category_box_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		$this->edit_mode_script();
	}


}
