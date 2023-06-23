<?php

namespace Rtcl\Controllers\Admin;

use Rtcl\Helpers\Functions;

class RegisterPostType
{

    public static function init() {
        add_action('init', [__CLASS__, 'register_taxonomies'], 5);
        add_action('init', [__CLASS__, 'register_post_types'], 5);
        add_action('init', [__CLASS__, 'register_post_status'], 9);
        add_action('init', [__CLASS__, 'support_jetpack_omnisearch']);
        add_filter('rest_api_allowed_post_types', [__CLASS__, 'rest_api_allowed_post_types']);
        add_action('rtcl_after_register_post_type', [__CLASS__, 'maybe_flush_rewrite_rules']);
        add_action('rtcl_flush_rewrite_rules', [__CLASS__, 'flush_rewrite_rules']);
        add_filter('gutenberg_can_edit_post_type', [__CLASS__, 'gutenberg_can_edit_post_type'], 10, 2);
        add_filter('use_block_editor_for_post_type', [__CLASS__, 'gutenberg_can_edit_post_type'], 10, 2);
    }

    public static function register_taxonomies() {
        if (!is_blog_installed() || post_type_exists(rtcl()->post_type)) {
            return;
        }
        do_action('rtcl_register_taxonomy');

        $permalinks = Functions::get_permalink_structure();

        $cat_labels = array(
            'name'                       => esc_html_x('Listing Categories', 'Taxonomy General Name', 'classified-listing'),
            'singular_name'              => esc_html_x('Category', 'Taxonomy Singular Name', 'classified-listing'),
            'menu_name'                  => esc_html__('Categories', 'classified-listing'),
            'all_items'                  => esc_html__('All Categories', 'classified-listing'),
            'parent_item'                => esc_html__('Parent Category', 'classified-listing'),
            'parent_item_colon'          => esc_html__('Parent Category:', 'classified-listing'),
            'new_item_name'              => esc_html__('New Category Name', 'classified-listing'),
            'add_new_item'               => esc_html__('Add New Category', 'classified-listing'),
            'edit_item'                  => esc_html__('Edit Category', 'classified-listing'),
            'update_item'                => esc_html__('Update Category', 'classified-listing'),
            'view_item'                  => esc_html__('View Category', 'classified-listing'),
            'separate_items_with_commas' => esc_html__('Separate Categories with commas', 'classified-listing'),
            'add_or_remove_items'        => esc_html__('Add or remove Categories', 'classified-listing'),
            'choose_from_most_used'      => esc_html__('Choose from the most used', 'classified-listing'),
            'popular_items'              => null,
            'search_items'               => esc_html__('Search Categories', 'classified-listing'),
            'not_found'                  => esc_html__('Not Found', 'classified-listing'),
        );

        $cat_args = array(
            'labels'            => $cat_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'query_var'         => true,
            'capabilities'      => array(
                'manage_terms' => 'manage_rtcl_options',
                'edit_terms'   => 'manage_rtcl_options',
                'delete_terms' => 'manage_rtcl_options',
                'assign_terms' => 'edit_' . rtcl()->post_type . 's'
            ),
            'rewrite'           => array(
                'slug'         => $permalinks['category_base'],
                'with_front'   => false,
                'hierarchical' => true,
            )
        );

        register_taxonomy(rtcl()->category, rtcl()->post_type, apply_filters('rtcl_register_listing_category_args', $cat_args));

        if ('local' === Functions::location_type()) {
            $location_labels = array(
                'name'                       => esc_html_x('Listing Locations', 'Taxonomy General Name', 'classified-listing'),
                'singular_name'              => esc_html_x('Location', 'Taxonomy Singular Name', 'classified-listing'),
                'menu_name'                  => esc_html__('Locations', 'classified-listing'),
                'all_items'                  => esc_html__('All Locations', 'classified-listing'),
                'parent_item'                => esc_html__('Parent Location', 'classified-listing'),
                'parent_item_colon'          => esc_html__('Parent Location:', 'classified-listing'),
                'new_item_name'              => esc_html__('New Location Name', 'classified-listing'),
                'add_new_item'               => esc_html__('Add New Location', 'classified-listing'),
                'edit_item'                  => esc_html__('Edit Location', 'classified-listing'),
                'update_item'                => esc_html__('Update Location', 'classified-listing'),
                'view_item'                  => esc_html__('View Location', 'classified-listing'),
                'separate_items_with_commas' => esc_html__('Separate Locations with commas', 'classified-listing'),
                'add_or_remove_items'        => esc_html__('Add or remove Locations', 'classified-listing'),
                'choose_from_most_used'      => esc_html__('Choose from the most used', 'classified-listing'),
                'popular_items'              => null,
                'search_items'               => esc_html__('Search Locations', 'classified-listing'),
                'not_found'                  => esc_html__('Not Found', 'classified-listing'),
            );

            $location_args = array(
                'labels'            => $location_labels,
                'hierarchical'      => true,
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => false,
                'query_var'         => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_rtcl_options',
                    'edit_terms'   => 'manage_rtcl_options',
                    'delete_terms' => 'manage_rtcl_options',
                    'assign_terms' => 'edit_' . rtcl()->post_type . 's'
                ),
                'rewrite'           => array(
                    'slug'         => $permalinks['location_base'],
                    'with_front'   => false,
                    'hierarchical' => true,
                )
            );
            register_taxonomy(rtcl()->location, rtcl()->post_type, apply_filters('rtcl_register_listing_location_args', $location_args));
        }


