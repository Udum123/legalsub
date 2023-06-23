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
class IconSettings {

    /**
     * Widget Field
     *
     * @return array
     */
    public static function style_settings() {
		$fields = array(
			'rtcl_icon_style_wrapper' => array(
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Icon style', 'classified-listing' ),
			),
			'icon_wrapper_size'             => [
				'label'     => esc_html__( 'Icon Wrapper Size', 'classified-listing' ),
				'type'      => 'slider',
				'size_units' => [ 'px' ],
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],

				],
				'selectors' => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper' => 'width:{{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],

			],
			
			'icon_size'           => [
				'label'      => esc_html__( 'Font Size', 'classified-listing' ),
				'type'      => 'slider',
				'size_units' => [ 'px' ],
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],

				],
				'selectors'  => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper :is( span, i )' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			
			
			'icon_border_radius'           => [
				'label'      => esc_html__( 'Border Radius', 'classified-listing' ),
				'size_units' => [ 'px' ],
				'type'       => 'dimensions',
				'selectors'  => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			],
			
			
			// Wrapper style settings.
			'icon_tabs_start' => array(
				'mode' => 'tabs_start',
			),
			// Tab For Normal view.
			'icon_color_tab' => array(
				'mode'  => 'tab_start',
				'label' => esc_html__( 'Normal', 'classified-listing' ),
			),
			'icon_bg_color' => array(
				'type'      => 'color',
				'label'     => __( 'Icon Background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper'   => 'background-color: {{VALUE}}',
				),
			),
			'icon_color' => array(
				'type'      => 'color',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper'   => 'color: {{VALUE}}',
				),
			),
			'icon_border' => array(
				'type'           => 'border',
				'mode'           => 'group',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '0',
							'left'     => '0',
							'isLinked' => false,
						),
					),
					'color'  => array(
						'default' => 'rgba(0, 0, 0, 0.15)',
					),
				),
				'selector'       => '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper',
			),


			'icon_color_tab_end' => array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			'icon_color_tab_hover' => array(
				'mode'  => 'tab_start',
				'label' => esc_html__( 'Hover', 'classified-listing' ),
			),
			'icon_bg_color_hover' => array(
				'type'      => 'color',
				'label'     => __( 'Icon Background', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ):hover .icon-wrapper' => 'background-color: {{VALUE}}',
				),
			),
			'icon_color_hover' => array(
				'type'      => 'color',
				'label'     => __( 'Icon Color', 'classified-listing' ),
				'selectors' => array(
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ):hover .icon-wrapper'   => 'color: {{VALUE}}',
				),
			),
			'icon_border_hover' => array(
				'type'     => 'border',
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ):hover .icon-wrapper',
			),
			
			'icon_color_tab_hover_end' => array(
				'mode' => 'tab_end',
			),
			'icon_tabs_start_end' => array(
				'mode' => 'tabs_end',
			),
			'icon_margin'            => [
				'label'      => esc_html__( 'Icon Margin (px)', 'classified-listing' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} :is( .btn, button, [type=button], [type=reset], [type=submit] ) .icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			],
			'rtcl_icon_style_wrapper_end' => array(
				'mode' => 'section_end',
			),

		);
        return $fields;
    }

}
