<?php
/**
 * The template for displaying product content in the single-rtcl_listing.php template
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/content-single-rtcl_listing.php.
 *
 * @package ClassifiedListing/Templates
 * @version 1.5.56
 */

use Rtcl\Helpers\Functions;

defined('ABSPATH') || exit;

global $listing;

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
$sidebar_position = Functions::get_option_item('rtcl_moderation_settings', 'detail_page_sidebar_position', 'right');
$sidebar_class = array(
    'col-md-3',
    'order-2'
);
$content_class = array(
    'col-md-9',
    'order-1',
    'listing-content'
);
if ($sidebar_position == "left") {
    $sidebar_class = array_diff($sidebar_class, array('order-2'));
    $sidebar_class[] = 'order-1';
    $content_class = array_diff($content_class, array('order-1'));
    $content_class[] = 'order-2';
} else if ($sidebar_position == "bottom") {
    $content_class = array_diff($content_class, array('col-md-9'));
    $sidebar_class = array_diff($sidebar_class, array('col-md-3'));
    $content_class[] = 'col-sm-12';
    $sidebar_class[] = 'rtcl-listing-bottom-sidebar';
}
/**
 * Hook: rtcl_before_single_product.
 *
 * @hooked rtcl_print_notices - 10
 */
do_action('rtcl_before_single_listing');

?>
<div id="rtcl-listing-<?php the_ID(); ?>" <?php Functions::listing_class('', $listing); ?>>

    <div class="row">
        <!-- Main content -->
        <div class="<?php echo esc_attr(implode(' ', $content_class)); ?>">
            <div class="mb-4 rtcl-single-listing-details">
                <?php do_action('rtcl_single_listing_content'); ?>
                <div class="row rtcl-main-content-wrapper">
                    <!--  Content -->
                    <div class="col-md-8">
                        <!-- Price -->
                        <?php if ($listing->can_show_price()): ?>
                            <div class="rtcl-price-wrap">
                                <?php echo $listing->get_price_html(); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Description -->
                        <div class="rtcl-listing-description"><?php $listing->the_content(); ?></div>

                        <?php if ($sidebar_position === "bottom") : ?>
                            <!-- Sidebar -->
                            <?php do_action('rtcl_single_listing_sidebar'); ?>
                        <?php endif; ?>
                    </div>
                    <!--  Inner Sidebar -->
                    <div class="col-md-4">
                        <div class="single-listing-inner-sidebar">
                            <?php do_action('rtcl_single_listing_inner_sidebar'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAP  -->
	        <?php do_action('rtcl_single_listing_content_end', $listing); ?>

            <!-- Business Hours  -->
            <?php do_action('rtcl_single_listing_business_hours' ) ?>

            <!-- Social Profile  -->
            <?php do_action('rtcl_single_listing_social_profiles') ?>

            <!-- Related Listing -->
            <?php $listing->the_related_listings(); ?>

            <!-- Review  -->
            <?php do_action('rtcl_single_listing_review') ?>
        </div>

        <?php if (in_array($sidebar_position, array('left', 'right'))) : ?>
            <!-- Sidebar -->
            <?php do_action('rtcl_single_listing_sidebar'); ?>
        <?php endif; ?>
    </div>
</div>

<?php do_action('rtcl_after_single_listing'); ?>