        do_action('rtcl_after_register_taxonomy');
    }

    public static function register_post_types() {

        if (!is_blog_installed() || post_type_exists(rtcl()->post_type)) {
            return;
        }

        do_action('rtcl_register_post_type');

        $permalinks = Functions::get_permalink_structure();

        $labels = array(
            'name'               => esc_html_x('Classified Listings', 'post type general name', 'classified-listing'),
            'singular_name'      => esc_html_x('Classified Listing', 'post type singular name', 'classified-listing'),
            'add_new'            => esc_html_x('Add New', 'post', 'classified-listing'),
            'add_new_item'       => esc_html__('Add New Listing', 'classified-listing'),
            'edit_item'          => esc_html__('Edit Listing', 'classified-listing'),
            'new_item'           => esc_html__('New Listing', 'classified-listing'),
            'all_items'          => esc_html__('All Listings', 'classified-listing'),
            'view_item'          => esc_html__('View Listing', 'classified-listing'),
            'search_items'       => esc_html__('Search Listing', 'classified-listing'),
            'not_found'          => esc_html__('No Listings found', 'classified-listing'),
            'not_found_in_trash' => esc_html__('No Listing found in the Trash', 'classified-listing'),
            'name_admin_bar'     => esc_html__('Listing', 'classified-listing'),
            'update_item'        => esc_html__('Update Listing', 'classified-listing'),
            'parent_item_colon'  => '',
            'menu_name'          => esc_html__('Classified Listing', 'classified-listing')
        );
        $listing_support = array('title', 'editor', 'author');
        $moderation_settings = Functions::get_option('rtcl_moderation_settings');
        if (!empty($moderation_settings['has_comment_form'])) {
            array_push($listing_support, 'comments');
        }
        $listings_page_id = Functions::get_page_id('listings');

        if (current_theme_supports('rtcl')) {
            $has_archive = $listings_page_id && get_post($listings_page_id) ? urldecode(get_page_uri($listings_page_id)) : 'listings';
        } else {
            $has_archive = false;
        }


        // If theme support changes, we may need to flush permalinks since some are changed based on this flag.
        $theme_support = current_theme_supports('rtcl') ? 'yes' : 'no';
        if (get_option('current_theme_supports_rtcl') !== $theme_support && update_option('current_theme_supports_rtcl', $theme_support)) {
            update_option('rtcl_queue_flush_rewrite_rules', 'yes');
        }

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'menu_icon'           => RTCL_URL . '/assets/images/icon-20x20.png',
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'supports'            => $listing_support,
            'hierarchical'        => false,
            'rewrite'             => $permalinks['listing_base'] ? array(
                'slug'       => $permalinks['listing_base'],
                'with_front' => false,
                'feeds'      => true,
            ) : false,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => $has_archive,
            'show_in_rest'        => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => rtcl()->post_type,
            'map_meta_cap'        => true
        );

        register_post_type(rtcl()->post_type, apply_filters('rtcl_register_listing_post_type_args', $args));

        $cf_group_labels = array(
            'name'               => esc_html__('Custom Fields', 'classified-listing'),
            'singular_name'      => esc_html__('Custom Fields', 'classified-listing'),
            'add_new'            => esc_html__('Add New', 'classified-listing'),
            'add_new_item'       => esc_html__('Add New Field Group', 'classified-listing'),
            'edit_item'          => esc_html__('Edit Field Group', 'classified-listing'),
            'new_item'           => esc_html__('New Field Group', 'classified-listing'),
            'view_item'          => esc_html__('View Field Group', 'classified-listing'),
            'search_items'       => esc_html__('Search Field Groups', 'classified-listing'),
            'not_found'          => esc_html__('No Field Groups found', 'classified-listing'),
            'not_found_in_trash' => esc_html__('No Field Groups found in Trash', 'classified-listing'),
        );

        register_post_type(rtcl()->post_type_cfg,
            apply_filters('rtcl_register_custom_field_group_args',
                array(
                    'labels'       => $cf_group_labels,
                    'public'       => false,
                    'show_ui'      => true,
                    '_builtin'     => false,
                    'hierarchical' => true,
                    'taxonomies'   => array('rtcl_category'),
                    'rewrite'      => false,
                    'query_var'    => "rtcl_cfg",
                    'supports'     => array(
                        'title',
	                    'page-attributes'
                    ),
                    'show_in_menu' => 'edit.php?post_type=' . rtcl()->post_type,
                    'capabilities' => array(
                        'edit_post'          => 'manage_rtcl_options',
                        'read_post'          => 'manage_rtcl_options',
                        'delete_post'        => 'manage_rtcl_options',
                        'edit_posts'         => 'manage_rtcl_options',
                        'edit_others_posts'  => 'manage_rtcl_options',
                        'delete_posts'       => 'manage_rtcl_options',
                        'publish_posts'      => 'manage_rtcl_options',
                        'read_private_posts' => 'manage_rtcl_options'
                    )
                )
            )
        );

        register_post_type(rtcl()->post_type_cf,
            apply_filters('rtcl_register_listing_custom_field_args',
                array(
                    'label'        => esc_html__('Custom Field', 'classified-listing'),
                    'public'       => false,
                    'hierarchical' => false,
                    'supports'     => false,
                    'rewrite'      => false,
                    'capabilities' => array(
                        'edit_post'          => 'manage_rtcl_options',
                        'read_post'          => 'manage_rtcl_options',
                        'delete_post'        => 'manage_rtcl_options',
                        'edit_posts'         => 'manage_rtcl_options',
                        'edit_others_posts'  => 'manage_rtcl_options',
                        'delete_posts'       => 'manage_rtcl_options',
                        'publish_posts'      => 'manage_rtcl_options',
                        'read_private_posts' => 'manage_rtcl_options'
                    ),
                )
            )
        );

        $payment_labels = array(
            'name'               => esc_html_x('Payment History', 'Post Type General Name', 'classified-listing'),
            'singular_name'      => esc_html_x('Payment', 'Post Type Singular Name', 'classified-listing'),
            'menu_name'          => esc_html__('Payment History', 'classified-listing'),
            'name_admin_bar'     => esc_html__('Payment', 'classified-listing'),
            'all_items'          => esc_html__('Payment History', 'classified-listing'),
            'add_new_item'       => esc_html__('Add New Payment', 'classified-listing'),
            'add_new'            => esc_html__('Add New', 'classified-listing'),
            'new_item'           => esc_html__('New Payment', 'classified-listing'),
            'edit_item'          => esc_html__('Edit Payment', 'classified-listing'),
            'update_item'        => esc_html__('Update Payment', 'classified-listing'),
            'view_item'          => esc_html__('View Payment', 'classified-listing'),
            'search_items'       => esc_html__('Search Payment', 'classified-listing'),
            'not_found'          => esc_html__('No payments found', 'classified-listing'),
            'not_found_in_trash' => esc_html__('No payments found in Trash', 'classified-listing'),
        );

        $payment_args = array(
            'label'               => esc_html__('Payments', 'classified-listing'),
            'description'         => esc_html__('Post Type Description', 'classified-listing'),
            'labels'              => $payment_labels,
            'supports'            => array('title', 'comments', 'custom-fields'),
            'taxonomies'          => array(''),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=' . rtcl()->post_type,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => rtcl()->post_type_payment,
            'map_meta_cap'        => true
        );

        $pricing_labels = array(
            'name'               => esc_html_x('Pricing', 'Post Type General Name', 'classified-listing'),
            'singular_name'      => esc_html_x('Pricing', 'Post Type Singular Name', 'classified-listing'),
            'menu_name'          => esc_html__('Pricing', 'classified-listing'),
            'name_admin_bar'     => esc_html__('Pricing', 'classified-listing'),
            'all_items'          => esc_html__('Pricing', 'classified-listing'),
            'add_new_item'       => esc_html__('Add New Pricing', 'classified-listing'),
            'add_new'            => esc_html__('Add New', 'classified-listing'),
            'new_item'           => esc_html__('New Pricing', 'classified-listing'),
            'edit_item'          => esc_html__('Edit Pricing', 'classified-listing'),
            'update_item'        => esc_html__('Update Pricing', 'classified-listing'),
            'view_item'          => esc_html__('View Pricing', 'classified-listing'),
            'search_items'       => esc_html__('Search Pricing', 'classified-listing'),
            'not_found'          => esc_html__('No Pricing found', 'classified-listing'),
            'not_found_in_trash' => esc_html__('No Pricing found in Trash', 'classified-listing'),
        );

        $pricing_args = array(
            'labels'            => $pricing_labels,
            'public'            => false,
            'show_ui'           => true,
            'supports'          => array('title', 'page-attributes'),
            'show_in_menu'      => 'edit.php?post_type=' . rtcl()->post_type,
            'show_in_admin_bar' => true,
            'has_archive'       => false,
            'capability_type'   => rtcl()->post_type_pricing,
            'map_meta_cap'      => true
        );
        $payment_settings = Functions::get_option('rtcl_payment_settings');

        if (!empty($payment_settings['payment']) && $payment_settings['payment'] == 'yes') {
            register_post_type(rtcl()->post_type_payment, apply_filters('rtcl_register_payment_post_type_args', $payment_args));
            register_post_type(rtcl()->post_type_pricing, apply_filters('rtcl_register_pricing_post_type_args', $pricing_args));
        }

        do_action('rtcl_after_register_post_type');
    }

    public static function register_post_status() {
        register_post_status('rtcl-reviewed', array(
            'label'       => esc_html_x('Reviewed', 'post', 'classified-listing'),
            'public'      => is_admin(),
            'internal'    => false,
            'label_count' => _n_noop('Review <span class="count">(%s)</span>',
                'Review <span class="count">(%s)</span>', 'classified-listing')
        ));

        register_post_status('rtcl-expired', array(
            'label'       => esc_html_x('Expired', 'post', 'classified-listing'),
            'public'      => is_admin(),
            'internal'    => false,
            'label_count' => _n_noop('Expired <span class="count">(%s)</span>',
                'Expired <span class="count">(%s)</span>', 'classified-listing')
        ));

        register_post_status('rtcl-temp', array(
            'label'                  => esc_html_x('Temporary', 'post', 'classified-listing'),
            'public'                 => false,
            'internal'               => false,
            'show_in_admin_all_list' => false,
            'label_count'            => _n_noop('Temporary <span class="count">(%s)</span>',
                'Temporary <span class="count">(%s)</span>', 'classified-listing')
        ));

        register_post_status('rtcl-pending', array(
            'label'                     => esc_html_x('Pending payment', 'pending status payment', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Pending payment <span class="count">(%s)</span>',
                'Pending payment <span class="count">(%s)</span>', 'classified-listing'),
        ));

        register_post_status('rtcl-created', array(
            'label'                     => esc_html_x('Created', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Created <span class="count">(%s)</span>',
                'Created <span class="count">(%s)</span>', 'classified-listing'),
        ));

        register_post_status('rtcl-completed', array(
            'label'                     => esc_html_x('Completed', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Completed <span class="count">(%s)</span>',
                'Completed <span class="count">(%s)</span>', 'classified-listing'),
        ));

        register_post_status('rtcl-failed', array(
            'label'                     => esc_html_x('Failed', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Failed <span class="count">(%s)</span>',
                'Failed <span class="count">(%s)</span>', 'classified-listing'),
        ));

        register_post_status('rtcl-cancelled', array(
            'label'                     => esc_html_x('Cancelled', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Cancelled <span class="count">(%s)</span>',
                'Cancelled <span class="count">(%s)</span>', 'classified-listing'),
        ));

        register_post_status('rtcl-refunded', array(
            'label'                     => esc_html_x('Refunded', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Refunded <span class="count">(%s)</span>',
                'Refunded <span class="count">(%s)</span>', 'classified-listing'),
        ));

        register_post_status('rtcl-on-hold', array(
            'label'                     => esc_html_x('On hold', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('On hold <span class="count">(%s)</span>',
                'Refunded <span class="count">(%s)</span>', 'classified-listing'),
        ));
        register_post_status('rtcl-processing', array(
            'label'                     => esc_html_x('Processing', 'Payment status', 'classified-listing'),
            'public'                    => is_admin(),
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Processing <span class="count">(%s)</span>',
                'Refunded <span class="count">(%s)</span>', 'classified-listing'),
        ));
    }


    /**
     * Add Product Support to Jetpack Omnisearch.
     */
    public static function support_jetpack_omnisearch() {
        if (class_exists('Jetpack_Omnisearch_Posts')) {
            new Jetpack_Omnisearch_Posts(rtcl()->post_type);
        }
    }

    /**
     * Added product for Jetpack related posts.
     *
     * @param array $post_types Post types.
     *
     * @return array
     */
    public static function rest_api_allowed_post_types($post_types) {
        $post_types[] = rtcl()->post_type;

        return $post_types;
    }

    /**
     * Flush rewrite rules.
     */
    public static function flush_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Flush rules if the event is queued.
     *
     */
    public static function maybe_flush_rewrite_rules() {
        if ('yes' === get_option('rtcl_queue_flush_rewrite_rules')) {
            update_option('rtcl_queue_flush_rewrite_rules', 'no');
            self::flush_rewrite_rules();
        }
    }

    /**
     * Disable Gutenberg for products.
     *
     * @param bool   $can_edit  Whether the post type can be edited or not.
     * @param string $post_type The post type being checked.
     *
     * @return bool
     */
    public static function gutenberg_can_edit_post_type($can_edit, $post_type) {
        return rtcl()->post_type === $post_type ? false : $can_edit;
    }

}
