<?php

namespace Rtcl\Controllers\Hooks;


use Rtcl\Helpers\Functions;
use Rtcl\Shortcodes\Listings;
use WP_Post;

class TemplateLoader
{

    /**
     * Listing page ID.
     *
     * @var integer
     */
    private static $listings_page_id = 0;


    /**
     * Store whether we're processing a listing inside the_content filter.
     *
     * @var boolean
     */
    private static $in_content_filter = false;

    /**
     * Is ClassifiedListing support defined?
     *
     * @var boolean
     */
    private static $theme_support = false;

    static function init() {
        self::$theme_support = current_theme_supports('rtcl');
        self::$listings_page_id = Functions::get_page_id('listings');
        if (self::$theme_support) {
            add_filter('template_include', [__CLASS__, 'template_loader']);
        } else {
            // Unsupported themes.
            add_action('template_redirect', array(__CLASS__, 'unsupported_theme_init'));
        }
    }

    /**
     *
     * @param string $template Template to load.
     *
     * @return string
     */
    public static function template_loader($template) {
	    
        if (is_embed()) {
            return $template;
        }

        $default_file = self::get_template_loader_default_file();

        if ($default_file) {

            $search_files = self::get_template_loader_files($default_file);

            $template = locate_template($search_files);

            if (!$template) {
                $fallback = rtcl()->plugin_path() . "/templates/" . $default_file;
                $template = file_exists($fallback) ? $fallback : '';
                $template = apply_filters('rtcl_template_loader_fallback_file', $template, $default_file);
            }

        }

        return $template;
    }

    private static function get_template_loader_default_file() {
        $default_file = '';

        if (is_singular(rtcl()->post_type)) {
            $default_file = 'single-' . rtcl()->post_type . '.php';
        } elseif (Functions::is_listing_taxonomy()) {
            $object = get_queried_object();
            if (is_tax(rtcl()->category) || is_tax(rtcl()->location)) {
                $default_file = 'taxonomy-' . $object->taxonomy . '.php';
            } else {
                $default_file = 'archive-' . rtcl()->post_type . '.php';
            }
        } elseif (is_post_type_archive(rtcl()->post_type) || (($listing_page_id = Functions::get_page_id('listings')) && is_page($listing_page_id))) {
            $default_file = 'archive-' . rtcl()->post_type . '.php';
        } elseif(is_author()) {
	        $default_file = 'author-' . rtcl()->post_type . '.php';
        }
        $default_file = apply_filters('rtcl_template_loader_default_file', $default_file);

        return $default_file;
    }

    private static function get_template_loader_files($default_file) {

        if (is_page_template()) {
            $templates[] = get_page_template_slug();
        }

        if (is_singular(rtcl()->post_type)) {
            $object = get_queried_object();
            $name_decoded = urldecode($object->post_name);
            if ($name_decoded !== $object->post_name) {
                $templates[] = "single-" . rtcl()->post_type . "-{$name_decoded}.php";
            }
            $templates[] = "single-" . rtcl()->post_type . "-{$object->post_name}.php";
        }

        $templates = [
            $default_file,
            rtcl()->get_template_path() . $default_file,
            rtcl()->get_template_path() . "listings/" . $default_file // TODO : Need to remove this backward support
        ];

        $templates = apply_filters('rtcl_template_loader_files', $templates, $default_file);

        return array_unique($templates);
    }

    /**
     * Unsupported theme compatibility methods.
     */

    /**
     * Hook in methods to enhance the unsupported theme experience on pages.
     *
     * @since 1.5.56
     */
    public static function unsupported_theme_init() {

        if (0 < self::$listings_page_id) {
            if (Functions::is_listing_taxonomy()) {
                self::unsupported_theme_tax_archive_init();
            } elseif (Functions::is_listing()) {
                self::unsupported_theme_listing_page_init();
            } else {
                self::unsupported_theme_listings_page_init();
            }
        }
    }


