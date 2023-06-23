<?php

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for Account page
 */
$options = [
	'enable_myaccount_registration'        => [
		'title'       => esc_html__( 'Account Creation', 'classified-listing' ),
		'type'        => 'checkbox',
		'default'     => 'yes',
		'description' => esc_html__( 'Allow visitor to create an account on the "My account" page', 'classified-listing' ),
	],
	'disable_name_phone_registration'      => [
		'title'       => esc_html__( 'Hide Name', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( 'Hide name at registration form', 'classified-listing' ),
	],
	'disable_phone_at_registration'        => [
		'title'       => esc_html__( 'Hide Phone Number', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( 'Hide phone number at registration form', 'classified-listing' ),
	],
	'required_phone_at_registration'       => [
		'title'       => esc_html__( 'Required phone', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( 'Required phone number at registration form', 'classified-listing' ),
		'dependency'  => [
			'rules' => [
				'#rtcl_account_settings-disable_phone_at_registration' => [
					'type'  => '!=',
					'value' => 'yes'
				]
			]
		]
	],
	'separate_registration_form'           => [
		'title'       => esc_html__( 'Separate Registration Form', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( 'Separate registration page from login page', 'classified-listing' ),
	],
	'user_role'                            => [
		'title'      => esc_html__( 'New User Default Role', 'classified-listing' ),
		'type'       => 'select',
		'class'      => 'rtcl-select2',
		'blank_text' => esc_html__( "Default Role as wordpress", 'classified-listing' ),
		'options'    => Functions::get_user_roles( '', [ 'administrator' ] ),
		'css'        => 'min-width:300px;'
	],
	'social_login_shortcode'               => [
		'title'       => esc_html__( 'Social Login shortcode', 'classified-listing' ),
		'type'        => 'text',
		'css'         => 'width:100%;',
		'description' => wp_kses( __( 'Add your social login shortcode, which will run at <em style="color:red">rtcl_login_form</em> hook. <br><strong style="color: green;">We will support shortcode from any third party plugin.</strong><br> <strong>Example: [TheChamp-Login], [miniorange_social_login theme="default"]</strong>', 'classified-listing' ), [
			'br'     => [],
			'em'     => [
				'style' => [ 'color' ]
			],
			'strong' => [
				'style' => [ 'color' ]
			],
		] ),
	],
	'terms_conditions_section'             => [
		'title'       => esc_html__( 'Terms and conditions', 'classified-listing' ),
		'type'        => 'title',
		'description' => '',
	],
	'enable_listing_terms_conditions'      => [
		'title'       => esc_html__( 'Enable Listing Terms and conditions', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( "Display and require user agreement to Terms and Conditions for Listing form.", 'classified-listing' )
	],
	'enable_checkout_terms_conditions'     => [
		'title'       => esc_html__( 'Enable Terms and conditions at checkout page', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( "Display and require user agreement to Terms and Conditions at checkout page.", 'classified-listing' )
	],
	'enable_registration_terms_conditions' => [
		'title'       => esc_html__( 'Enable Terms and conditions at registration', 'classified-listing' ),
		'type'        => 'checkbox',
		'description' => esc_html__( "Display and require user agreement to Terms and Conditions at registration page.", 'classified-listing' )
	],
	'page_for_terms_and_conditions'        => [
		'title'       => esc_html__( 'Terms and conditions page', 'classified-listing' ),
		'description' => esc_html__( "Choose a page to act as your Terms and conditions.", 'classified-listing' ),
		'type'        => 'select',
		'class'       => 'rtcl-select2',
		'blank_text'  => esc_html__( "Select a page", 'classified-listing' ),
		'options'     => Functions::get_pages(),
		'css'         => 'min-width:300px;'
	],
	'terms_and_conditions_checkbox_text'   => [
		'title'       => esc_html__( 'Terms and conditions', 'classified-listing' ),
		'type'        => 'textarea',
		'default'     => Text::get_default_terms_and_conditions_checkbox_text(),
		'description' => esc_html__( 'Optionally add some text for the terms checkbox that customers must accept.', 'classified-listing' )
	],
	'privacy_policy_section'               => [
		'title'       => esc_html__( 'Privacy policy', 'classified-listing' ),
		'type'        => 'title',
		'description' => esc_html__( "This section controls the display of your website privacy policy. The privacy notices below will not show up unless a privacy page is first set.", 'classified-listing' ),
	],
	'page_for_privacy_policy'              => [
		'title'       => esc_html__( 'Privacy page', 'classified-listing' ),
		'description' => esc_html__( "Choose a page to act as your privacy policy.", 'classified-listing' ),
		'type'        => 'select',
		'class'       => 'rtcl-select2',
		'blank_text'  => esc_html__( "Select a page", 'classified-listing' ),
		'options'     => Functions::get_pages(),
		'css'         => 'min-width:300px;'
	],
	'registration_privacy_policy_text'     => [
		'title'       => esc_html__( 'Registration privacy policy', 'classified-listing' ),
		'type'        => 'textarea',
		'description' => esc_html__( "Optionally add some text about your store privacy policy to show on account registration forms.", 'classified-listing' ),
		'default'     => Text::get_default_registration_privacy_policy_text()
	],
	'checkout_privacy_policy_text'         => [
		'title'       => esc_html__( 'Checkout privacy policy', 'classified-listing' ),
		'type'        => 'textarea',
		'description' => esc_html__( "Optionally add some text about your store privacy policy to show during checkout.", 'classified-listing' ),
		'default'     => Text::get_default_checkout_privacy_policy_text(),
	]
];

return apply_filters( 'rtcl_account_settings_options', $options );
