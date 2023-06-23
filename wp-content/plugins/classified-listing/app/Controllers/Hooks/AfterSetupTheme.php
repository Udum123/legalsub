<?php

namespace Rtcl\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;

class AfterSetupTheme
{
    static function template_functions() {
        add_action('template_redirect', [__CLASS__, 'template_redirect']);
        add_action('template_redirect', [__CLASS__, 'send_frame_options_header']);
        add_action('template_redirect', [__CLASS__, 'prevent_adjacent_posts_rel_link_wp_head']);
        add_action('the_post', [__CLASS__, 'setup_listing_data']);
    }

    static function template_redirect() {
        $redirect_url = '';
        global $wp;

        // When default permalinks are enabled, redirect listings page to post type archive url.
        if (!empty($_GET['page_id']) && '' === get_option('permalink_structure') && Functions::get_page_id('listings') === absint($_GET['page_id']) && get_post_type_archive_link(rtcl()->post_type)) { // WPCS: input var ok, CSRF ok.
            wp_safe_redirect(get_post_type_archive_link(rtcl()->post_type));
            exit;
        }
        // Logout
        if (isset($wp->query_vars['logout']) && !empty($_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'logout')) {
            wp_safe_redirect(str_replace('&amp;', '&', wp_logout_url(Link::get_my_account_page_link())));
            exit;
        }

        // Redirect
        if (!empty($redirect_url)) {
            wp_redirect($redirect_url);
            exit();
        }
    }

    /**
     * When loading sensitive checkout or account pages, send a HTTP header to limit rendering of pages to same origin iframes for security reasons.
     *
     * @since  1.5.4
     */
    static function send_frame_options_header() {
        if (Functions::is_checkout_page() || Functions::is_account_page()) {
            send_frame_options_header();
        }
    }

    /**
     * Remove adjacent_posts_rel_link_wp_head - pointless for products.
     *
     * @since 1.5.4
     */
    static function prevent_adjacent_posts_rel_link_wp_head() {
        if (is_singular(rtcl()->post_type)) {
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        }
    }


    /**
     * When the_post is called, put product data into a global.
     *
     * @param mixed $post Post Object.
     *
     * @return Listing | false
     */
    static function setup_listing_data($post) {
        unset($GLOBALS['listing']);

        if (is_int($post)) {
            $post = get_post($post);
        }

        if (empty($post->post_type) || rtcl()->post_type !== $post->post_type) {
            return false;
        }

        $GLOBALS['listing'] = rtcl()->factory->get_listing($post);

        return $GLOBALS['listing'];
    }

}
