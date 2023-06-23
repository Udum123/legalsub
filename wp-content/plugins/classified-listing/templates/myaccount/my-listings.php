<?php
/**
 *Manage Listing by user
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var WP_Query $rtcl_query
 */


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Pagination;

global $post;
?>

<div class="rtcl rtcl-listings manage-listing">

    <?php do_action('rtcl_my_account_before_my_listing', $rtcl_query); ?>

    <!-- header here -->
    <div class="action-wrap mb-2">
        <div class="float-sm-left">
            
            <form action="<?php echo esc_url( Link::get_account_endpoint_url("listings") ); ?>" class="form-inline">
                <label class="sr-only" for="search-ml"><?php esc_html_e("Name", "classified-listing") ?></label>
                <input type="text" id="search-ml" name="u" class="form-control mb-2 mr-sm-2"
                       placeholder="<?php esc_attr_e("Search by title", 'classified-listing'); ?>"
                       value="<?php echo isset($_GET['u']) ? esc_attr(wp_unslash($_GET['u'])) : ''; ?>">
                <button type="submit"
                        class="btn btn-primary mb-2"><?php esc_html_e("Search", 'classified-listing'); ?></button>
                <?php Functions::query_string_form_fields(null, ['submit', 'paged', 'u']); ?>
            </form>
        </div>
        <?php if( apply_filters( 'rtcl_add_new_listing_button', true ) ){ ?>
        <div class="float-sm-right">
            <a href="<?php echo esc_url(Link::get_listing_form_page_link()); ?>"
               class="btn btn-success"><?php esc_html_e('Add New Listing', 'classified-listing'); ?></a>
        </div>
        <?php } ?>
        <div class="clearfix"></div>
    </div>
    <?php if ($rtcl_query->have_posts()): ?>
        <div class="rtcl-list-view">
            <!-- the loop -->
            <?php while ($rtcl_query->have_posts()) : $rtcl_query->the_post();
                $post_meta = get_post_meta($post->ID);
                $listing = rtcl()->factory->get_listing($post->ID);
                ?>
                <div class="listing-item rtcl-listing-item">
                    <div class="listing-thumb">
                        <a href="<?php the_permalink(); ?>"><?php $listing->the_thumbnail(); ?></a>
                    </div>
                    <div class="listing-details">
                        <div class="item-content">
                            <div class="rtcl-listings-title-block">
                                <h3 class="listing-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php $listing->the_badges(); ?>
                            </div>
                            <?php $listing->the_meta(); ?>

                            <div class="rtcl-status-wrap">
                                <strong><?php esc_html_e('Status', 'classified-listing'); ?></strong>:
                                <span class="rtcl-status"><?php echo Functions::get_status_i18n($post->post_status); ?></span>
                            </div>
                            <?php if ($listing->get_status() !== 'pending') { ?>
                                <?php if (get_post_meta($listing->get_id(), 'never_expires', true)) : ?>
                                    <div class="rtcl-never-expired rtcl-expire-wrap">
                                        <strong><?php esc_html_e('Expires on', 'classified-listing'); ?></strong>:
										<span class="rtcl-expire"><?php esc_html_e('Never Expires', 'classified-listing'); ?></span>
                                    </div>
                                <?php elseif ($expiry_date = get_post_meta($listing->get_id(), 'expiry_date', true)) : ?>
                                    <div class="rtcl-expired-on rtcl-expire-wrap">
                                        <strong><?php esc_html_e('Expires on', 'classified-listing'); ?></strong>:
                                        <span class="rtcl-expire"><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'),
                                            strtotime($expiry_date)); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>

                            <?php do_action('rtcl_listing_loop_extra_meta', $listing); ?>

                        </div>
                        <?php if( apply_filters( 'rtcl_my_listing_actions_button_display', true ) ){ ?>
                        <div class="rtcl-actions">
                            <?php do_action('rtcl_my_listing_actions', $listing); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
            <!-- end of the loop -->
        </div>
        <!-- pagination here -->
        <?php Pagination::pagination($rtcl_query); ?>
    <?php else: ?>
        <p><?php esc_html_e("No listing found.", 'classified-listing'); ?></p>
    <?php endif; ?>

    <?php do_action('rtcl_my_account_after_my_listing', $rtcl_query); ?>
</div>