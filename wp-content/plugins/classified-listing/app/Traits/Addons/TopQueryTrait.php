<?php
/**
 * Trait for listing widget
 *
 * The Elementor builder.
 *
 * @package  Classifid-listing
 * @since    2.0.10
 */

namespace Rtcl\Traits\Addons;

use RtclPro\Helpers\Fns;
/**
 * Top Query Related function.
 */
trait TopQueryTrait {
	/**
	 * Top Query Related function.
	 *
	 * @return array
	 */
	public function top_listing_query_prepared() {
		$top_query = Fns::top_listings_query();
		$posts_id  = array_map(
			function( $post ) {
				return $post->ID;
			},
			$top_query->posts
		);
		wp_reset_postdata();
		return array(
			'top_query' => $top_query,
			'top_items' => $posts_id,
		);
	}
}
