<?php
/**
 * @package ClassifiedListing/Templates
 * @version 2.2.1.1
 */

use Rtcl\Helpers\Functions;

defined('ABSPATH') || exit;

get_header('listing');

/**
 * Hook: rtcl_before_main_content.
 *
 * @hooked rtcl_output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action('rtcl_before_main_content');

Functions::get_template( 'listing/author-content');

/**
 * Hook: rtcl_after_main_content.
 *
 * @hooked rtcl_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('rtcl_after_main_content');

/**
 * Hook: rtcl_sidebar.
 *
 * @hooked rtcl_get_sidebar - 10
 */

do_action('rtcl_sidebar');

get_footer('listing');
