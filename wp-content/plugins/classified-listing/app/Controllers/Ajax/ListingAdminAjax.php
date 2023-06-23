<?php

namespace Rtcl\Controllers\Ajax;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use Rtcl\Models\RtclEmail;

class ListingAdminAjax
{

    public function __construct() {

        add_action('wp_ajax_rtcl_custom_fields_listings', array($this, 'ajax_callback_custom_fields'), 10, 2);
        add_action('wp_ajax_rtcl_get_sub_location_options', array($this, 'ajax_callback_get_location_for_contact'));

        add_action('wp_ajax_nopriv_rtcl_get_sub_location_options',
            array($this, 'ajax_callback_get_location_for_contact'));
        add_action('wp_ajax_rtcl_delete_temp_listing', array($this, 'delete_temp_listing'));
        add_action('wp_ajax_nopriv_rtcl_delete_temp_listing', array($this, 'delete_temp_listing'));

        // Send email to user by moderator
        add_action('wp_ajax_rtcl_send_email_to_user_by_moderator', array($this, 'send_email_to_user_by_moderator'));

        // Price unit
        add_action('wp_ajax_rtcl_get_price_units_ajax', array($this, 'rtcl_get_price_units_ajax_cb'));
    }

    function rtcl_get_price_units_ajax_cb() {
        wp_send_json(array(
            'html' => isset($_POST['term_id']) ? Functions::get_listing_form_price_unit_html(absint($_POST['term_id'])) : ''
        ));
    }

    function send_email_to_user_by_moderator() {
        $error = true;
        $class = "rtcl-flash-warn";
        if (Functions::verify_nonce()) {
            $post_id = !empty($_POST['post_id']) ? absint($_POST['post_id']) : 0;
            $message = !empty($_POST['message']) ? esc_textarea($_POST['message']) : '';
            $post = get_post($post_id);
            if ($post && $message) {
                $data['message'] = $message;
                $is_send = rtcl()->mailer()->emails['Listing_Moderation_Email_To_Owner']->trigger($post_id, $data);
                if ($is_send) {
                    $notification = absint(get_post_meta($post_id, '_notification_by_moderator', true)) + 1;
                    update_post_meta($post_id, '_notification_by_moderator', $notification);
                    $error = false;
                    $class = "rtcl-flash-success";
                    $message = esc_html__("Successfully sent", "classified-listing");
                    do_action("rtcl_sent_email_to_user_by_moderator", $post_id);
                } else {
                    $message = esc_html__("Error!!", "classified-listing");
                }
            } else {
                $message = esc_html__("Please select all field", "classified-listing");
            }
        } else {
            $message = esc_html__("Session Expired", "classified-listing");
        }
        wp_send_json(array(
            "error"   => $error,
            "message" => $message,
            "class"   => $class
        ));
    }

    function delete_temp_listing() {

        // TODO: check_ajax_referer( 'my-special-string', 'security' );

        $id = Functions::request("id");
        $post = get_post($id);
        if ($post === null || rtcl()->post_type !== $post->post_type || $post->post_status != Functions::get_temp_listing_status()) {
            wp_send_json([
                'result' => 0,
                'error'  => esc_html__("Post with given ID does not exist.", "classified-listing")
            ]);
        }

        $param = array('post_parent' => $id, 'post_type' => 'attachment', 'suppress_filters' => false);
        $children = get_posts($param);

        if (is_array($children)) {
            foreach ($children as $attch) {
                Functions::delete_post($attch->ID);
            }
        }

        Functions::delete_post($id);
        wp_send_json([
            'result' => 1
        ]);
    }

    function ajax_callback_get_location_for_contact() {
		do_action('rtcl_set_local');
        $term_id = absint($_POST['term_id']);
        $locations = '';
        if ($term_id) {
            $locs = Functions::get_one_level_locations($term_id);
            if (!empty($locs)) {
                $locations .= isset($_POST['blank']) ? sprintf('<option>%s</option>', esc_html(Text::get_select_location_text())) : '';
                foreach ($locs as $loc) {
                    $locations .= "<option value='{$loc->term_id}'>{$loc->name}</option>";
                }
            }
        }
        wp_send_json(array(
            'locations' => $locations
        ));
    }

    function ajax_callback_custom_fields($post_id = 0, $term_id = 0) {

        $ajax = false;

        if (isset($_POST['term_id'])) {
            $ajax = true;
            $post_id = absint($_POST['post_id']);
            $term_id = absint($_POST['term_id']);
        }
        $customFields = Functions::get_custom_fields_html($term_id, $post_id);


        if ($ajax) {
            $childCats = Functions::get_one_level_categories($term_id);
            $child_cats = null;
            if (!empty($childCats)) {
                $child_cats .= "<option value=''>" . Text::get_select_category_text() . "</option>";
                foreach ($childCats as $child_cat) {
                    $child_cats .= "<option value='{$child_cat->term_id}'>{$child_cat->name}</option>";
                }
            }
            wp_send_json(array(
                'custom_fields' => $customFields,
                'child_cats'    => $child_cats
            ));
        } else {
            echo $customFields;
        }

    }

}