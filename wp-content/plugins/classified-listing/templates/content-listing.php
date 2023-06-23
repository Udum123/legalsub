<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.5.4
 */

use Rtcl\Helpers\Functions;

global $listing;

?>
<div <?php Functions::listing_class('', $listing) ?><?php Functions::listing_data_attr_options() ?>>
    <?php
    /**
     * Hook: rtcl_before_listing_loop_item.
     *
     * @hooked rtcl_template_loop_product_link_open - 10
     */
    do_action('rtcl_before_listing_loop_item');

    /**
     * Hook: rtcl_listing_loop_item.
     *
     * @hooked listing_thumbnail - 10
     */
    do_action('rtcl_listing_loop_item_start');


    /**
     * Hook: rtcl_listing_loop_item.
     *
     * @hooked loop_item_wrap_start - 10
     * @hooked loop_item_listing_title - 20
     * @hooked loop_item_badges - 30
     * @hooked loop_item_listable_fields - 40
     * @hooked loop_item_meta - 50
     * @hooked loop_item_meta_buttons - 60
     * @hooked loop_item_excerpt - 70
     * @hooked listing_price - 80
     * @hooked loop_item_wrap_end - 100
     */
    do_action('rtcl_listing_loop_item');


    /**
     * Hook: rtcl_listing_loop_item_end.
     *
     * @hooked listing_price - 10
     */
    do_action('rtcl_listing_loop_item_end');

    /**
     * Hook: rtcl_after_listing_loop_item.
     *
     * @hooked listing_loop_map_data - 50
     * @hooked listing_loop_sold_out_banner - 200
     */
    do_action('rtcl_after_listing_loop_item');
    ?>
</div>
