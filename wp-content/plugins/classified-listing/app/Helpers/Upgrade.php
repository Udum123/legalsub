<?php


namespace Rtcl\Helpers;


use Rtcl\Models\Roles;

class Upgrade
{

    static function init() {
        add_action('init', [__CLASS__, 'run_upgrade']);
    }

    public static function run_upgrade() {
//        self::upgrade_to_1_5_5();
//        self::upgrade_to_1_5_59();
    }

    public static function upgrade_to_1_5_59() {
        $old_version = get_option('rtcl_pro_version');
        if ($old_version && version_compare($old_version, '1.5.59') < 0) {
            Roles::remove_default_caps();
            Roles::create_roles();
            update_option('rtcl_queue_flush_rewrite_rules', 'yes');
            self::update_rtcl_version('1.5.59');
        }
    }

    public static function upgrade_to_1_5_5() {
        $old_version = get_option('rtcl_pro_version');
        if ($old_version && version_compare($old_version, '1.5.5') < 0) {
            if ($listings_page_id = Functions::get_page_id('listings')) {
                $my_post = array(
                    'ID'           => $listings_page_id,
                    'post_content' => ''
                );
                wp_update_post($my_post);
            }
            update_option('rtcl_queue_flush_rewrite_rules', 'yes');
            self::update_rtcl_version('1.5.5');
        }
    }

    static function update_rtcl_version($version = '') {
        $version = $version ?: RTCL_VERSION;
        delete_option('rtcl_pro_version');
        add_option('rtcl_pro_version', $version);
    }
}