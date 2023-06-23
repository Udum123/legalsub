<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
get_header('listing'); ?>
<?php
/**
 * rtcl_before_main_content hook.
 *
 * @hooked rtcl_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked rtcl_breadcrumb - 20
 */
do_action('rtcl_before_main_content');
?>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>

    <?php Functions::get_template_part('content', 'single-rtcl_listing'); ?>

<?php endwhile; // end of the loop. ?>

<?php
/**
 * rtcl_after_main_content hook.
 *
 * @hooked rtcl_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('rtcl_after_main_content');
?>

<?php
/**
 * rtcl_sidebar hook.
 *
 * @hooked rtcl_get_sidebar - 10
 */
do_action('rtcl_sidebar');
?>

<?php
get_footer('listing');
