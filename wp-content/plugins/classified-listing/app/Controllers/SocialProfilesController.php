<?php

namespace Rtcl\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Resources\Options;

class SocialProfilesController
{

    public static function init() {
        if (Functions::is_enable_social_profiles()) {
            add_filter('postbox_classes_' . rtcl()->post_type . '_rtcl_social_profiles', [
                __CLASS__,
                'add_meta_box_classes'
            ]);
            add_action('rtcl_listing_details_meta_box', [__CLASS__, 'add_social_profiles_meta_box']);
            add_action('save_post_' . rtcl()->post_type, [__CLASS__, 'save_social_profiles'], 10);
            add_action('rtcl_listing_form_after_save_or_update', [__CLASS__, 'update_social_profile_at_save_or_update'], 10, 5);

            if (rtcl()->is_request('frontend')) {
                add_action("rtcl_listing_form", [__CLASS__, 'listing_social_profiles'], 40);
                add_action("rtcl_single_listing_social_profiles", [__CLASS__, 'display_social_profiles']);
            }
        }
    }

    public static function display_social_profiles( $listing = false ) {
        if (!$listing) global $listing;
		if ( ! $listing ) return; 
        $social_profiles = get_post_meta($listing->get_id(), '_rtcl_social_profiles', true);
        $social_profiles = !empty($social_profiles) && is_array($social_profiles) ? $social_profiles : [];
        if (empty($social_profiles)) return;
        Functions::get_template('listing/social-profiles', compact('social_profiles'));
    }


    public static function listing_social_profiles($post_id) {
        $social_profiles = get_post_meta($post_id, '_rtcl_social_profiles', true);
        $social_profiles = !empty($social_profiles) && is_array($social_profiles) ? $social_profiles : [];
        Functions::get_template("listing-form/social-profiles", compact('post_id', 'social_profiles'));
    }

    /**
     * @param Listing  $listing
     * @param          $type
     * @param          $cat_id
     * @param          $new_listing_status
     * @param string[] $request_data
     */
    static function update_social_profile_at_save_or_update($listing, $type, $cat_id, $new_listing_status, $request_data = ['data' => '']) {
        $data = $request_data['data'];
        if (is_a($listing, Listing::class) && isset($data['rtcl_social_profiles']) && is_array($data['rtcl_social_profiles'])) {
            $raw_profiles = $data['rtcl_social_profiles'];
            $social_list = Options::get_social_profiles_list();
            $profiles = [];
            foreach ($social_list as $item => $value) {
                if (!empty($raw_profiles[$item])) {
                    $profiles[$item] = esc_url_raw($raw_profiles[$item]);
                }
            }
            if (!empty($profiles)) {
                update_post_meta($listing->get_id(), '_rtcl_social_profiles', $profiles);
            } else {
                delete_post_meta($listing->get_id(), '_rtcl_social_profiles');
            }
        }
    }

    /**
     * @param integer $post_id
     *
     * @return integer|mixed|void
     */
    static function save_social_profiles($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the logged in user has permission to edit this post
        if (!current_user_can('edit_' . rtcl()->post_type, $post_id)) {
            return $post_id;
        }
        if (!Functions::verify_nonce()) {
            return $post_id;
        }
        if (isset($_POST['rtcl_social_profiles']) && is_array($_POST['rtcl_social_profiles'])) {
            $raw_profiles = $_POST['rtcl_social_profiles'];
            $social_list = Options::get_social_profiles_list();
            $profiles = [];
            foreach ($social_list as $item => $value) {
                if (!empty($raw_profiles[$item])) {
                    $profiles[$item] = esc_url_raw($raw_profiles[$item]);
                }
            }
            if (!empty($profiles)) {
                update_post_meta($post_id, '_rtcl_social_profiles', $profiles);
            } else {
                delete_post_meta($post_id, '_rtcl_social_profiles');
            }
        }
    }


    /**
     * @param \WP_Post $post
     */
    static function social_profiles_meta_box($post) {
        $post_id = $post->ID;
        $social_profiles = get_post_meta($post_id, '_rtcl_social_profiles', true);
        $social_profiles = !empty($social_profiles) && is_array($social_profiles) ? $social_profiles : [];
        Functions::get_template("listing-form/social-profiles", compact('post_id', 'social_profiles'));
    }


    static function add_social_profiles_meta_box($post) {
        add_meta_box(
            'rtcl_social_profiles',
            esc_html__('Social Profiles', 'classified-listing'),
            [__CLASS__, 'social_profiles_meta_box'],
            rtcl()->post_type,
            'normal',
            'high'
        );
    }

    /**
     * @param array $classes
     *
     * @return array
     */
    static function add_meta_box_classes($classes = array()) {
        array_push($classes, sanitize_html_class('rtcl'));

        return $classes;
    }
}