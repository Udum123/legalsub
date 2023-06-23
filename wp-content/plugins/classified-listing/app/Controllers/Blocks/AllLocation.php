<?php

/**
 * Main Gutenberg AllLocation Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Blocks;

use Rtcl\Helpers\Functions;

class AllLocation
{
	protected $name = 'rtcl/all-location';

	protected $attributes = [];

	public function get_attributes($default = false)
	{
		$attributes = array(
			'blockId'      => array(
				'type'    => 'string',
				'default' => '',
			),
			"col_style" => array(
				"type" => "object",
				"default" => array(
					"style" => "grid",
					"style_list" => "1",
					"style_grid" => "1",
				),
			),
			'colBGColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes { background-color:{{colBGColor}}; }']]
			],
			'colBorderColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes { border-color:{{colBorderColor}}; }']]
			],
			'colBorderWith'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes {border-width:{{colBorderWith}}; }']]
			],
			'colBorderStyle'      => [
				'type'    => 'string',
				'default' => 'solid',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes { border-style:{{colBorderStyle}}; }']]
			],
			'colBorderRadius'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes { border-radius:{{colBorderRadius}}; }']]
			],
			"col_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .grid-style-1 .location-boxes,
				{{RTCL}} .list-style-1 .location-boxes {padding:{{col_padding}} !important;}']]
			),
			"gutter_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .location-boxes-wrapper {padding:{{gutter_padding}};}']]
			),
			'headerBGColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations.grid-style-2 .location-boxes .location-boxes-header { background-color:{{headerBGColor}}; }']]
			],
			'headerBorderColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations.grid-style-2 .location-boxes .location-boxes-header { border-color:{{headerBorderColor}}; }']]
			],
			"header_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .location-boxes-header {padding:{{header_padding}} !important;}']]
			),
			'childIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => 	[(object)[
					'selector' => '{{RTCL}} .gb-all-locations .rtcl-gb-sub-location li i {
						color:{{childIconColor}};
					}'
				]],
			],
			'childColor' => [
				'type' => 'string',
				'style' => [(object)[
					'selector' => '{{RTCL}} .gb-all-locations .rtcl-gb-sub-location li a {
						color:{{childColor}} !important;
					}'
				]],
			],
			'childHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [(object)[
					'selector' => '{{RTCL}} .gb-all-locations .rtcl-gb-sub-location li a:hover {
						color:{{childHoverColor}};
					}'
				]],
			],
			'childTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .rtcl-gb-sub-location li']],
			],
			"child_location_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .location-boxes-body {padding:{{child_location_padding}} !important;}']]
			),
			"titleColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-title a{color:{{titleColor}} !important;}']]
			),
			"titleHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-title a:hover {color:{{titleHoverColor}} !important;}'],
				],

			),
			'titleTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '18', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-title'
					],
				],
			],
			"counterColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-counter{color:{{counterColor}}; }']]
			),
			'counterTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-counter'],],
			],
			"contentColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-description{color:{{contentColor}}; }']]
			),
			'contentTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .gb-all-locations .location-boxes .rtcl-description'
					],
				],
			],
			"container_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {padding:{{container_padding}};}']]
			),
			"container_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {margin:{{container_margin}};}']]
			),
			"containerBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {background-color:{{containerBGColor}};}']]
			),
			"locations" => array(
				"type" => "array",
			),
			"location_type" => array(
				"type" => "string",
				"default" => "all",
			),
			"show_count" => array(
				"type" => "boolean",
				"default" => true,
			),
			"count_position" => array(
				"type" => "string",
				"default" => "inline",
			),
			"count_after_text" => array(
				"type" => "string",
			),
			"orderby" => array(
				"type" => "string",
				"default" => "name",
			),
			"sortby" => array(
				"type" => "string",
				"default" => "asc",
			),
			"hide_empty" => array(
				"type" => "boolean",
				"default" => false,
			),
			"show_desc" => array(
				"type" => "boolean",
				"default" => true,
			),
			"show_sub_location" => array(
				"type" => "boolean",
				"default" => true,
			),
			"enable_link" => array(
				"type" => "boolean",
				"default" => true,
			),
			"enable_nofollow" => array(
				"type" => "boolean",
				"default" => false,
			),
			"enable_parent" => array(
				"type" => "boolean",
				"default" => false,
			),
			"sub_location_limit" => array(
				"type" => "number",
				"default" => 4,
			),
			"location_limit" => array(
				"type" => "number",
				"default" => 4,
			),
			"desc_limit" => array(
				"type" => "number",
				"default" => 20,
			),
			"col_xl" => array(
				"type" => "string",
				"default" => "3",
			),
			"col_lg" => array(
				"type" => "string",
				"default" => "3",
			),
			"col_md" => array(
				"type" => "string",
				"default" => "4",
			),
			"col_sm" => array(
				"type" => "string",
				"default" => "6",
			),
			"col_mobile" => array(
				"type" => "string",
				"default" => "12",
			),
		);

		if ($default) {
			$temp = [];
			foreach ($attributes as $key => $value) {
				if (isset($value['default'])) {
					$temp[$key] = $value['default'];
				}
			}
			return $temp;
		} else {
			return $attributes;
		}
	}

	public function __construct()
	{
		add_action('init', [$this, 'all_location_content']);
	}

	public function all_location_content()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/all-location',
			[
				'render_callback' => [$this, 'render_callback_listings'],
				'attributes' => $this->get_attributes(),
			]
		);
	}

	public function render_callback_listings($attributes)
	{
		$settings = $attributes;
		$view = isset($settings['col_style']['style']) ? $settings['col_style']['style'] : 'grid';
		$style = '1';
		if ('grid' == $view) {
			$style = isset($settings['col_style']['style_grid']) ? $settings['col_style']['style_grid'] : '1';
		}

		$data = array(
			'template' => 'block/all-location/' . $view . '/style-' . $style,
			'view' => $view,
			'style' => $style,
			'settings' => $settings,
			'terms' => AdminAjaxController::rtcl_gb_all_location_query($settings),
			'default_template_path' => null,
		);

		$data = apply_filters('rtcl_gb_all_location_box_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
