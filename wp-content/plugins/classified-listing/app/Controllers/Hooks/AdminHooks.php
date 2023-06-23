<?php

namespace Rtcl\Controllers\Hooks;


use Rtcl\Helpers\Cache;

class AdminHooks {

	public static function init() {
		add_action( "rtcl_sent_email_to_user_by_moderator", [
			__CLASS__,
			'update_user_notification_by_moderator'
		], 10 );
		add_action( "rtcl_sent_email_to_user_by_visitor", [ __CLASS__, 'update_user_notification_by_visitor' ], 10 );
		add_action( 'update_option_rtcl_general_settings', [
			__CLASS__,
			'update_taxonomy_cache_at_taxonomy_order_change'
		], 10, 2 );
		add_filter( 'quick_edit_show_taxonomy', [ __CLASS__, 'listing_remove_taxonomy_from_quick_edit' ], 10, 3 );
		add_action( 'parse_request', [ __CLASS__, 'listing_payment_search_by_id' ] );

		add_action( 'in_admin_header', [ __CLASS__, 'remove_all_notices' ], 1000 );
	}


	/**
	 * Remove admin notices
	 */
	public static function remove_all_notices() {
		$screen = get_current_screen();
		if ( isset($screen->base) && 'rtcl_listing_page_rtcl-settings' == $screen->base ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	public static function listing_remove_taxonomy_from_quick_edit( $show_in_quick_edit, $taxonomy_name, $post_type ) {
		if ( rtcl()->post_type === $post_type && in_array( $taxonomy_name, [ rtcl()->location, rtcl()->category ] ) ) {
			return false;
		}

		return $show_in_quick_edit;
	}

	public static function update_user_notification_by_moderator( $post_id ) {
		$count = absint( get_post_meta( $post_id, "notification_by_moderation", true ) );

		update_post_meta( $post_id, 'notification_by_moderation', $count + 1 );
	}

	public static function update_user_notification_by_visitor( $post_id ) {

		$count = absint( get_post_meta( $post_id, "notification_by_visitor", true ) );

		update_post_meta( $post_id, 'notification_by_visitor', $count + 1 );

	}

	public static function update_taxonomy_cache_at_taxonomy_order_change( $old_options, $new_options ) {
		if ( ( isset( $old_options['taxonomy_orderby'] ) && isset( $new_options['taxonomy_orderby'] ) && ( $old_options['taxonomy_orderby'] !== $new_options['taxonomy_orderby'] ) ) ||
		     ( isset( $old_options['taxonomy_order'] ) && isset( $new_options['taxonomy_order'] ) && ( $old_options['taxonomy_order'] !== $new_options['taxonomy_order'] ) ) ) {
			Cache::remove_all_taxonomy_cache();
		}
	}

	public static function listing_payment_search_by_id( $wp ) {
		global $pagenow;
		if ( ! is_admin() && 'edit.php' != $pagenow && ( 'rtcl_listing' !== $_GET['post_type'] || 'rtcl_payment' !== $_GET['post_type'] ) ) {
			return;
		}

		if ( ! isset( $wp->query_vars['s'] ) ) {
			return;
		}

		$post_id = absint( $wp->query_vars['s'] );
		if ( ! $post_id ) {
			return;
		}

		unset( $wp->query_vars['s'] );
		$wp->query_vars['p'] = $post_id;
	}

}