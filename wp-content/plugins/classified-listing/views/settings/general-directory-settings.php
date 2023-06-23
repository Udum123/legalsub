<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings for Payment
 */
$options = array(
    'ls_section'             => [
        'title'       => esc_html__('Directory Settings', 'classified-listing'),
        'type'        => 'title',
        'description' => '',
    ],
    'enable_business_hours'  => [
        'title' => esc_html__('Enable Business Hours', 'classified-listing'),
        'type'  => 'checkbox',
        'label' => esc_html__('Enable business hours', 'classified-listing')
    ],
    'enable_social_profiles' => [
        'title' => esc_html__('Enable Social Profiles', 'classified-listing'),
        'type'  => 'checkbox',
        'label' => esc_html__('Enable social profiles', 'classified-listing')
    ]
);

return apply_filters('rtcl_general_directory_settings_options', $options);
