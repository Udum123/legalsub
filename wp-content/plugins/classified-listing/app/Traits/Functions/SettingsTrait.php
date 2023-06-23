<?php


namespace Rtcl\Traits\Functions;


use Rtcl\Helpers\Functions;

trait SettingsTrait
{
    static function get_privacy_policy_page_id() {
        $page_id = Functions::get_option_item('rtcl_account_settings', 'page_for_privacy_policy', 0);

        return apply_filters('rtcl_privacy_policy_page_id', 0 < $page_id ? absint($page_id) : 0);
    }

    static function get_terms_and_conditions_page_id() {
        $page_id = Functions::get_option_item('rtcl_account_settings', 'page_for_terms_and_conditions', 0);

        return apply_filters('rtcl_terms_and_conditions_page_id', 0 < $page_id ? absint($page_id) : 0);
    }

}
