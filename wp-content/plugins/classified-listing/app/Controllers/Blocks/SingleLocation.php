<?php

/**
 * Main Gutenberg SingleLocation Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Blocks;

use Rtcl\Helpers\Functions;

class SingleLocation
{
	protected $name = 'rtcl/single-location';

	protected $attributes = [];


	public function get_attributes($default = false)
	{
		$attributes = array(
			'blockId'      => array(
				'type'    => 'string',
				'default' => '',
			),
			"iconName" => array(
				"type" => "string",
				"default" => "right-big"
			),
			'iconColorStyle' => array(
				'type'    => 'string',
				'default' => 'normal',
			),
			'iconColor' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3 .rtcl-gb-content > a .rtcl-icon
						{color:{{iconColor}};}'
					]
				]
			),
			'iconHoverColor' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3:hover .rtcl-gb-content > a .rtcl-icon
						{color:{{iconHoverColor}};}'
					]
				]
			),
			'iconRotate' => array(
				'type'    => 'number',
				'default' => 0,
				'style'   => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3 .rtcl-gb-content > a
						{transform:rotate({{iconRotate}}deg);}'
					]
				]
			),
			'iconHoverRotate' => array(
				'type'    => 'number',
				'default' => 0,
				'style'   => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3:hover .rtcl-gb-content > a
						{transform:rotate({{iconHoverRotate}}deg);}'
					]
				]
			),
			'iconBGColor' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3 .rtcl-gb-content > a
						{background-color:{{iconBGColor}};}'
					]
				]
			),
			'iconBGHoverColor' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3:hover .rtcl-gb-content > a
						{background-color:{{iconBGHoverColor}};}'
					]
				]
			),
			'boxBGType' => array(
				'type'    => 'string',
				'default' => 'classic',
			),
			'boxBGImgID' => array(
				'type'    => 'string',
				'default' => '',
			),
			'boxBGColor' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)[
						'depends' => [
							(object)['key' => 'boxBGType', 'condition' => '==', 'value' => 'classic'],
						],
						'selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
						{background-color:{{boxBGColor}};}'
					]
				]
			),
			'boxBGImgURL' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [(object)[
					'depends' => [
						(object)['key' => 'boxBGType', 'condition' => '==', 'value' => 'classic'],
					],
					'selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
					{background-image:url({{boxBGImgURL}});}'
				]]
			),
			'boxBGImgSize' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
					{background-size:{{boxBGImgSize}};}']]
			),
			'boxBGImgRepeat' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
					{background-repeat:{{boxBGImgRepeat}};}']]
			),
			'boxBGImgPosition' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
					{background-position:{{boxBGImgPosition}};}']]
			),
			'boxBGImgAttachment' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
					{background-attachment:{{boxBGImgAttachment}};}']]
			),
			'boxBGWith' => array(
				'type'    => 'number',
				'default' => 0,
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box
					{width:{{boxBGWith}}px;}']]
			),
			'boxBGHeight' => array(
				'type'    => 'number',
				'default' => 290,
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box
					{height:{{boxBGHeight}}px;}']]
			),
			'boxBGGradient' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [(object)[
					'depends' => [
						(object)['key' => 'boxBGType', 'condition' => '==', 'value' => 'gradient'],
					],
					'selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-img
					{background:{{boxBGGradient}};}'
				]]
			),
			'overlayBGColorStyle' => array(
				'type'    => 'string',
				'default' => 'normal',
			),
			'overlayBGGradient' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box:not(.location-box-style-3).rtcl-gb-has-count .rtcl-gb-content
					{background:{{overlayBGGradient}};}'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3 .rtcl-image-wrapper .rtcl-gb-img::before
					{background:{{overlayBGGradient}};}']
				]
			),
			'overlayHoverBGGradient' => array(
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box:not(.location-box-style-3).rtcl-gb-has-count:hover .rtcl-gb-content
					{background:{{overlayHoverBGGradient}};}'],
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box.location-box-style-3 .rtcl-image-wrapper .rtcl-gb-img::after
					{background:{{overlayHoverBGGradient}};}']
				]
			),
			"col_style" => array(
				"type" => "object",
				"default" => array(
					"style" => "1",
				),
			),
			"titleColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-content .rtcl-gb-title {color:{{titleColor}} !important;}']
				]
			),
			"titleHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box:hover .rtcl-gb-content .rtcl-gb-title a {color:{{titleHoverColor}} !important;}']
				],
			),
			'titleTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '20', 'unit' => 'px !important'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-content .rtcl-gb-title']
				],
			],
			"counterColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-counter{color:{{counterColor}};}']
				]
			),
			'counterTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px !important'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '15', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-listing-location-box .rtcl-gb-counter']
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
			"location" => array(
				"type" => "string",
			),
			"show_count" => array(
				"type" => "boolean",
				"default" => true,
			),
			"enable_link" => array(
				"type" => "boolean",
				"default" => true,
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
		add_action('init', [$this, 'register_listing_serch_form']);
	}

	public function register_listing_serch_form()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/single-location',
			[
				'render_callback' => [$this, 'render_callback_listings'],
				'attributes' => $this->get_attributes(),
			]
		);
	}

	public function render_callback_listings($attributes)
	{
		$settings = $attributes;
		$style = isset($settings['col_style']['style']) ? $settings['col_style']['style'] : '1';

		$data = array(
			'template' => 'block/single-location/style-' . $style,
			'style' => $style,
			'settings' => $settings,
			'term' => AdminAjaxController::rtcl_gb_single_location_query($settings),
			'default_template_path' => null,
		);

		$data = apply_filters('rtcl_gb_single_location_box_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
