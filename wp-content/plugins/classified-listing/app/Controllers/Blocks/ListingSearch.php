<?php

/**
 * Main Gutenberg ListingSearch Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace Rtcl\Controllers\Blocks;

use Rtcl\Helpers\Functions;

class ListingSearch
{
	protected $name = 'rtcl/listing-search';

	protected $attributes = [];


	public function get_attributes($default = false)
	{
		$attributes = array(
			'blockId'      => array(
				'type'    => 'string',
				'default' => '',
			),
			"search_style" => array(
				"type" => "string",
				"default" => "dependency"
			),
			'search_oriantation' => array(
				'type'    => 'string',
				'default' => 'inline',
			),
			'fields_label' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'location_field' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'geo_location_range' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'keyword_field' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'category_field' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'types_field' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'price_field' => array(
				'type'    => 'boolean',
				'default' => false,
			),

			"wrapper_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
			),
			"wrapper_border_radius" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
			),
			"wrapper_bg_color" => array(
				"type" => "string",
				"default" => ""
			),
			'label_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .show-field-label .form-group label']
				],
			],
			"label_text_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-widget-search .show-field-label .form-group label { color:{{label_text_color}}; }'
				]]
			),
			"field_text_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { color:{{field_text_color}}; }',
					],
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { --search-placeholder-color::{{field_text_color}}; }',
					]
				]
			),
			"field_bg_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { background-color:{{field_bg_color}}; }'
				]]
			),
			"field_gutter_space" => array(
				"type" => "number",
				"default" => 15,
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-widget-search.rtcl-widget-search-inline .rtcl-widget-search-form .row { gap:{{field_gutter_space}}px; }'
					],
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-widget-search.rtcl-widget-search-vertical .rtcl-widget-search-form .row { gap:{{field_gutter_space}}px; }'
					]
				]
			),
			"field_height" => array(
				"type" => "number",
				"default" => 50,
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control,
					{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] { height:{{field_height}}px; }'
				]]
			),
			"field_border_type" => array(
				"type" => "string",
				"default" => "solid",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { border-style:{{field_border_type}}; }'
				]]
			),
			"field_border" => array(
				"type" => "string",
				"default" => "1px",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { border-width:{{field_border}} !important; }'
				]]
			),
			"field_border_color" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)[
					'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { border-color:{{field_border_color}} !important; }'
				]]
			),
			"field_border_radius" => array(
				"type" => "object",
				'default' => (object)['top' => '5', 'bottom' => '5', 'left' => '5', 'right' => '5', 'unit' => 'px'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .form-control { border-radius:{{field_border_radius}}; }'
					]
				]
			),

			"button_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {padding:{{button_padding}};}']]
			),
			"button_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {margin:{{button_margin}};}']]
			),
			"button_width" => array(
				"type" => "number",
				"default" => '',
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search.rtcl-widget-search-inline .ws-button {min-width:{{button_width}}px;}']]
			),

			'button_typo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '14', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'uppercase', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit]']
				],
			],
			"button_color_style" => array(
				"type" => "string",
				"default" => "normal"
			),
			"button_bg_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {background-color:{{button_bg_color}};}']]
			),
			"button_text_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {color:{{button_text_color}};}']]
			),
			"button_border_type" => array(
				"type" => "string",
				"default" => "none",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {border-style:{{button_border_type}};}']]
			),
			"button_border" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {border-width:{{button_border}};}']]
			),
			"button_border_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] {border-color:{{button_border_color}};}']]
			),
			"button_border_radius" => array(
				"type" => "object",
				'default' => (object)['top' => '5', 'bottom' => '5', 'left' => '5', 'right' => '5', 'unit' => 'px'],
				'style' => [
					(object)[
						'selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit] { border-radius:{{button_border_radius}}; }'
					]
				]
			),
			"hv_button_bg_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit]:hover {background-color:{{hv_button_bg_color}};}']]
			),
			"hv_button_text_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit]:hover {color:{{hv_button_text_color}};}']]
			),
			"hv_button_border_type" => array(
				"type" => "string",
				"default" => "none",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit]:hover {border-style:{{hv_button_border_type}};}']]
			),
			"hv_button_border" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit]:hover {border-width:{{hv_button_border}};}']]
			),
			"hv_button_border_color" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl-gb-widget-search .rtcl-widget-search-form .btn[type=submit]:hover {border-color:{{hv_button_border_color}};}']]
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
		add_action('init', [$this, 'register_listing_serch_form']);
	}

	public function register_listing_serch_form()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/listing-search',
			[
				'render_callback' => [$this, 'render_callback_search'],
				'attributes' => $this->get_attributes(),
			]
		);
	}

	/**
	 * Activete field count
	 *
	 * @param [type] $settings all settings.
	 * @return int
	 */
	private function active_count($settings)
	{
		$active_count = 1;
		if ($settings['keyword_field']) {
			$active_count++;
		}
		if ($settings['location_field']) {
			$active_count++;
		}
		if ($settings['category_field']) {
			$active_count++;
		}
		if ($settings['types_field']) {
			$active_count++;
		}
		if ($settings['price_field']) {
			$active_count++;
		}
		return $active_count;
	}

	public function render_callback_search($attributes)
	{
		$settings = $attributes;

		$search_style       = isset($settings['search_style']) ? $settings['search_style'] : 'dependency';
		$search_oriantation = !empty($settings['search_oriantation']) ? $settings['search_oriantation'] : 'inline';

		$template_style = 'block/listing-search/search';

		$data = array(
			'id'                    => wp_rand(),
			'template'              => $template_style,
			'style'                 => $search_style,
			'active_count'          => $this->active_count($settings),
			'selected_location'     => false,
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

		$data = apply_filters('rtcl_gb_search_widget_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
