<?php
/**
 * Main Elementor Headerbtn Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since 2.1.0
 */

namespace Rtcl\Controllers\Elementor\Widgets;

use Rtcl\Abstracts\ElementorWidgetBase;
use Elementor\Controls_Manager;
use Rtcl\Helpers\Functions;
use Elementor\Group_Control_Border;

/**
 * Elementor HeaderButton Widget.
 *
 * Elementor widget.
 *
 * @since 1.0.0
 */
class HeaderButton extends ElementorWidgetBase {

	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->rtcl_name = __( 'Header Button', 'classified-listing' );
		$this->rtcl_base = 'rtcl-listing-headerbtn';
		parent::__construct( $data, $args );
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->general_button_fields(),
			$this->content_visibility_fields(),
			$this->compare_button_fields(),
			$this->favourites_button_fields(),
			$this->sign_in_button_fields(),
			$this->chat_option_button_fields(),
			$this->add_listing_button_fields(),
		);
		return $fields;
	}
		/**
		 * Set field controlls
		 *
		 * @return array
		 */
	public function content_visibility_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Content Visibility ', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_favourites',
				'label'       => __( 'Show favourites icon', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Icon. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_favourites_icon_order',
				'label'       => __( 'Favourites icon order', 'classified-listing' ),
				'default'     => '2',
				'description' => __( 'Icon order', 'classified-listing' ),
				'condition'   => array(
					'rtcl_show_favourites' => 'yes',
				),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_sec_sign_in',
				'label'       => __( 'Show profile', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Icon. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_sign_in_icon_order',
				'label'       => __( 'Profile icon order', 'classified-listing' ),
				'default'     => '4',
				'description' => __( 'Profile', 'classified-listing' ),
				'condition'   => array(
					'rtcl_show_sec_sign_in' => 'yes',
				),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_add_listing_button',
				'label'       => __( 'Show add listing button', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Icon. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_add_listing_button_order',
				'label'       => __( 'Add listing button order', 'classified-listing' ),
				'default'     => '5',
				'description' => __( 'Add listing icon order', 'classified-listing' ),
				'condition'   => array(
					'rtcl_show_add_listing_button' => 'yes',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'el_header_button_visibility_fields', $fields, $this );
	}

	/**
	 * Button fieldcontrolls
	 *
	 * @return array
	 */
	public function general_button_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_general',
				'label' => __( 'General', 'classified-listing' ),
			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Content spacing', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_content_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Icon Border Radius', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'header_button_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'     => __( 'Icon Size', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_area_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Icon Font Size', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_font_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Icon Gap', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_icon_gap_size',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn' => 'gap: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Button fieldcontrolls
	 *
	 * @return array
	 */
	public function compare_button_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_compare',
				'label'     => __( 'Compare', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_compare' => 'yes',
				),
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'compare_icon',
				'label'   => esc_html__( 'Cart Icon', 'classified-listing' ),
				'default' => array(
					'value'   => 'fas fa-exchange-alt',
					'library' => 'solid',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Button fieldcontrolls
	 *
	 * @return array
	 */
	public function favourites_button_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_favourites',
				'label'     => __( 'Favourites', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_favourites' => 'yes',
				),
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'favourites_icon',
				'label'   => esc_html__( 'Cart Icon', 'classified-listing' ),
				'default' => array(
					'value'   => 'far fa-heart',
					'library' => 'solid',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Button fieldcontrolls
	 *
	 * @return array
	 */
	public function sign_in_button_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_sign_in',
				'label'     => __( 'Profile', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_sec_sign_in' => 'yes',
				),
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'sign_in_icon',
				'label'   => esc_html__( 'Sign in Icon', 'classified-listing' ),
				'default' => array(
					'value'   => 'far fa-user',
					'library' => 'solid',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Button fieldcontrolls
	 *
	 * @return array
	 */
	public function chat_option_button_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_chat_option',
				'label'     => __( 'Chat Option', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_chat_option' => 'yes',
				),
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'sec_chat_option_icon',
				'label'   => esc_html__( 'Chat Icon', 'classified-listing' ),
				'default' => array(
					'value'   => 'far fa-comments',
					'library' => 'solid',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Button fieldcontrolls
	 *
	 * @return array
	 */
	public function add_listing_button_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_add_listing',
				'label'     => __( 'Add listing button', 'classified-listing' ),
				'condition' => array(
					'rtcl_show_add_listing_button' => 'yes',
				),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'add_listing_button_text',
				'label'   => __( 'Button text', 'classified-listing' ),
				'default' => 'Add listing',
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'add_listing_icon',
				'label'   => esc_html__( 'Add Listing Icon', 'classified-listing' ),
				'default' => array(
					'value'   => 'fas fa-plus-circle',
					'library' => 'solid',
				),
			),
			array(
				'label'     => __( 'Button width', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_button_width',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li.rtcl-el-add-listing-btn a' => 'width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'     => __( 'Button Height', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'rtcl_button_height',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li.rtcl-el-add-listing-btn a' => 'height: {{SIZE}}{{UNIT}};',
				),
			),
			
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Set Style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->widget_icon_style_fields(),
			$this->widget_button_style_fields()
		);
		return $fields;
	}
	/**
	 * Set Style controlls
	 *
	 * @return array
	 */
	public function widget_icon_style_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_icon_style_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Icon style', 'classified-listing' ),
			),
			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'icon_tabs_start',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'icon_color_tab',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_bg_color',
				'label'     => __( 'Icon Background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a'   => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_color',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a'   => 'color: {{VALUE}}',
				),
			),
			array(
				'type'           => Group_Control_Border::get_type(),
				'mode'           => 'group',
				'id'             => 'icon_border',
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
						'default' => 'rgba(0, 0, 0, 0.15)',
					),
				),
				'selector'       => '{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_count_bg_color',
				'label'     => __( 'Icon Count Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn .count'   => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_count_color',
				'label'     => __( 'Icon Count Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn .count'   => 'color: {{VALUE}}',
				),
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'icon_color_tab_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_bg_color_hover',
				'label'     => __( 'Icon Background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a:hover'   => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_color_hover',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a:hover'   => 'color: {{VALUE}}',
				),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'icon_border_hover',
				'selector' => '{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn li:not(.rtcl-el-add-listing-btn) a:hover',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_count_bg_color_hover',
				'label'     => __( 'Icon Count Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn a:hover .count'   => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_count_color_hover',
				'label'     => __( 'Icon Count Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn a:hover .count'   => 'color: {{VALUE}}',
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

		);
		return $fields;
	}
	/**
	 * Set Style controlls
	 *
	 * @return array
	 */
	public function widget_button_style_fields(): array {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_button_style_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Button style', 'classified-listing' ),
			),
			// Wrapper style settings.
			array(
				'mode' => 'tabs_start',
				'id'   => 'icon_listing_tabs_start',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'icon_listing_color_tab',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_add_listing_bg_color',
				'label'     => __( 'Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-header-btn .rtcl-el-add-listing-btn a.rtcl-el-item-btn'   => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_listing_text_color',
				'label'     => __( 'Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-item-btn'   => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_listing_icon_color',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-add-listing-btn .rtcl-el-item-btn span'   => 'color: {{VALUE}}',
				),
			),

			array(
				'mode' => 'tab_end',
			),
			// Tab For Normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'icon_listing_color_tab_hover',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_add_listing_bg_color_hover',
				'label'     => __( 'Background Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-add-listing-btn a.rtcl-el-item-btn:hover'   => 'background-color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_listing_text_color_hover',
				'label'     => __( 'Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-item-btn:hover'   => 'color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'icon_listing_icon_color_hover',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-listing-header-action .rtcl-el-add-listing-btn:hover span'   => 'color: {{VALUE}}',
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
		);
		return $fields;
	}
	/**
	 * Undocumented function
	 *
	 * @return array.
	 */
	public function get_style_depends() {
		return array( 'elementor-icons-fa-regular', 'elementor-icons-shared-0', 'elementor-icons-fa-solid' );
	}
	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() { ?>
		<?php
			$settings         = $this->get_settings();
			$data             = array(
				'template'              => 'elementor/header-button/header-button',
				'default_template_path' => null,
				'settings'              => $settings,
			);
			$data['settings'] = $settings;
			$data             = apply_filters( 'rtcl_el_header_button_data', $data );
			Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
			?>
		<?php
	}

}
