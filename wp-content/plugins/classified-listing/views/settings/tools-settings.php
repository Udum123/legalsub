<?php

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings for Tools
 */

$options = array(
    'data_management_section' => array(
        'title'       => esc_html__('Data Management', 'classified-listing'),
        'type'        => 'title',
        'description' => sprintf(__('You can remove all classified listing cache from here. <a href="%s">Clear all cache</a>', 'classified-listing'), add_query_arg([
            rtcl()->nonceId    => wp_create_nonce(rtcl()->nonceText),
            'clear_rtcl_cache' => ''
        ], Link::get_current_url()))
    ),
    'delete_all_data'         => array(
        'title'       => esc_html__('Delete all data', 'classified-listing'),
        'type'        => 'checkbox',
        'description' => esc_html__('Allow to delete all all listing data during delete this plugin', 'classified-listing'),
    )
);
return apply_filters('rtcl_tools_settings_options', $options);