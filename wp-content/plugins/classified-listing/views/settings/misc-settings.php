<?php

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for misc
 */
$options = [
	'img_gallery_section'          => [
		'title' => esc_html__( 'Image Sizes', 'classified-listing' ),
		'type'  => 'title',
	],
	'image_size_gallery'           => [
		'title'       => esc_html__( 'Galley Slider', 'classified-listing' ),
		'type'        => 'image_size',
		'default'     => [ 'width' => 800, 'height' => 380, 'crop' => 'yes' ],
		'options'     => [
			'width'  => esc_html__( 'Width', 'classified-listing' ),
			'height' => esc_html__( 'Height', 'classified-listing' ),
			'crop'   => esc_html__( 'Hard Crop', 'classified-listing' ),
		],
		'description' => esc_html__( 'This image size is being used in the image slider on Listing details pages.', "classified-listing" )
	],
	'image_size_gallery_thumbnail' => [
		'title'       => esc_html__( 'Gallery Thumbnail', 'classified-listing' ),
		'type'        => 'image_size',
		'default'     => [ 'width' => 150, 'height' => 105, 'crop' => 'yes' ],
		'options'     => [
			'width'  => esc_html__( 'Width', 'classified-listing' ),
			'height' => esc_html__( 'Height', 'classified-listing' ),
			'crop'   => esc_html__( 'Hard Crop', 'classified-listing' ),
		],
		'description' => esc_html__( 'Gallery thumbnail image size', "classified-listing" )
	],
	'image_size_thumbnail'         => [
		'title'       => esc_html__( 'Thumbnail', 'classified-listing' ),
		'type'        => 'image_size',
		'default'     => [ 'width' => 300, 'height' => 240, 'crop' => 'yes' ],
		'options'     => [
			'width'  => esc_html__( 'Width', 'classified-listing' ),
			'height' => esc_html__( 'Height', 'classified-listing' ),
			'crop'   => esc_html__( 'Hard Crop', 'classified-listing' ),
		],
		'description' => esc_html__( 'Listing thumbnail size will use all listing page', "classified-listing" )
	],
	'image_allowed_type'           => [
		'title'   => esc_html__( 'Allowed Image type', 'classified-listing' ),
		'type'    => 'multi_checkbox',
		'default' => [ 'png', 'jpeg', 'jpg' ],
		'options' => apply_filters( 'rtcl_gallery_image_support_format', [
			'png'  => esc_html__( 'PNG', 'classified-listing' ),
			'jpg'  => esc_html__( 'JPG', 'classified-listing' ),
			'jpeg' => esc_html__( 'JPEG', 'classified-listing' ),
			'webp' => esc_html__( 'WebP', 'classified-listing' ),
		] )
	],
	'image_allowed_memory'         => [
		'title'       => esc_html__( 'Allowed Image memory size', 'classified-listing' ),
		'type'        => 'number',
		'default'     => 2,
		'description' => sprintf( __( 'Enter the image memory size, like 2 for 2 MB (only number with out MB) <br><span style="color: red">Your hosting allowed maximum %s</span>',
			'classified-listing' ), Functions::formatBytes( Functions::get_wp_max_upload() ) )
	],
	'image_edit_cap'               => [
		'title'   => esc_html__( 'User can edit image', 'classified-listing' ),
		'type'    => 'checkbox',
		'default' => 'yes',
		'label'   => esc_html__( 'User can edit image size , can crop , can make feature', 'classified-listing' )
	],
	'placeholder_image'            => [
		'title' => esc_html__( 'Place holder image', 'classified-listing' ),
		'type'  => 'image',
		'label' => esc_html__( 'Select an Image to display as placeholder if have no image.', 'classified-listing' )
	],
	'single_listing_section'       => [
		'title' => esc_html__( 'Single Listing', 'classified-listing' ),
		'type'  => 'title',
	],
	'disable_gallery_slider'       => [
		'title' => esc_html__( 'Disable gallery slider', 'classified-listing' ),
		'type'  => 'checkbox',
		'label' => esc_html__( 'Disable', 'classified-listing' ),
	],
	'disable_gallery_video'        => [
		'title' => esc_html__( 'Disable gallery video', 'classified-listing' ),
		'type'  => 'checkbox',
		'label' => esc_html__( 'Disable', 'classified-listing' ),
	],
	'social_section'               => [
		'title' => esc_html__( 'Social Share buttons', 'classified-listing' ),
		'type'  => 'title',
	],
	'social_services'              => [
		'title'   => esc_html__( 'Enable services', 'classified-listing' ),
		'type'    => 'multi_checkbox',
		'default' => [ 'facebook', 'twitter' ],
		'options' => Options::social_services_options()
	],
	'social_pages'                 => [
		'title'   => esc_html__( 'Show buttons in', 'classified-listing' ),
		'type'    => 'multi_checkbox',
		'default' => [ 'listing' ],
		'options' => [
			'listing'    => esc_html__( 'Listing detail page', 'classified-listing' ),
			'listings'   => esc_html__( 'Listings page', 'classified-listing' ),
			'categories' => esc_html__( 'Categories page', 'classified-listing' ),
			'locations'  => esc_html__( 'Locations page', 'classified-listing' )
		]
	],
	'recaptcha_section'            => [
		'title' => esc_html__( 'Google reCAPTCHA', 'classified-listing' ),
		'type'  => 'title',
	],
	'recaptcha_forms'              => [
		'title'   => esc_html__( 'Enable reCAPTCHA in', 'classified-listing' ),
		'type'    => 'multi_checkbox',
		'options' => Options::get_recaptcha_form_list()
	],
	'recaptcha_version'            => [
		'title'       => esc_html__( 'reCAPTCHA version', 'classified-listing' ),
		'type'        => 'radio',
		'default'     => 2,
		'options'     => [
			3 => esc_html__( 'reCAPTCHA v3', 'classified-listing' ),
			2 => esc_html__( 'reCAPTCHA v2', 'classified-listing' ),
		],
		'description' => esc_html__( 'Google reCAPTCHA v2 will show in the form and reCAPTCHA v3 will show in the browser corner.', 'classified-listing' )
	],
	'recaptcha_site_key'           => [
		'title'       => esc_html__( 'Site key', 'classified-listing' ),
		'type'        => 'text',
		'description' => sprintf(
			'<span style="color:#c90808; font-weight: 500">%1$s</span> %2$s <a target="_blank" href="%4$s">%3$s</a>',
			esc_html__( 'Google reCAPTCHA v2 and v3, site key and secrect key will be different.', 'classified-listing' ),
			esc_html__( 'How to generate reCAPTCHA', 'classified-listing' ),
			esc_html__( 'Click here', 'classified-listing' ),
			'https://www.radiustheme.com/docs/faqs/add-re-captcha/'
		)
	],
	'recaptcha_secret_key'         => [
		'title' => esc_html__( 'Secret key', 'classified-listing' ),
		'type'  => 'text'
	],
	'map_section'                  => [
		'title' => esc_html__( 'Map', 'classified-listing' ),
		'type'  => 'title',
	],
	'map_type'                     => [
		'title'   => esc_html__( 'Map Type', 'classified-listing' ),
		'type'    => 'radio',
		'default' => 'osm',
		'options' => [
			'osm'    => esc_html__( 'OpenStreetMap', 'classified-listing' ),
			'google' => esc_html__( 'GoogleMap', 'classified-listing' ),
		]
	],
	'map_api_key'                  => [
		'title'      => esc_html__( 'Google Map API key', 'classified-listing' ),
		'type'       => 'text',
		'dependency' => [
			'rules' => [
				"input[id^=rtcl_misc_settings-map_type]" => [
					'type'  => 'equal',
					'value' => 'google'
				]
			]
		]
	],
	'map_zoom_level'               => [
		'title'   => esc_html__( 'Map zoom level', 'classified-listing' ),
		'type'    => 'select',
		'default' => 10,
		'options' => [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18 ]
	],
	'map_center'                   => [
		'title'       => esc_html__( 'Map default location', 'classified-listing' ),
		'type'        => 'map_center',
		'description' => 'google' === Functions::get_map_type() ? wp_kses( __( '<span style="color: red">Map Api key is required.</span>', 'classified-listing' ), [
			'span' => [
				'style' => [ 'color' ]
			]
		] ) : ''
	],
	'maxmind_section'              => [
		'title'       => esc_html__( 'MaxMind Geolocation', 'classified-listing' ),
		'description' => esc_html__( 'An integration for utilizing MaxMind to do Geolocation lookups. Please note that this integration will only do country lookups.', 'classified-listing' ),
		'type'        => 'title',
	],
	'maxmind_license_key'          => [
		'title'       => __( 'MaxMind License Key', 'classified-listing' ),
		'type'        => 'password',
		'description' => sprintf(
		/* translators: %1$s: Documentation URL */
			__(
				'The key that will be used when dealing with MaxMind Geolocation services. You can read how to generate one in <a href="%1$s">MaxMind Geolocation Integration documentation</a>.',
				'classified-listing'
			),
			'https://docs.woocommerce.com/document/maxmind-geolocation-integration/'
		),
		'default'     => '',
	],
	'maxmind_database_path'        => [
		'title'       => __( 'Database File Path', 'classified-listing' ),
		'type'        => 'html',
		'html'        => sprintf( '<strong>%s</strong>', $this->maxMindDatabaseService()->get_database_path() ),
		'description' => esc_html__( 'The location that the MaxMind database should be stored. By default, the integration will automatically save the database here.', 'classified-listing' )
	],
];

return apply_filters( 'rtcl_misc_settings_options', $options );