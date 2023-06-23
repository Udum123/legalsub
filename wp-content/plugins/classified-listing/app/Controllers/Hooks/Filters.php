<?php

namespace Rtcl\Controllers\Hooks;

class Filters
{

    public static function beforeUpload() {
        add_filter('upload_dir', [__CLASS__, 'custom_upload_dir']);
    }

    public static function afterUpload() {
        remove_filter('upload_dir', [__CLASS__, 'custom_upload_dir']);
    }

    public static function custom_upload_dir($dirs) {
        $custom_dir = '/' . rtcl()->upload_directory . $dirs['subdir'];
        $dirs['subdir'] = $custom_dir;
        $dirs['path'] = $dirs['basedir'] . $custom_dir;
        $dirs['url'] = $dirs['baseurl'] . $custom_dir;
        return $dirs;
    }

}