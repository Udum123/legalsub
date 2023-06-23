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
use Rtcl\Abstracts\ElementorWidgetBase;
use Rtcl\Helpers\Functions;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

/**
 * ListingCategoryBox Class
 */
class ListingSearch extends ElementorWidgetBase {

	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->rtcl_name = __( 'Search Form', 'classified-listing' );
		$this->rtcl_base = 'rtcl-listing-search-form';
		parent::__construct( $data, $args );
	}

	/**
	 * Search from style
	 *
	 * @return array
	 */
	public function search_style() {
		$style = apply_filters(
			'rtcl_el_search_style',
			array(
				'dependency' => esc_html__( 'Dependency Selection', 'classified-listing' ),
			)
		);
		return $style;
	}

	/**
	 * Search from style
	 *
	 * @return array
	 */
	public function search_oriantation() {
		$style = apply_filters(
			'rtcl_el_search_oriantation',
			array(
				'inline'   => __( 'Inline', 'classified-listing' ),
				'vertical' => __( 'Vertical', 'classified-listing' ),
			)
		);
		return $style;
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields() : array {

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'classified-listing' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'search_style',
				'label'   => __( 'Style', 'classified-listing' ),
				'options' => $this->search_style(),
				'default' => 'dependency',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'search_oriantation',
				'label'   => __( 'Oriantation', 'classified-listing' ),
				'options' => $this->search_oriantation(),
				'default' => 'inline',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'fields_label',
				'label'     => __( 'Show fields Label', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => '',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'keyword_field',
				'label'     => __( 'Keywords field', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'location_field',
				'label'     => __( 'Location field', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),
			
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'category_field',
				'label'     => __( 'Category field', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'types_field',
				'label'     => __( 'Types field', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'price_field',
				'label'     => __( 'Price field', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => '',
			),

			array(
				'mode' => 'section_end',
			),

		);
		$fields = apply_filters( 'rtcl_el_search_general_fields', $fields, $this );
		return $fields;
	}
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->style_wrapper_fields(),
			$this->style_from_fields(),
			$this->style_button_fields(),
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function style_wrapper_fields() {
		$fields = array(

			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_form_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'General Style', 'classified-listing' ),
			),
			array(
				'label'      => __( 'Wrapper Padding', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-widget-search-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Border Radius', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'wrapper_border_radius',
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-widget-search-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'     => Group_Control_Background::get_type(),
				'mode'     => 'group',
				'types'    => array( 'classic' ),
				'id'       => 'form-bg',
				'label'    => __( 'Background', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-widget-search-form',
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
	public function style_from_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_fields_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Fields', 'classified-listing' ),
			),
			array(
				'mode'      => 'group',
				'type'      => Group_Control_Typography::get_type(),
				'id'        => 'rtcl_label_typo',
				'label'     => __( 'Label Typography', 'classified-listing' ),
				'selector'  => '{{WRAPPER}} .show-field-label .ws-item > label',
				'condition' => array(
					'fields_label' => 'yes',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'field_label_color',
				'label'     => __( 'Label Text Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .show-field-label label' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'fields_label' => 'yes',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'field_text_color',
				'label'     => __( 'Field text color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search .form-control,{{WRAPPER}} .show-field-label .ws-item .search-input-label' => '--search-placeholder-color:{{VALUE}}; color: {{VALUE}}',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'field_bg_color',
				'label'     => __( 'Field background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} .form-control' => 'background-color: {{VALUE}}',
				),
			),
			array(
				'label'     => __( 'Field Gutter Spacing', 'classified-listing' ),
				'type'      => Controls_Manager::SLIDER,
				'id'        => 'field_gutter_spacing',
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search ' => '--search-items-gap: {{SIZE}}{{UNIT}};',
				),
			),

			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_field_height',
				'label'      => __( 'Field Height', 'classified-listing' ),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px'      => array(
						'min' => 20,
						'max' => 130,
					),
					'default' => array(
						'unit' => 'px',
						'size' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search .form-control, {{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]' => 'height: {{SIZE}}{{UNIT}};',
				),
			),

			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_field_border',
				'selector' => '{{WRAPPER}} .rtcl-elementor-widget-search .form-control',
			),

			array(
				'label'      => __( 'Border Radius', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'filed_border_radius',
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-control ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	public function style_button_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_button_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Button Section', 'classified-listing' ),
			),
			array(
				'label'      => __( 'Margin', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'search_button_margin',
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Padding', 'classified-listing' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'search_button_pading',
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Min Width', 'classified-listing' ),
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'field_min_width',
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 800,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition'  => array(
					'search_oriantation' => 'inline',
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search.rtcl-widget-search-inline .ws-button' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Max Width', 'classified-listing' ),
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'field_max_width',
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 800,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition'  => array(
					'search_oriantation' => 'vertical',
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-elementor-widget-search.rtcl-widget-search-vertical .btn[type=submit]' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_button_typo',
				'label'    => __( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]',
			),
			array(
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'button_alignment',
				'label'     => __( 'Button alignment', 'classified-listing' ),
				'options'   => $this->alignment_options(),
				'default'   => 'left',
				'condition' => array(
					'search_oriantation' => 'vertical',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'button_tabs_start',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'button_normal_tabs_start',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_from_button_bg_color',
				'label'     => __( 'Button Background', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]' => 'background-color: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_from_button_text_color',
				'label'     => __( 'Button Text Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]' => 'color: {{VALUE}};' ),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_button_border',
				'selector' => '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]',
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode'  => 'tab_start',
				'id'    => 'button_hover_tabs_start',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_from_button_hover_bg_color',
				'label'     => __( 'Button Background', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]:hover' => 'background-color: {{VALUE}};' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_from_button_hover_text_color',
				'label'     => __( 'Button Text Color', 'classified-listing' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]:hover' => 'color: {{VALUE}};' ),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'rtcl_button_hover_border',
				'selector' => '{{WRAPPER}} .rtcl-elementor-widget-search .btn[type=submit]:hover',
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
	 * Activete field count
	 *
	 * @param [type] $settings all settings.
	 * @return int
	 */
	private function active_count( $settings ) {
		$active_count = 1;
		if ( $settings['keyword_field'] ) {
			$active_count++;
		}
		if ( $settings['location_field'] ) {
			$active_count++;
		}
		if ( $settings['category_field'] ) {
			$active_count++;
		}
		if ( $settings['types_field'] ) {
			$active_count++;
		}
		if ( $settings['price_field'] ) {
			$active_count++;
		}
		return $active_count;
	}
	/**
	 * Display Output.
	 *
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings();

		$search_style       = isset( $settings['search_style'] ) ? $settings['search_style'] : 'dependency';
		$search_oriantation = ! empty( $settings['search_oriantation'] ) ? $settings['search_oriantation'] : 'inline';

		$template_style = 'elementor/search/search';

		$data = array(
			'id'                    => wp_rand(),
			'template'              => $template_style,
			'style'                 => $search_style,
			'active_count'          => $this->active_count( $settings ),
			'selected_location'     => false,
			'widget_base'           => $this->rtcl_base,
			
			'selected_category'     => false,
			'orientation'           => $search_oriantation,
			'classes'               => array(
				'rtcl',
				'rtcl-widget-search',
				'rtcl-widget-search-' . $search_oriantation,
				'rtcl-widget-search-style-' . $search_style,
			),
			'settings'              => $settings,
			'default_template_path' => '',
		);

		$data = apply_filters( 'rtcl_el_search_widget_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}
}
