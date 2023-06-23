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

use Rtcl\Abstracts\ElementorWidgetBase;
use Elementor\Controls_Manager;
use Rtcl\Helpers\Functions;
use \Elementor\Icons_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Typography;
/**
 * Elementor SingleLocation Widget.
 *
 * Elementor widget.
 *
 * @since 1.0.0
 */
class SingleLocation extends ElementorWidgetBase {

	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->rtcl_name = __( 'Single Location', 'classified-listing' );
		$this->rtcl_base = 'rtcl-listing-single-location';
		parent::__construct( $data, $args );
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		
		$location_dropdown = $this->taxonomy_list( 'all', 'rtcl_location' );
		$fields            = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_general',
				'label' => __( 'General', 'classified-listing' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_location_style',
				'label'   => __( 'Style', 'classified-listing' ),
				'options' => $this->location_box_style(),
				'default' => 'style-1',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'location',
				'label'   => __( 'Location', 'classified-listing' ),
				'options' => $location_dropdown,
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'display_count',
				'label'     => __( 'Show Listing Counts', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),

			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'enable_link',
				'label'     => __( 'Enable Link', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),

			array(
				'type'      => Controls_Manager::ICONS,
				'id'        => 'box_icon',
				'label'     => esc_html__( 'Icon', 'classified-listing' ),
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'solid',
				),
				'condition' => array(
					'rtcl_location_style' => 'style-3',
				),
			),

			array(
				'type'       => Controls_Manager::SLIDER,
				'mode'       => 'responsive',
				'id'         => 'width',
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'label'      => __( 'Max Width', 'classified-listing' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'mode'       => 'responsive',
				'id'         => 'height',
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 290,
				),
				'label'      => __( 'Box Height', 'classified-listing' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box' => 'height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),

			array(
				'mode'  => 'section_start',
				'id'    => 'sec_background',
				'label' => __( 'Background', 'classified-listing' ),
			),

			array(
				'type'     => Group_Control_Background::get_type(),
				'mode'     => 'group',
				'types'    => array( 'classic', 'gradient' ),
				'id'       => 'bgimg',
				'label'    => __( 'Background', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-el-listing-location-box .rtin-img',
			),

			array(
				'label'     => esc_html__( 'Overlay Settings', 'classified-listing' ),
				'id'        => 'bg_control_heading',
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'rtcl_location_overlay_tabs_start',
			),

			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_location_overlay_tab_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),

			array(
				'type'     => Group_Control_Background::get_type(),
				'mode'     => 'group',
				'types'    => array( 'gradient' ),
				'id'       => 'gradient_bg',
				'label'    => __( 'Overlay Background', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-el-listing-location-box:not(.location-box-style-3) .rtin-content,{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3 .rtcl-image-wrapper .rtin-img::before',
			),

			array(
				'mode' => 'tab_end',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_location_overlay_tab_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),

			array(
				'type'     => Group_Control_Background::get_type(),
				'mode'     => 'group',
				'types'    => array( 'gradient' ),
				'id'       => 'gradient_bg_hover',
				'label'    => __( 'Overlay Background', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-el-listing-location-box:not(.location-box-style-3):hover .rtin-content,{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3 .rtcl-image-wrapper .rtin-img::after',
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

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function location_box_style() {
		$style = apply_filters(
			'rtcl_el_location_box_style',
			array(
				'style-1' => __( 'Style 1', 'classified-listing' ),
				'style-2' => __( 'Style 2', 'classified-listing' ),
			)
		);

		return $style;
	}
	/**
	 * Set Style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array(
			// Style Tab.
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_color',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Style', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Title', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtin-title'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .rtin-title a' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_hover_color',
				'label'     => __( 'Title Hover', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box:hover .rtin-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-el-listing-location-box:hover .rtin-title a' => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'counter_color',
				'label'     => __( 'Counter', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtin-counter' => 'color: {{VALUE}}' ),
			),
			
			array(
				'mode' => 'section_end',
			),

			array(
				'mode'      => 'section_start',
				'id'        => 'sec_icon_style',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Icon', 'classified-listing' ),
				'condition' => array(
					'rtcl_location_style' => 'style-3',
				),
			),
			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'rtcl_location_icon_start',
			),

			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_location_icon_normal',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_bg_color',
				'label'     => __( 'Icon Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3 .rtin-content > a'   => 'background: {{VALUE}}',

				),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_color',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3 .rtin-content > a'   => 'color: {{VALUE}}',
				),
			),

			array(
				'id'         => 'icon_rotation',
				'label'      => esc_html__( 'Icon Rotate', 'classified-listing' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 5,
					),
				),
				'default'    => array(
					'size' => '',
					'unit' => 'deg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3 .rtin-content > a' => 'transform: rotate({{SIZE}}{{UNIT}});',
				),
			),

			array(
				'mode' => 'tab_end',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_location_icon_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_bg_hover_color',
				'label'     => __( 'Icon Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3:hover .rtin-content > a'   => 'background: {{VALUE}}',

				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_hover_color',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3 .rtin-content > a:hover'   => 'color: {{VALUE}}',

				),
			),
			array(
				'id'         => 'icon_rotation_hover',
				'label'      => esc_html__( 'Icon Rotate', 'classified-listing' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 5,
					),
				),
				'default'    => array(
					'size' => '',
					'unit' => 'deg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-listing-location-box.location-box-style-3:hover .rtin-content>a' => 'transform: rotate({{SIZE}}{{UNIT}});',
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
				'mode'  => 'section_start',
				'id'    => 'sec_style_type',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Typography', 'classified-listing' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtin-counter',
			),
			array(
				'mode' => 'section_end',
			),

		);
		return $fields;
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function get_style_depends() {
		return array( 'elementor-icons-shared-0', 'elementor-icons-fa-solid' );
	}
	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		ob_start();
		Icons_Manager::render_icon( $settings['box_icon'], array( 'aria-hidden' => 'true' ) );

		$icon  = ob_get_clean();
		$style = isset( $settings['rtcl_location_style'] ) ? $settings['rtcl_location_style'] : 'style-1';
		if ( ! in_array( $style, array_keys( $this->location_box_style() ) ) ) {
			$style = 'style-1';
		}
		$data = array(
			'template'              => 'elementor/single-location/grid-style-1',
			'style'                 => $style,
			'icon'                  => $icon,
			'default_template_path' => null,
		);
		$term = get_term( $settings['location'], 'rtcl_location' );
		if ( $term && ! is_wp_error( $term ) ) {
			$data['title']     = $term->name;
			$data['count']     = $term->count;
			$data['permalink'] = get_term_link( $term );
		} else {
			$data['title']             = __( 'Please Select a Location and Background', 'classified-listing' );
			$data['count']             = 0;
			$settings['display_count'] = $data['enable_link'] = false;
			$data['permalink']         = '#';
		}

		$data['settings'] = $settings;
		$data             = apply_filters( 'rtcl_el_location_box_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}



}
