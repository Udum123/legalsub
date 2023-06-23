<?php

namespace Rtcl\Shortcodes;


use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class ListingForm
{

    /**
     * @param $atts
     */
    public static function output($atts) {
        if (!is_user_logged_in() && !Functions::is_enable_post_for_unregister()) {
            Functions::login_form();
            return;
        }
        Functions::clear_notices();

        // check if edit post
        $post_id = 'edit' == get_query_var('rtcl_action') ? absint(get_query_var('rtcl_listing_id', 0)) : 0;
        $has_permission = true;
        if ($post_id && !Functions::current_user_can('edit_' . rtcl()->post_type, $post_id)) {
            $has_permission = false;
        } else if (!is_user_logged_in() && !Functions::is_enable_post_for_unregister()) {
            $has_permission = false;
        }
        if (!$has_permission) {
            Functions::add_notice(__('You do not have sufficient permissions to access this page.', 'classified-listing'), 'error');
            Functions::print_notices();
            return;
        }

        do_action('rtcl_before_add_edit_listing_before_category_condition', $post_id);

        if (Functions::notice_count('error')) {
            Functions::print_notices();
            return;
        }

        // check category
        $category_id = 0;
        if (!$post_id) {
            $category_id = isset($_GET['category']) ? absint($_GET['category']) : 0;
            $selected_type = (isset($_GET['type']) && in_array($_GET['type'], array_keys(Functions::get_listing_types()))) ? $_GET['type'] : '';
            $category = get_term_by('id', $category_id, rtcl()->category);
            if (is_object($category)) {
                $parent_id = Functions::get_term_top_most_parent_id($category_id, rtcl()->category);
                if (Functions::term_has_children($category_id)) {
                    Functions::add_notice(__("Please select ad type and category", "classified-listing"), 'error');
                }
                if (!Functions::is_ad_type_disabled() && !$selected_type) {
                    Functions::add_notice(__("Please select an ad type", "classified-listing"), 'error');
                }
                $cats_on_type = wp_list_pluck(Functions::get_one_level_categories(0, $selected_type), 'term_id');
                if (!in_array($parent_id, $cats_on_type)) {
                    Functions::add_notice(__("Please select correct type and category", "classified-listing"), 'error');
                }
                do_action('rtcl_before_add_edit_listing_into_category_condition', $post_id, $category_id);
                if (Functions::notice_count('error')) {
                    Functions::get_template("listing-form/category", array(
                        'parent_cat_id' => Functions::get_term_top_most_parent_id($category_id, rtcl()->category),
                        'selected_type' => $selected_type
                    ));
                    return;
                }
            } else {
                Functions::get_template("listing-form/category", array(
                    'parent_cat_id' => 0,
                    'selected_type' => $selected_type
                ));
                return;
            }
        }

        do_action('rtcl_before_add_edit_listing_after_category_condition', $post_id, $category_id);

        if (Functions::notice_count('error')) {
            Functions::print_notices();
            return;
        }

        Functions::get_template("listing-form/form", compact('post_id'));

    }

}