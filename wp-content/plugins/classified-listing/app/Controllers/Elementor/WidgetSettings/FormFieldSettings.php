<?php
/**
 * Main ProductDescription class.
 *
 * @package RadiusTheme\SB
 */

namespace Rtcl\Controllers\Elementor\WidgetSettings;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Product Description class
 */
class FormFieldSettings {

	/**
	 * Widget Field
	 *
	 * @return array
	 */
	public static function fields_settings(): array  {
		return [
			'fields_label_style_start'      => [
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => esc_html__( 'Form Label', 'classified-listing' ),
			],
			'fields_label_typo'       => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => esc_html__( 'Label Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-widget-search-sortable :is( label )',
			],
			'fields_label_color'      => [
				'label'     => esc_html__( 'Label Color', 'classified-listing' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is( label )' => 'color: {{VALUE}} !important;',
				],
			],
			'fields_label_margin'     => [
				'label'      => esc_html__( 'Label Margin', 'classified-listing' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is( label )' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fields_label_style_end'        => [
				'mode' => 'section_end',
			],
			'fields_style_start'      => [
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => esc_html__( 'Form Field\'s', 'classified-listing' ),
			],
			'fields_text_typo'       => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => esc_html__( 'Typography', 'classified-listing' ),
				'selector' => '{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input, .rtcl-search-input-button )',
			],
			'fields_height'           => [
				'label'     => esc_html__( 'Field\'s Height', 'classified-listing' ),
				'type'      => 'slider',
				'separator' => 'default',
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input, .rtcl-search-input-button )' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			],

			'fields_tabs_start'       => [
				'mode' => 'tabs_start',
			],
			// Tab For normal view.
			'fields_normal'           => [
				'mode'  => 'tab_start',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			],
			'fields_border'           => [
				'mode'       => 'group',
				'type'       => 'border',
				'selector'   => '{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input, .rtcl-search-input-button )',
				'size_units' => [ 'px' ],
			],
			'fields_text_color'       => [
				'label'     => esc_html__( 'Text Color', 'classified-listing' ),
				'type'      => 'color',

				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input, .rtcl-search-input-button )' => 'color: {{VALUE}};',
				],
			],
			'fields_bg_color'         => [
				'label'     => esc_html__( 'Background Color', 'classified-listing' ),
				'type'      => 'color',
				'alpha'     => true,
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input, .rtcl-search-input-button )' => 'background-color: {{VALUE}};',
				],
			],
			'fields_normal_end'       => [
				'mode' => 'tab_end',
			],
			'fields_hover'            => [
				'mode'  => 'tab_start',
				'label' => esc_html__( 'Hover & Focus', 'classified-listing' ),
			],

			'fields_hover_border'     => [
				'mode'       => 'group',
				'type'       => 'border',
				'selector'   => '{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input):hover',
				'size_units' => [ 'px' ],
			],
			'fields_hover_text_color' => [
				'label'     => esc_html__( 'Text Color', 'classified-listing' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input):hover' => 'color: {{VALUE}};',
				],
			],
			'fields_hover_bg_color'   => [
				'label'     => esc_html__( 'Background Color', 'classified-listing' ),
				'type'      => 'color',
				'alpha'     => true,
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input):hover' => 'background-color: {{VALUE}};',
				],
			],

			'fields_hover_end'        => [
				'mode' => 'tab_end',
			],
			'fields_tabs_end'         => [
				'mode' => 'tabs_end',
			],
			'fields_border_radius'           => [
				'label'      => esc_html__( 'Border Radius', 'classified-listing' ),
				'size_units' => [ 'px' ],
				'type'       => 'dimensions',
				'selectors'  => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input)' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			],
			'fields_padding'          => [
				'label'      => esc_html__( 'Fields Padding (px)', 'classified-listing' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-widget-search-sortable :is(select, input, .rtcl-search-input-button )' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fields_style_end'        => [
				'mode' => 'section_end',
			],
		];

	}

}
