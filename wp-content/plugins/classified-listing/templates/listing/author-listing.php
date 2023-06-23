<?php
/**
 * Author Listing
 *
 * @author     RadiusTheme
 * @package    ClassifiedListing/Templates
 * @version    2.2.1.1
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$author  = get_user_by( 'slug', get_query_var( 'author_name' ) );
$user_id = $author->ID;

$general_settings = Functions::get_option( 'rtcl_general_settings' );
// Define the query
$paged = Pagination::get_page_number();

$args = array(
	'post_type'      => rtcl()->post_type,
	'posts_per_page' => ! empty( $general_settings['listings_per_page'] ) ? absint( $general_settings['listings_per_page'] ) : 10,
	'paged'          => $paged,
	'author'         => $user_id,
	'meta_query'     => [
		[
			'key'     => '_rtcl_manager_id',
			'compare' => 'NOT EXISTS'
		]
	]
);

$user_ads_query = new \WP_Query( apply_filters( 'rtcl_user_listing_args', $args ) );

if ( $user_ads_query->have_posts() ) : ?>
	<div class="rtcl-user-listing-list rtcl-user-ad-listing-wrapper">
		<h2><?php printf( esc_html__( "All ads from %s", "classified-listing" ), $author->display_name ) ?></h2>
		<div class="rtcl-listings rtcl-list-view rtcl-listing-wrapper"
			 data-pagination='{"max_num_pages":<?php echo esc_attr( $user_ads_query->max_num_pages ) ?>, "current_page": 1, "found_posts":<?php echo esc_attr( $user_ads_query->found_posts ) ?>, "posts_per_page":<?php echo esc_attr( $user_ads_query->query_vars['posts_per_page'] ) ?>}'>
			<!-- the loop -->
			<?php
			while ( $user_ads_query->have_posts() ) : $user_ads_query->the_post();
				$listing = rtcl()->factory->get_listing( get_the_ID() );
				Functions::get_template_part( 'content', 'listing' );
			endwhile; ?>
			<!-- end of the loop -->

			<!-- Use reset postdata to restore original query -->
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
<?php endif; ?>