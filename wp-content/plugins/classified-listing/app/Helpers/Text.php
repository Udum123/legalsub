<?php


namespace Rtcl\Helpers;


class Text
{

    public static function price_type_fixed() {
        return apply_filters('rtcl_text_price_type_fixed', __("Fixed", "classified-listing"));
    }

    public static function price_type_negotiable() {
        return apply_filters('rtcl_text_price_type_negotiable', __("Negotiable", "classified-listing"));
    }

    public static function price_type_on_call() {
        return apply_filters('rtcl_text_price_type_on_call', __("On Call", "classified-listing"));
    }


    public static function location_level_first() {
        $generalSettings = Functions::get_option('rtcl_general_settings');
        $text = isset($generalSettings['location_level_first']) && !empty($generalSettings['location_level_first']) ? self::string_translation($generalSettings['location_level_first']) : __('States', 'classified-listing');

        return apply_filters('rtcl_text_location_level_first', $text);
    }

    public static function location_level_second() {
        $generalSettings = Functions::get_option('rtcl_general_settings');
        $text = isset($generalSettings['location_level_second']) && !empty($generalSettings['location_level_second']) ? self::string_translation($generalSettings['location_level_second']) : __('City', 'classified-listing');

        return apply_filters('rtcl_text_location_level_second', $text);
    }

    public static function location_level_third() {
        $generalSettings = Functions::get_option('rtcl_general_settings');
        $text = isset($generalSettings['location_level_third']) && !empty($generalSettings['location_level_third']) ? self::string_translation($generalSettings['location_level_third']) : __('City', 'classified-listing');

        return apply_filters('rtcl_text_location_level_third', $text);
    }

    public static function add_to_favourite() {
        return apply_filters('rtcl_text_add_to_favourite', __('Add to Favourites', 'classified-listing'));
    }

    public static function remove_from_favourite() {
        return apply_filters('rtcl_text_remove_from_favourite', __('Remove from Favourites', 'classified-listing'));
    }

    public static function report_abuse() {
        return apply_filters('rtcl_text_report_abuse', __('Report Abuse', 'classified-listing'));
    }

    public static function string_translation($string) {

        return apply_filters('rtcl_text_string_translation', $string);
    }

    public static function get_default_registration_privacy_policy_text() {
        return apply_filters('rtcl_default_registration_privacy_policy_text', sprintf(__('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our %s.', 'classified-listing'), '[privacy_policy]'));
    }

    public static function get_default_checkout_privacy_policy_text() {
        return apply_filters('rtcl_default_checkout_privacy_policy_text', sprintf(__('Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.', 'classified-listing'), '[privacy_policy]'));
    }

    public static function get_default_terms_and_conditions_checkbox_text() {
        return apply_filters('rtcl_default_terms_and_conditions_checkbox_text', sprintf(__('I have read and agree to the website %s.', 'classified-listing'), '[terms]'));
    }

    public static function get_privacy_policy_text($type = '') {
        $text = '';

        switch ($type) {
            case 'checkout':
                $text = Functions::get_option_item('rtcl_account_settings', 'checkout_privacy_policy_text', self::get_default_checkout_privacy_policy_text());
                break;
            case 'registration':
                $text = Functions::get_option_item('rtcl_account_settings', 'registration_privacy_policy_text', self::get_default_registration_privacy_policy_text());
                break;
        }

        return trim(apply_filters('rtcl_get_privacy_policy_text', $text, $type));
    }

    static function get_terms_and_conditions_checkbox_text() {
        return trim(apply_filters('rtcl_get_terms_and_conditions_checkbox_text', Functions::get_option_item('rtcl_account_settings', 'terms_and_conditions_checkbox_text', self::get_default_terms_and_conditions_checkbox_text())));
    }


    static function get_select_location_text() {
        return apply_filters('rtcl_get_select_location_text', __("Select a location", 'classified-listing'));
    }

    static function get_select_category_text() {
        return apply_filters('rtcl_get_select_category_text', __("Select a category", 'classified-listing'));
    }

    static function get_single_listing_email_button_text(){
        return apply_filters('rtcl_single_listing_email_button_text', __("Message to Seller", "classified-listing"));
    }
}