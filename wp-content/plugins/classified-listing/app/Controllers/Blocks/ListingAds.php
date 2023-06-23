<?php

/**
 * Main Gutenberg ListingAds Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Blocks;

use Rtcl\Helpers\Functions;

class ListingAds
{
	protected $name = 'rtcl/listing-ads';

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
			"locations" => array(
				"type" => "array",
			),
			"listing_type" => array(
				"type" => "string",
				"default" => "all",
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

			"promotion_in" => array(
				"type" => "array",
			),
			"promotion_not_in" => array(
				"type" => "array",
			),
			"orderby" => array(
				"type" => "string",
				"default" => "date",
			),
			"sortby" => array(
				"type" => "string",
				"default" => "desc",
			),
			"perPage" => array(
				"type" => "number",
				"default" => 8,
			),
			"offset" => array(
				"type" => "number",
				"default" => 0,
			),
			"align" => array(
				"type" => "string",
			),

			"col_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{padding:{{col_padding}};}']
				]
			),
			"content_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content
					{padding:{{content_padding}};}'],
				]
			),
			"col_style" => array(
				"type" => "object",
				"default" => array(
					"style" => "list",
					"style_list" => "1",
					"style_grid" => "1",
				),
			),
			"colGutterSpace" => array(
				"type" => "string",
				"default" => 30,
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item {margin-bottom:{{colGutterSpace}}px !important; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-grid-view{gap:{{colGutterSpace}}px !important; }']
				]
			),
			'colBGColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{ background-color:{{colBGColor}} !important; }']
				]
			],
			'colBorderColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{ border-color:{{colBorderColor}} !important; }']
				]
			],
			'colBorderWith'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{border-width:{{colBorderWith}} !important; }']
				]
			],
			'colBorderStyle'      => [
				'type'    => 'string',
				'default' => 'solid',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{ border-style:{{colBorderStyle}} !important; }']
				]
			],
			'colBorderRadius'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item 
					{ border-radius:{{colBorderRadius}}; }']
				]
			],
			'colBoxShadowStyle'      => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'colBoxShadow' => [
				'type' => 'object',
				'default' => (object)['openShadow' => 1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1], 'color' => '', 'inset' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item']
				],
			],
			'colBoxShadowHover' => [
				'type' => 'object',
				'default' => (object)['openShadow' => 1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1], 'color' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item:hover'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item:hover']
				],
			],
			'pfeaturedBDColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item.is-featured{ border-color:{{pfeaturedBDColor}} !important; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured{ border-color:{{pfeaturedBDColor}} !important; }']
				]
			],
			'pfeaturedBGColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item.is-featured{ background-color:{{pfeaturedBGColor}} !important; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured{ background-color:{{pfeaturedBGColor}} !important; }'],
				]
			],
			'ptopBDColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item.is-top{ border-color:{{ptopBDColor}} !important; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-top{ border-color:{{ptopBDColor}} !important; }']
				]
			],
			'ptopBGColor'  => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item.is-top{ background-color:{{ptopBGColor}} !important; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-top{ background-color:{{ptopBGColor}} !important; }']
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
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-title a{color:{{titleColor}} !important;}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title a{color:{{titleColor}} !important;}']
				]
			),
			"titleHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-title a:hover {color:{{titleHoverColor}} !important;}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title a:hover {color:{{titleHoverColor}} !important;}']
				],
			),
			'titleTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '18', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-title'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title']
				],
			],
			"title_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-title {margin:{{title_margin}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title {margin:{{title_margin}};}']
				]
			),
			"bnewBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-new{background-color:{{bnewBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-new{background-color:{{bnewBGColor}};}']
				]
			),
			"bnewColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-new{color:{{bnewColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-new{color:{{bnewColor}};}']
				]
			),
			"bfeaturedBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-featured{background-color:{{bfeaturedBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-featured{background-color:{{bfeaturedBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item.is-featured .listing-thumb:after { background-color:{{bfeaturedBGColor}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured .listing-thumb:after { background-color:{{bfeaturedBGColor}}; }']
				]
			),
			"bfeaturedColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-featured{color:{{bfeaturedColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-featured{color:{{bfeaturedColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item.is-featured .listing-thumb:after { color:{{bfeaturedColor}}; }'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured .listing-thumb:after { color:{{bfeaturedColor}}; }']
				]
			),
			"btopBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-_top{background-color:{{btopBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-_top{background-color:{{btopBGColor}};}']
				]
			),
			"btopColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-_top{color:{{btopColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-_top{color:{{btopColor}};}']
				]
			),
			"bbumpBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-list-view .badge.rtcl-badge-_bump_up{background-color:{{bbumpBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-grid-view .badge.rtcl-badge-_bump_up{background-color:{{bbumpBGColor}};}']
				]
			),
			"bbumpColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-list-view .badge.rtcl-badge-_bump_up{color:{{bbumpColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-grid-view .badge.rtcl-badge-_bump_up{color:{{bbumpColor}};}']
				]
			),
			"bpopularBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-popular.popular-badge.badge-success{background-color:{{bpopularBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-popular.popular-badge.badge-success{background-color:{{bpopularBGColor}};}']
				]
			),
			"bpopularColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-popular.popular-badge.badge-success{color:{{bpopularColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-popular.popular-badge.badge-success{color:{{bpopularColor}};}']
				]
			),
			"badge_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge {padding:{{badge_padding}} !important;}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge {padding:{{badge_padding}} !important;}']
				]
			),
			"badge_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge {margin:{{badge_margin}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge {margin:{{badge_margin}};}']
				]
			),
			'badgeTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '13', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '13', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '600'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-badge-wrap .badge'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge']
				],
			],
			"soldBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-sold-out{background-color:{{soldBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-sold-out{background-color:{{soldBGColor}};}']
				]
			),
			"soldColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-sold-out{color:{{soldColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-sold-out{color:{{soldColor}};}']
				]
			),
			'soldTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '14', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '14', 'unit' => 'px'], 'transform' => 'uppercase', 'weight' => '600'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-sold-out'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-sold-out']
				],
			],
			"metaColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"metaColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-listing-meta-data li,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data li{color:{{metaColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .right-content .rtcl-listing-meta-data,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .right-content .rtcl-listing-meta-data{color:{{metaColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-4 .right-content .rtcl-listing-type,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-list-style-4 .right-content .rtcl-listing-type{color:{{metaColor}};}'],
				]
			),
			"metaHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item:hover .rtcl-listing-meta-data li,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item:hover .rtcl-listing-meta-data li{color:{{metaHoverColor}};}']]
			),
			"metaIconColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-listing-meta-data li .rtcl-icon,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .right-content .rtcl-listing-meta-data i,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-4 .right-content .rtcl-listing-type .rtcl-icon,
					
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data li .rtcl-icon
					{color:{{metaIconColor}};}']
				]
			),
			"metaIconHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item:hover .rtcl-listing-meta-data li .rtcl-icon,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item:hover .rtcl-listing-meta-data li .rtcl-icon{color:{{metaIconHoverColor}};}']]
			),
			"metaCatColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-cat a,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-cat a{color:{{metaCatColor}};}']]
			),
			"metaCatHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .listing-cat a:hover,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-cat a:hover{color:{{metaCatHoverColor}};}']]
			),
			'metaTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-listing-meta-data li'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data li'],
				],
			],
			"meta_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-listing-meta-data,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data
				{margin:{{meta_margin}};}']]
			),

			"priceColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-3 .item-price .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-price-range .sep,

					
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-range .sep,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .rtcl-price-amount
					{color:{{priceColor}};}']

				]
			),
			"priceBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-list-view.rtcl-gb-list-style-3 .item-price,

					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .item-price
					{background-color:{{priceBGColor}};}']

				]
			),
			"priceFontSize" => array(
				"type" => "string",
				"default" => 22,
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-price-range .sep,

				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-range .sep,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price.on_call
				{font-size:{{priceFontSize}}px;}']]
			),
			"priceFontWeight" => array(
				"type" => "string",
				"default" => 600,
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .rtcl-price-amount
				{font-weight:{{priceFontWeight}};}']]
			),
			"unitLabelColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view span.rtcl-price-meta,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-3 .item-price .rtcl-price-meta,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-price .rtcl-price-meta,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .rtcl-price-meta
					{color:{{unitLabelColor}};}']
				]
			),
			"unitLFSize" => array(
				"type" => "string",
				"default" => 15,
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view span.rtcl-price-meta,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-price .rtcl-price-meta,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price .rtcl-price-meta
				{font-size:{{unitLFSize}}px;}']]
			),
			"unitLFSizeWeight" => array(
				"type" => "string",
				"default" => 500,
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view span.rtcl-price-meta,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-price .rtcl-price-meta,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .rtcl-price-meta
				{font-weight:{{unitLFSizeWeight}};}']]
			),
			"price_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .item-price,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .item-price
				{margin:{{price_margin}};}']]
			),
			"price_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
			),
			"btnColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"detailsBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-details a{background-color:{{detailsBGColor}};}']]
			),
			"detailsBGHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-details a:hover{background-color:{{detailsBGHoverColor}};}']]
			),
			"detailsColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-details a{color:{{detailsColor}};}']]
			),
			"detailsHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-details a:hover{color:{{detailsHoverColor}};}']]
			),
			"actionTextColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-meta-buttons-withtext .rtcl-text-gb-button a{color:{{actionTextColor}};}']
				]
			),
			"actionTextHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-meta-buttons-withtext .rtcl-text-gb-button a:hover{color:{{actionTextHoverColor}};}']]
			),

			"btnBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-4 .right-content .rtcl-gb-fill-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-5 .right-content .rtcl-phn a,

					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a
					{background-color:{{btnBGColor}};}']
				]
			),
			"btnBGHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-4 .right-content .rtcl-gb-fill-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-5 .right-content .rtcl-phn a:hover,

					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a:hover
					{background-color:{{btnBGHoverColor}};}'],
				]
			),
			"btnColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-4 .right-content .rtcl-gb-fill-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-5 .right-content .rtcl-phn a,

					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a .rtcl-icon,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a
					{color:{{btnColor}};}'],
				]
			),
			"btnHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-4 .right-content .rtcl-gb-fill-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view.rtcl-gb-list-style-5 .right-content .rtcl-phn a:hover,

					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a:hover .rtcl-icon,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a:hover
					{color:{{btnHoverColor}};}'],
				]
			),
			"btnBorderColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a{border-color:{{btnBorderColor}};}']]
			),
			"btnHoverBorderColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a:hover{border-color:{{btnHoverBorderColor}};}']]
			),
			"contentColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-excerpt{color:{{contentColor}};}']]
			),
			'contentTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-excerpt'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-excerpt']
				],
			],
			"content_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-list-view .listing-item .item-content .rtcl-excerpt,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-excerpt 
				{margin:{{content_margin}};}']]
			),
			"container_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {padding:{{container_padding}} !important;}']]
			),
			"container_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {margin:{{container_margin}} !important;}']]
			),
			"containerBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {background-color:{{containerBGColor}} !important;}']]
			),
			"content_visibility" => array(
				"type" => "object",
				"default" => array(
					"badge" => true,
					"location" => true,
					"category" => true,
					"date" => true,
					"price" => true,
					"author" => true,
					"view" => true,
					"content" => true,
					"grid_content" => false,
					"title" => true,
					"thumbnail" => true,
					"listing_type" => true,
					"thumb_position" => "",
					"details_btn" => true,
					"favourit_btn" => true,
					"phone_btn" => true,
					"compare_btn" => true,
					"quick_btn" => true,
					"sold" => true,
					"pagination" => false,
					"actionLayout" => "horizontal-layout",

				),
			),

			"pagination_wrap_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
				{{RTCL}}.rtcl-block-frontend .pagination {padding:{{pagination_wrap_padding}};}']]
			),
			"pagination_wrap_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
					{{RTCL}}.rtcl-block-frontend .pagination 
					{margin:{{pagination_wrap_margin}};}']
				]
			),
			"pagination_number_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item .page-link,
					{{RTCL}}.rtcl-block-frontend .pagination .page-item .page-link
					{padding:{{pagination_number_padding}};}']
				]
			),
			"pagiWrapBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
				{{RTCL}}.rtcl-block-frontend .pagination 
				{background-color:{{pagiWrapBGColor}};}']]
			),
			"pagiWrapBDColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
				{{RTCL}}.rtcl-block-frontend .pagination 
				{border-color:{{pagiWrapBDColor}} !important;}']]
			),
			"pagiWrapBDWidth" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
				{{RTCL}}.rtcl-block-frontend .pagination 
				{border-width:{{pagiWrapBDWidth}} !important;}']]
			),
			"pagiWrapBDStyle" => array(
				"type" => "string",
				"default" => "solid",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
				{{RTCL}}.rtcl-block-frontend .pagination 
				{border-style:{{pagiWrapBDStyle}} !important;}']]
			),
			"pagiWrapBDRadius" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination,
				{{RTCL}}.rtcl-block-frontend .pagination 
				{border-radius:{{pagiWrapBDRadius}};}']]
			),
			"pagiNumColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"pagiNumTextColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item .page-link,
				{{RTCL}}.rtcl-block-frontend .pagination .page-item .page-link
				{color:{{pagiNumTextColor}};}']]
			),
			"pagiNumTextHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item .page-link:hover,
				{{RTCL}}.rtcl-block-frontend .pagination .page-item .page-link:hover
				{color:{{pagiNumTextHoverColor}};}']]
			),
			"pagiNumBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item .page-link,
				{{RTCL}}.rtcl-block-frontend .pagination .page-item .page-link
				{background-color:{{pagiNumBGColor}};}']]
			),
			"pagiNumBGHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item .page-link:hover,
				{{RTCL}}.rtcl-block-frontend .pagination .page-item .page-link:hover
				{background-color:{{pagiNumBGHoverColor}};}']]
			),
			"pagiActiveTextColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item.active .page-link,
				{{RTCL}}.rtcl-block-frontend .pagination .page-item.active .page-link
				{color:{{pagiActiveTextColor}};}']]
			),
			"pagiActiveBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor .pagination .page-item.active .page-link,
				{{RTCL}}.rtcl-block-frontend .pagination .page-item.active .page-link
				{background-color:{{pagiActiveBGColor}};}']]
			),
			"content_limit" => array(
				"type" => "number",
				"default" => 20,
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
		add_action('init', [$this, 'register_listings']);
	}

	public function register_listings()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/listing-ads',
			[
				'render_callback' => [$this, 'render_callback_listings'],
				'attributes' => $this->get_attributes(),
			]
		);
	}
	public function render_callback_listings($attributes)
	{

		$settings  = $attributes;
		$the_loops = ListingsAjaxController::rtcl_gb_listings_query($settings);
		$view = isset($settings['col_style']['style']) ? $settings['col_style']['style'] : 'list';

		$style = '1';
		if ('list' === $view) {
			$style = isset($settings['col_style']['style_list']) ? $settings['col_style']['style_list'] : '1';
		}
		if ('grid' === $view) {
			$style = isset($settings['col_style']['style_grid']) ? $settings['col_style']['style_grid'] : '1';
		}
		$data = array(
			'template'              => 'block/listing-ads/' . $view . '/style-' . $style,
			'view'                  => $view,
			'style'                 => $style,
			'instance'              => $settings,
			'the_loops'             => $the_loops,
			'default_template_path' => null,
		);
		$data            = apply_filters('rtcl_gb_listing_filter_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
