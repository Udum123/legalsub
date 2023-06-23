<?php

namespace Rtcl\Controllers;


use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Shortcodes\Categories;
use Rtcl\Shortcodes\Checkout;
use Rtcl\Shortcodes\FilterListings;
use Rtcl\Shortcodes\ListingForm;
use Rtcl\Shortcodes\Listings;
use Rtcl\Shortcodes\MyAccount;
use WP_Query;

class Shortcodes
{

    public static function init_short_code() {
        $shortcodes = array(
            'rtcl_my_account'      => __CLASS__ . '::my_account',
            'rtcl_checkout'        => __CLASS__ . '::checkout',
            'rtcl_categories'      => __CLASS__ . '::categories',
            'rtcl_listing_form'    => __CLASS__ . '::listing_form',
            'rtcl_listings'        => __CLASS__ . '::listings',
            'rtcl_filter_listings' => __CLASS__ . '::filter_listings',
            'rtcl_listing_page'    => __CLASS__ . '::listing_page',
        );

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        }

    }

    public static function shortcode_wrapper(
        $function,
        $atts = array(),
        $wrapper = array(
            'class'  => 'rtcl',
            'before' => null,
            'after'  => null,
        )
    ) {
        ob_start();

        // @codingStandardsIgnoreStart
        echo empty($wrapper['before']) ? '<div class="' . esc_attr($wrapper['class']) . '">' : $wrapper['before'];
        call_user_func($function, $atts);
        echo empty($wrapper['after']) ? '</div>' : $wrapper['after'];

        // @codingStandardsIgnoreEnd

        return ob_get_clean();
    }

    /**
     * My account page shortcode.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */
    public static function my_account($atts) {
        return self::shortcode_wrapper([MyAccount::class, 'output'], $atts);
    }

    public static function checkout($atts) {
        return self::shortcode_wrapper([Checkout::class, 'output'], $atts);
    }

    public static function categories($atts) {
        return self::shortcode_wrapper([Categories::class, 'output'], $atts);
    }

    public static function listing_form($atts) {
        return self::shortcode_wrapper([ListingForm::class, 'output'], $atts);
    }

    public static function listings($atts) {
        $atts = (array)$atts;
        $type = 'listings';

        $shortcode = new Listings($atts, $type);
        return $shortcode->get_content();
    }

    public static function filter_listings($atts) {
        return self::shortcode_wrapper([FilterListings::class, 'output'], $atts);
    }


    /**
     * Show a single listing page.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */
    public static function listing_page($atts) {
        if (empty($atts)) {
            return '';
        }

        if (!isset($atts['id'])) {
            return '';
        }

        $args = array(
            'posts_per_page'      => 1,
            'post_type'           => rtcl()->post_type,
            'post_status'         => (!empty($atts['status'])) ? $atts['status'] : 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows'       => 1,
        );

        if (isset($atts['id'])) {
            $args['p'] = absint($atts['id']);
        }

        // Don't render titles if desired.
        if (isset($atts['show_title']) && !$atts['show_title']) {
            remove_action('rtcl_single_listing_content', [TemplateHooks::class, 'add_single_listing_title'], 5);
        }

        $single_listing = new WP_Query($args);

        // For "is_single" to always make load comments_template() for reviews.
        $single_listing->is_single = true;

        ob_start();

        global $wp_query;

        // Backup query object so following loops think this is a product page.
        $previous_wp_query = $wp_query;
        // @codingStandardsIgnoreStart
        $wp_query = $single_listing;
        // @codingStandardsIgnoreEnd

        wp_enqueue_script('rtcl-single-listing');

        while ($single_listing->have_posts()) {
            $single_listing->the_post()
            ?>
            <div class="single-listing">
                <?php Functions::get_template_part('content', 'single-rtcl_listing'); ?>
            </div>
            <?php
        }

        // Restore $previous_wp_query and reset post data.
        // @codingStandardsIgnoreStart
        $wp_query = $previous_wp_query;
        // @codingStandardsIgnoreEnd
        wp_reset_postdata();

        return '<div class="rtcl">' . ob_get_clean() . '</div>';
    }

}