    /**
     * Enhance the unsupported theme experience on Product Category and Attribute pages by rendering
     * those pages using the single template and shortcode-based content. To do this we make a dummy
     * post and set a shortcode as the post content. This approach is adapted from bbPress.
     *
     * @since 1.5.56
     */
    private static function unsupported_theme_tax_archive_init() {
        global $wp_query, $post;

        $queried_object = get_queried_object();
        $queried_tax = '';
        if ($queried_object && isset($queried_object->taxonomy)) {
            $queried_tax = $queried_object->taxonomy;
        }
        switch ($queried_tax) {
            case 'rtcl_location':
                $location = $queried_object->slug;
                $category = get_query_var('rtcl_category');
                break;
            case 'rtcl_category':
                $category = $queried_object->slug;
                $location = get_query_var('rtcl_location');
                break;
            default:
                $category = get_query_var('rtcl_category');
                $location = get_query_var('rtcl_location');
                break;
        }
        $args = self::get_current_listings_view_args();
        $shortcode_args = array(
            'page'        => $args->page,
            'paginate'    => true,
            'cache'       => false,
            'limit'       => apply_filters('rtcl_loop_listing_per_page', Functions::get_option_item('rtcl_general_settings', 'listings_per_page'))
        );

        if (Functions::is_listing_category() || Functions::is_listing_location()) {
            $shortcode_args['category'] = $category;
            $shortcode_args['location'] = $location;
        } else {
            // Default theme archive for all other taxonomies.
            return;
        }

        // Description handling.
        if (!empty($queried_object->description) && (empty($_GET['listing-page']) || 1 === absint($_GET['listing-page']))) { // WPCS: input var ok, CSRF ok.
            $prefix = '<div class="term-description">' . Functions::format_content($queried_object->description) . '</div>'; // WPCS: XSS ok.
        } else {
            $prefix = '';
        }

        $shortcode = new Listings($shortcode_args);
        $listings_page = get_post(self::$listings_page_id);

        $dummy_post_properties = array(
            'ID'                    => 0,
            'post_status'           => 'publish',
            'post_author'           => $listings_page->post_author,
            'post_parent'           => 0,
            'post_type'             => 'page',
            'post_date'             => $listings_page->post_date,
            'post_date_gmt'         => $listings_page->post_date_gmt,
            'post_modified'         => $listings_page->post_modified,
            'post_modified_gmt'     => $listings_page->post_modified_gmt,
            'post_content'          => $prefix . $shortcode->get_content(),
            'post_title'            => Functions::clean($queried_object->name),
            'post_excerpt'          => '',
            'post_content_filtered' => '',
            'post_mime_type'        => '',
            'post_password'         => '',
            'post_name'             => $queried_object->slug,
            'guid'                  => '',
            'menu_order'            => 0,
            'pinged'                => '',
            'to_ping'               => '',
            'ping_status'           => '',
            'comment_status'        => 'closed',
            'comment_count'         => 0,
            'filter'                => 'raw',
        );

        // Set the $post global.
        $post = new WP_Post((object)$dummy_post_properties); // @codingStandardsIgnoreLine.

        // Copy the new post global into the main $wp_query.
        $wp_query->post = $post;
        $wp_query->posts = array($post);

        // Prevent comments form from appearing.
        $wp_query->post_count = 1;
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
        $wp_query->is_single = true;
        $wp_query->is_archive = false;
        $wp_query->is_tax = true;
        $wp_query->max_num_pages = 0;

        // Prepare everything for rendering.
        setup_postdata($post);
        remove_all_filters('the_content');
        remove_all_filters('the_excerpt');
        add_filter('template_include', array(__CLASS__, 'force_single_template_filter'));
    }


    /**
     * Hook in methods to enhance the unsupported theme experience on the Shop page.
     *
     * @since 1.5.56
     */
    private static function unsupported_theme_listings_page_init() {
        add_filter('the_content', array(__CLASS__, 'unsupported_theme_listings_content_filter'), 10);
    }


    /**
     * Filter the title and insert WooCommerce content on the shop page.
     *
     * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
     *
     * @param string $title Existing title.
     * @param int    $id    ID of the post being filtered.
     *
     * @return string
     * @since 1.5.56
     */
    public static function unsupported_theme_title_filter($title, $id) {
        if (self::$theme_support || !$id !== self::$listings_page_id) {
            return $title;
        }

        if (is_page(self::$listings_page_id) || (is_home() && 'page' === get_option('show_on_front') && absint(get_option('page_on_front')) === self::$listings_page_id)) {
            $args = self::get_current_listings_view_args();
            $title_suffix = array();

            if ($args->page > 1) {
                /* translators: %d: Page number. */
                $title_suffix[] = sprintf(esc_html__('Page %d', 'classified-listing'), $args->page);
            }

            if ($title_suffix) {
                $title = $title . ' &ndash; ' . implode(', ', $title_suffix);
            }
        }
        return $title;
    }


