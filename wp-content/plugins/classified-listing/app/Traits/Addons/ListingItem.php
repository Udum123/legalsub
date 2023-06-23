<?php
/**
 * Traits for Elementor single page editor
 *
 * The Elementor builder.
 *
 * @package  Classifid-listing
 * @since    2.0.10
 */

namespace Rtcl\Traits\Addons;

/**
 * Undocumented class
 */
trait ListingItem {

	/**
	 * Listing last Item id return
	 */
	public static function get_prepared_listing_id() {
		if ( is_singular( rtcl()->post_type ) ) {
			return get_the_ID();
		}

		global $wpdb;
		$cache_key = 'rtcl_last_post_id';
		$_post_id  = get_transient( $cache_key );

		if ( false === $_post_id || 'publish' !== get_post_status( $_post_id ) ) {
			delete_transient( $cache_key );
			$_post_id = $wpdb->get_var(
				$wpdb->prepare( "SELECT MAX(ID) FROM {$wpdb->prefix}posts WHERE post_type =  %s AND post_status = %s", rtcl()->post_type, 'publish' )
			);
			set_transient( $cache_key, $_post_id, 12 * HOUR_IN_SECONDS );
		}

		return $_post_id;

	}

}

