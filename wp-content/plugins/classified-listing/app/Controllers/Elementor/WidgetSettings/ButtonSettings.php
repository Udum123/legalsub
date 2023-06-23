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
class ButtonSettings {

    /**
     * Widget Field
     *
     * @return array
     */
    public static function style_settings() {
        $fields = [
            'button_section_start'      => [
                'mode'  => 'section_start',
                'label' => esc_html__( 'Button', 'classified-listing' ),
                'tab'   => 'style',
            ],
            'button_typography'         => [
                'mode'     => 'group',
                'type'     => 'typography',
                'label'    => esc_html__( 'Typography', 'classified-listing' ),
                'selector' => '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )',
            ],
            'button_height'             => [
                'label'     => esc_html__( 'Height', 'classified-listing' ),
                'type'      => 'slider',
                'range'     => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ],
			'button_width'             => [
				'label'     => esc_html__( 'Width', 'classified-listing' ),
				'type'      => 'slider',
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'width: {{SIZE}}{{UNIT}};',
				],
			],
            'button_tabs_start'         => [
                'mode' => 'tabs_start',
            ],
            'button_normal'             => [
                'mode'  => 'tab_start',
                'label' => esc_html__( 'Normal', 'classified-listing' ),
            ],
            'button_text_color_normal'  => [
                'label'     => esc_html__( 'Color', 'classified-listing' ),
                'type'      => 'color',

                'separator' => 'default',
                'selectors' => [
                    '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'color: {{VALUE}};',
                ],
            ],
            'button_bg_color_normal'    => [
                'label'     => esc_html__( 'Background Color', 'classified-listing' ),
                'type'      => 'color',

                'selectors' => [
                    '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'background-color: {{VALUE}};',
                ],
            ],

            'button_border'             => [
                'mode'       => 'group',
                'type'       => 'border',
                'selector'   => '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )',
                'size_units' => [ 'px' ],
            ],
            'button_normal_end'         => [
                'mode' => 'tab_end',
            ],
            'button_hover'              => [
                'mode'  => 'tab_start',
                'label' => esc_html__( 'Hover', 'classified-listing' ),
            ],
            'button_text_color_hover'   => [
                'label'     => esc_html__( 'Color', 'classified-listing' ),
                'type'      => 'color',

                'separator' => 'default',
                'selectors' => [
                    '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ):hover' => 'color: {{VALUE}};',
                ],
            ],
            'button_bg_color_hover'     => [
                'label'     => esc_html__( 'Background Color', 'classified-listing' ),
                'type'      => 'color',

                'selectors' => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ):hover'  => 'background-color: {{VALUE}};',
                ],
            ],
            'button_border_hover_color' => [
                'label'     => esc_html__( 'Border Color', 'classified-listing' ),
                'type'      => 'color',

                'selectors' => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ):hover'  => 'border-color: {{VALUE}};',
                ],
            ],
            'button_hover_end'          => [
                'mode' => 'tab_end',
            ],
            'button_tabs_end'           => [
                'mode' => 'tabs_end',
            ],
            'button_border_radius'      => [
                'label'      => esc_html__( 'Border Radius (px)', 'classified-listing' ),
                'type'       => 'dimensions',
                'default'    => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'size_units' => [ 'px' ],
                'separator'  => 'before',
                'selectors'  => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ],
            'button_padding'            => [
                'label'      => esc_html__( 'Padding (px)', 'classified-listing' ),
                'type'       => 'dimensions',
                'size_units' => [ 'px' ],
                'selectors'  => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ],
            'button_margin'             => [
                'label'      => esc_html__( 'Margin (px)', 'classified-listing' ),
                'type'       => 'dimensions',
                'size_units' => [ 'px' ],
                'selectors'  => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] )' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ],
			
            'button_section_end'        => [
                'mode' => 'section_end',
            ],
        ];
        return $fields;
    }

}
