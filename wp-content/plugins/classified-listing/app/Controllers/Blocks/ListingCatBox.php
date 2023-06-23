<?php

/**
 * Main Gutenberg ListingCatBox Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Blocks;

use Rtcl\Helpers\Functions;

class ListingCatBox
{
	protected $name = 'rtcl/listing-cat-box';

	protected $attributes = [];

	public function get_attributes($default = false)
	{
		$attributes = array(
			'blockId'      => array(
				'type'    => 'string',
				'default' => '',
			),
			"cats" => array(
				"type" => "array",
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
			"count_child" => array(
				"type" => "boolean",
				"default" => true,
			),
			"count_after_text" => array(
				"type" => "string",
			),
			"enable_parent" => array(
				"type" => "boolean",
				"default" => false,
			),
			"align" => array(
				"type" => "string",
				"default" => "center",
			),
			"content_limit" => array(
				"type" => "number",
				"default" => 12,
			),
			"category_limit" => array(
				"type" => "number",
				"default" => 8,
			),
			"sub_category_limit" => array(
				"type" => "number",
				"default" => 5,
			),
			"icon_type" => array(
				"type" => "string",
				"default" => "icon",
			),
			"image_size" => array(
				"type" => "string",
				"default" => "rtcl-thumbnail",
			),
			"custom_image_width" => array(
				"type" => "number",
				"default" => 400,
			),
			"custom_image_height" => array(
				"type" => "number",
				"default" => 280,
			),
			"col_desktop" => array(
				"type" => "string",
				"default" => "4",
			),
			"col_tablet" => array(
				"type" => "string",
				"default" => "2",
			),
			"col_mobile" => array(
				"type" => "string",
				"default" => "1",
			),

			"col_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{padding:{{col_padding}};}']
				]
			),
			"col_style" => array(
				"type" => "object",
				"default" => array(
					"style" => "1",
				),
			),
			"colGutterSpace" => array(
				"type" => "string",
				"default" => 30,
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-grid-view{gap:{{colGutterSpace}}px !important; }']
				]
			),
			'colBGColorStyle' => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'colBGColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{ background-color:{{colBGColor}} !important; }']
				]
			],
			'colBGHoverColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box:hover
					{ background-color:{{colBGHoverColor}} !important; }']
				]
			],
			'colBorderColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{ border-color:{{colBorderColor}} !important; }']
				]
			],
			'colBorderWith' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{border-width:{{colBorderWith}} !important; }']
				]
			],
			'colBorderStyle' => [
				'type'    => 'string',
				'default' => 'solid',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{ border-style:{{colBorderStyle}}; }']
				]
			],
			'colBorderRadius' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{ border-radius:{{colBorderRadius}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box
					{ overflow: hidden;}']
				]
			],
			'colBoxShadowStyle'  => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'colBoxShadow' => [
				'type' => 'object',
				'default' => (object)['openShadow' => 1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1], 'color' => '', 'inset' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box']
				],
			],
			'colBoxShadowHover' => [
				'type' => 'object',
				'default' => (object)['openShadow' => 1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1], 'color' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box:hover']
				],
			],
			'headerColorStyle' => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'headerBGColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-box-head
					{ background-color:{{headerBGColor}} !important; }']
				]
			],
			'headerHoverBGColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2:hover .rtcl-box-head
					{ background-color:{{headerHoverBGColor}} !important; }']
				]
			],
			'headerBDColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-box-head
					{ border-color:{{headerBDColor}} !important; }']
				]
			],
			'headerHoverBDColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2:hover .rtcl-box-head
					{ border-color:{{headerHoverBDColor}} !important; }']
				]
			],
			"header_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-box-head
					{padding:{{header_padding}};}']
				]
			),
			"content_visibility" => array(
				"type" => "object",
				"default" => array(
					"icon" => true,
					"subCat" => true,
					"catDesc" => true,
					"counter" => true,
					"contentAlign" => "",
				),
			),
			'iconColorStyle' => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'iconColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-icon .rtcl-icon
					{ color:{{iconColor}} !important; }']
				]
			],
			'iconHoverColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box:hover .item-icon .rtcl-icon
					{ color:{{iconHoverColor}} !important; }']
				]
			],
			'iconBGColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{background-color:{{iconBGColor}} !important; }']
				]
			],
			'iconFontSize' => [
				'type'    => 'number',
				'default' => 32,
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon .rtcl-icon
					{ font-size:{{iconFontSize}}px !important; }']
				]
			],
			'iconArea' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{ width:{{iconArea}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{ height:{{iconArea}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{ display:inline-block; }']
				]
			],
			'iconBorderColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{ border-color:{{iconBorderColor}}; }']
				]
			],
			'iconBorderWith'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{border-width:{{iconBorderWith}}; }']
				]
			],
			'iconBorderStyle'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{ border-style:{{iconBorderStyle}}; }']
				]
			],
			'iconBorderRadius'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-1 .item-icon
					{ border-radius:{{iconBorderRadius}} !important; }']
				]
			],
			"titleColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"titleColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .title a{color:{{titleColor}} !important;}']
				]
			),
			"titleHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .title a:hover {color:{{titleHoverColor}} !important;}']
				],
			),
			'titleTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '18', 'unit' => 'px !important'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .title']
				],
			],
			"title_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .title{margin:{{title_margin}};}']
				],
			),
			"counter_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .counter {margin:{{counter_margin}};}']
				],
			),
			"counterColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"counterColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .counter{color:{{counterColor}};}']
				]
			),
			"counterHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box:hover .item-content .counter {color:{{counterHoverColor}};}']
				],
			),
			"counterFontSize" => array(
				"type" => "number",
				"default" => 14,
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .counter{font-size:{{counterFontSize}}px;}']
				]
			),
			'contentTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .content'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .content']
				],
			],
			"contentColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"contentColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .content,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .content
					{color:{{contentColor}} !important;}']
				]
			),
			"contentHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box:hover .content
					{color:{{contentHoverColor}};}']
				],
			),
			"content_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box  .content,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box .item-content .content
					{margin:{{content_margin}};}']
				],
			),
			"subCatColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"subCatColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-sub-cats li
					{color:{{subCatColor}};}']
				]
			),
			"subCatHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-sub-cats li:hover
					{color:{{subCatHoverColor}};}']
				],
			),
			"subCatIconColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-sub-cats li .rtcl-icon
					{color:{{subCatIconColor}};}']
				]
			),
			"subCatIconHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-sub-cats li:hover .rtcl-icon
					{color:{{subCatIconHoverColor}};}']
				],
			),
			"subCatFontSize" => array(
				"type" => "number",
				"default" => 15,
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-sub-cats li{font-size:{{subCatFontSize}}px;}']
				]
			),
			"sub_cat_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-cat-wrap .rtcl-gb-cat-box.rtcl-gb-cat-box-2 .rtcl-box-body {padding:{{sub_cat_padding}};}']
				],
			),
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
		add_action('init', array($this, 'register_listing_cat_box'));
	}

	public function register_listing_cat_box()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/listing-cat-box',
			array(
				'render_callback' => array($this, 'render_callback_listing_cat_box'),
				'attributes' => $this->get_attributes(),
			)
		);
	}

	public function render_callback_listing_cat_box($attributes)
	{
		$settings = $attributes;
		$style = isset($settings['col_style']['style']) ? $settings['col_style']['style'] : '1';

		$data = array(
			'template' => 'block/category-box/style-' . $style,
			'style' => $style,
			'settings' => $settings,
			'terms' => AdminAjaxController::rtcl_cat_box_query($settings),
			'default_template_path' => null,
		);

		$data = apply_filters('rtcl_gb_category_box_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
