<?php

use Rtcl\Resources\Options;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Settings for Payment
 */
$options = array(
	'ls_section' => array(
		'title'       => esc_html__('Listing settings', 'classified-listing'),
		'type'        => 'title',
		'description' => '',
	),

	'load_bootstrap'               => array(
		'title'       => esc_html__('Bootstrap options', 'classified-listing'),
		'type'        => 'multi_checkbox',
		'default'     => array('css', 'js'),
		'description' => esc_html__("This plugin uses bootstrap 4. Disable these options if your theme already include them.",
			"classified-listing"),
		'options'     => array(
			'css' => esc_html__('Include bootstrap CSS', 'classified-listing'),
			'js'  => esc_html__('Include bootstrap javascript libraries', 'classified-listing')
		)
	),
	'include_results_from'         => array(
		'title'   => esc_html__('Include results from', 'classified-listing'),
		'type'    => 'multi_checkbox',
		'default' => array('child_categories', 'child_locations'),
		'options' => array(
			'child_categories' => esc_html__('Child categories', 'classified-listing'),
			'child_locations'  => esc_html__('Child locations', 'classified-listing')
		)
	),
	'listings_per_page'            => array(
		'title'       => esc_html__('Listings per page', 'classified-listing'),
		'type'        => 'number',
		'default'     => 20,
		'css'         => 'width:50px',
		'description' => esc_html__('Number of listings to show per page. Use a value of "0" to show all listings.',
			'classified-listing')
	),
	'related_posts_per_page'       => array(
		'title'       => esc_html__('Number of listing for Related Listing', 'classified-listing'),
		'type'        => 'number',
		'default'     => 4,
		'css'         => 'width:50px',
		'description' => esc_html__('Number of listings to show as related listing', 'classified-listing')
	),
	'orderby'                      => array(
		'title'   => esc_html__('Order Listing by', 'classified-listing'),
		'type'    => 'select',
		'default' => 'date',
		'options' => array(
			'title' => esc_html__('Title', 'classified-listing'),
			'date'  => esc_html__('Date posted', 'classified-listing'),
			'price' => esc_html__('Price', 'classified-listing'),
			'views' => esc_html__('Views count', 'classified-listing')
		)
	),
	'order'                        => array(
		'title'   => esc_html__('Sort listings by', 'classified-listing'),
		'type'    => 'select',
		'default' => 'desc',
		'options' => array(
			'asc'  => esc_html__('Ascending', 'classified-listing'),
			'desc' => esc_html__('Descending', 'classified-listing')
		)
	),
	'taxonomy_orderby'             => array(
		'title'   => esc_html__('Category / Location Order by', 'classified-listing'),
		'type'    => 'select',
		'default' => 'title',
		'options' => array(
			'name'        => esc_html__('Name', 'classified-listing'),
			'id'          => esc_html__('Id', 'classified-listing'),
			'count'       => esc_html__('Count', 'classified-listing'),
			'slug'        => esc_html__('Slug', 'classified-listing'),
			'_rtcl_order' => esc_html__('Custom Order', 'classified-listing'),
			'none'        => esc_html__('None', 'classified-listing'),
		),
	),
	'taxonomy_order'               => array(
		'title'   => esc_html__('Category / Location Sort by', 'classified-listing'),
		'type'    => 'select',
		'default' => 'asc',
		'options' => array(
			'asc'  => esc_html__('Ascending', 'classified-listing'),
			'desc' => esc_html__('Descending', 'classified-listing')
		)
	),
	'text_editor'                  => array(
		'title'       => esc_html__('Text Editor', 'classified-listing'),
		'type'        => 'radio',
		'default'     => 'wp_editor',
		'options'     => array(
			'wp_editor' => esc_html__('WP Editor', 'classified-listing'),
			'textarea'  => esc_html__('Textarea', 'classified-listing')
		),
		'description' => esc_html__('Listing form Editor style', 'classified-listing'),
	),
	'location_section'             => array(
		'title'       => esc_html__('Location settings', 'classified-listing'),
		'type'        => 'title',
		'description' => '',
	),
	'location_type'                => array(
		'title'   => esc_html__('Location type', 'classified-listing'),
		'type'    => 'radio',
		'default' => 'local',
		'options' => [
			'local' => esc_html__('Local (WordPress default location taxonomy)', 'classified-listing'),
			'geo'   => __('GEO Location (Set Map type => Settings > Misc > Map Type)', 'classified-listing')
		],
	),
	'location_level_first'         => array(
		'title'   => esc_html__('First level location', 'classified-listing'),
		'type'    => 'text',
		'default' => esc_html__('State', 'classified-listing'),
	),
	'location_level_second'        => array(
		'title'   => esc_html__('Second level location', 'classified-listing'),
		'type'    => 'text',
		'default' => esc_html__('City', 'classified-listing'),
	),
	'location_level_third'         => array(
		'title'   => esc_html__('Third level location', 'classified-listing'),
		'type'    => 'text',
		'default' => esc_html__('Town', 'classified-listing'),
	),
	'currency_section'             => array(
		'title'       => esc_html__('Currency Options', 'classified-listing'),
		'type'        => 'title',
		'description' => esc_html__('The following options affect how prices are displayed on the frontend.',
			'classified-listing'),
	),
	'currency'                     => array(
		'title'   => esc_html__('Currency', 'classified-listing'),
		'type'    => 'select',
		'class'   => 'rtcl-select2',
		'options' => Options::get_currencies(),
	),
	'currency_position'            => array(
		'title'   => esc_html__('Currency position', 'classified-listing'),
		'type'    => 'select',
		'class'   => 'rtcl-select2',
		'options' => Options::get_currency_positions()
	),
	'currency_thousands_separator' => array(
		'title'       => esc_html__('Thousands separator', 'classified-listing'),
		'type'        => 'text',
		'css'         => 'width:50px',
		'description' => esc_html__('The symbol (usually , or .) to separate thousands.', 'classified-listing'),
		'default'     => ','
	),
	'currency_decimal_separator'   => array(
		'title'       => esc_html__('Decimal separator', 'classified-listing'),
		'type'        => 'text',
		'css'         => 'width:50px',
		'description' => esc_html__('The symbol (usually , or .) to separate decimal points.',
			'classified-listing'),
		'default'     => '.'
	),
	'note_section'             => array(
		'title'       => esc_html__('Information', 'classified-listing'),
		'type'        => 'title',
	),
	'admin_note_to_users' => array(
		'title'       => esc_html__('Admin note to all users', 'classified-listing'),
		'type'        => 'textarea',
		'css'         => 'width:500px;min-height:100px',
		'description' => esc_html__("This information will show to all user's dashboard.", 'classified-listing')
	),
);

return apply_filters('rtcl_general_settings_options', $options);