    /**
     * Filter the content and insert ClassifiedListing content on the Listings page.
     *
     * For non-RTCL themes, this will setup the main Listings page to be shortcode based to improve default appearance.
     *
     * @param string $content Existing post content.
     *
     * @return string
     * @since 1.5.56
     */
    public static function unsupported_theme_listings_content_filter($content) {

        if (self::$theme_support || !is_main_query() || !in_the_loop()) {
            return $content;
        }

        self::$in_content_filter = true;

        // Remove the filter we're in to avoid nested calls.
        remove_filter('the_content', array(__CLASS__, 'unsupported_theme_listings_content_filter'));
        $location = $category = '';
        if (isset($_GET['rtcl_location'])) {
            $location = get_term_by('slug', Functions::clean($_GET['rtcl_location']), rtcl()->location);
            $location = $location ? $location->slug : '';
        }
        if (isset($_GET['rtcl_category'])) {
            $category = get_term_by('slug', Functions::clean($_GET['rtcl_category']), rtcl()->category);
            $category = $category ? $category->slug : '';
        }
        // Unsupported theme shop page.
        if (is_page(self::$listings_page_id)) {
            $args = self::get_current_listings_view_args();
            $shortcode = new Listings(
                array_merge(
                    rtcl()->query->get_catalog_ordering_args(),
                    array(
                        'page'        => $args->page,
                        'location'    => $location,
                        'category'    => $category,
                        'paginate'    => true,
                        'cache'       => false,
                        'limit'       => apply_filters('rtcl_loop_listing_per_page', Functions::get_option_item('rtcl_general_settings', 'listings_per_page'))
                    )
                ),
                'listings'
            );

            // Allow queries to run e.g. layered nav.
            add_action('pre_get_posts', [rtcl()->query, 'listing_query']);

            $content = $content . $shortcode->get_content();

            // Remove actions and self to avoid nested calls.
            remove_action('pre_get_posts', [rtcl()->query, 'listing_query']);
            rtcl()->query->remove_ordering_args();
        }

        self::$in_content_filter = false;

        return $content;
    }

    /**
     * Hook in methods to enhance the unsupported theme experience on Listing pages.
     *
     * @since 1.5.56
     */
    private static function unsupported_theme_listing_page_init() {
        add_filter('the_content', array(__CLASS__, 'unsupported_theme_listing_content_filter'), 10);
        remove_action('rtcl_before_main_content', [TemplateHooks::class, 'output_content_wrapper'], 10);
        remove_action('rtcl_after_main_content', [TemplateHooks::class, 'output_content_wrapper_end'], 10);
    }


    /**
     * Filter the content and insert ClassifiedListing content on the shop page.
     *
     * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
     *
     * @param string $content Existing post content.
     *
     * @return string
     * @since 1.5.56
     */
    public static function unsupported_theme_listing_content_filter($content) {
        global $wp_query;

        if (self::$theme_support || !is_main_query() || !in_the_loop()) {
            return $content;
        }

        self::$in_content_filter = true;

        // Remove the filter we're in to avoid nested calls.
        remove_filter('the_content', array(__CLASS__, 'unsupported_theme_listing_content_filter'));

        if (Functions::is_listing()) {
            $content = do_shortcode('[rtcl_listing_page id="' . get_the_ID() . '" show_title=0 status="any"]');
        }

        self::$in_content_filter = false;

        return $content;
    }


    /**
     * Force the loading of one of the single templates instead of whatever template was about to be loaded.
     *
     * @param string $template Path to template.
     *
     * @return string
     * @since 1.5.56
     */
    public static function force_single_template_filter($template) {
        $possible_templates = array(
            'page',
            'single',
            'singular',
            'index',
        );

        foreach ($possible_templates as $possible_template) {
            $path = get_query_template($possible_template);
            if ($path) {
                return $path;
            }
        }

        return $template;
    }

    /**
     * Get information about the current listing page view.
     *
     * @return object
     * @since 1.5.56
     */
    private static function get_current_listings_view_args() {
        return (object)array(
            'page' => absint(max(1, absint(get_query_var('paged'))))
        );
    }

}