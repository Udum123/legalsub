<?php

namespace Rtcl\Helpers;

class Cache {
	/**
	 * Transients to delete on shutdown.
	 *
	 * @var array Array of transient keys.
	 */
	private static $delete_transients = [];

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'nocache_headers', [ __CLASS__, 'additional_nocache_headers' ], 10 );
//        add_action('template_redirect', array(__CLASS__, 'geolocation_ajax_redirect'));
//        add_action('admin_notices', array(__CLASS__, 'notices'));
//        add_action('delete_version_transients', array(__CLASS__, 'delete_version_transients'), 10);
//        add_action('wp', array(__CLASS__, 'prevent_caching'));
		add_action( 'clean_term_cache', [ __CLASS__, 'clean_term_cache' ], 10, 2 );
		add_action( 'edit_terms', [ __CLASS__, 'clean_term_cache' ], 10, 2 );
		add_action( 'init', [ __CLASS__, 'remove_all_cache' ] );
	}

	/**
	 * Set constants to prevent caching by some plugins.
	 *
	 * @param mixed $return Value to return. Previously hooked into a filter.
	 *
	 * @return mixed
	 */
	public static function set_nocache_constants( $return = true ) {
		Functions::maybe_define_constant( 'DONOTCACHEPAGE', true );
		Functions::maybe_define_constant( 'DONOTCACHEOBJECT', true );
		Functions::maybe_define_constant( 'DONOTCACHEDB', true );

		return $return;
	}

	/**
	 * Set additional nocache headers.
	 *
	 * @param array $headers Header names and field values.
	 *
	 * @return array
	 * @since 1.5.58
	 */
	public static function additional_nocache_headers( $headers ) {
		// no-transform: Opt-out of Google weblight if page is dynamic e.g. cart/checkout. https://support.google.com/webmasters/answer/6211428?hl=en.
		$headers['Cache-Control'] = 'no-transform, no-cache, no-store, must-revalidate';

		return $headers;
	}

	static function remove_all_cache() {
		if ( isset( $_GET['clear_rtcl_cache'] ) && Functions::verify_nonce() ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE `option_name` LIKE (%s) OR `option_name` LIKE (%s)",
				'_transient_rtcl_cache_%',
				'_transient_timeout_rtcl_cache%'
			) );
			Functions::add_notice( __( "All cache has been removed.", "classified-listing" ) );

		}
	}

	static function remove_all_taxonomy_cache() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE `option_name` LIKE (%s) OR `option_name` LIKE (%s) OR `option_name` LIKE (%s) OR `option_name` LIKE (%s)",
			'_transient_rtcl_cache_rtcl_category%',
			'_transient_rtcl_cache_rtcl_location%',
			'_transient_timeout_rtcl_cache_category%',
			'_transient_timeout_rtcl_cache_location%'
		) );
	}

	/**
	 * Clean term caches added by WooCommerce.
	 *
	 * @param array|int $ids Array of ids or single ID to clear cache for.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @since 3.3.4
	 */
	public static function clean_term_cache( $ids, $taxonomy ) {
		if ( in_array( $taxonomy, [ rtcl()->category, rtcl()->location ], true ) ) {

			$taxonomy_list_transient_name   = rtcl()->get_transient_name( '', $taxonomy, 'list' );
			$target_taxonomy_cache_key_part = $taxonomy . '_hierarchy_';

			delete_transient( $taxonomy_list_transient_name );
			$ids       = is_array( $ids ) ? $ids : [ $ids ];
			$clear_ids = [ 0 ];
			foreach ( $ids as $id ) {
				$clear_ids[] = $id;
				$clear_ids   = array_merge( $clear_ids, get_ancestors( $id, $taxonomy, 'taxonomy' ) );
			}

			$clear_ids = array_unique( $clear_ids );

			global $wpdb;
			foreach ( $clear_ids as $id ) {
				$term_transient_name = "_transient_" . rtcl()->get_transient_name( '%', $taxonomy, 'hierarchy_' . $id );
				$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE `option_name` LIKE (%s)", $term_transient_name ) );
				wp_cache_delete( $target_taxonomy_cache_key_part . $id, $taxonomy );
			}

		}
	}


	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once.
	 *
	 * @param string $group Group of cache to get.
	 *
	 * @return string
	 */
	public static function get_cache_prefix( $group ) {
		// Get cache key - uses cache key rtcl_orders_cache_prefix to invalidate when needed.
		$prefix = wp_cache_get( 'rtcl_' . $group . '_cache_prefix', $group );

		if ( false === $prefix ) {
			$prefix = microtime();
			wp_cache_set( 'rtcl_' . $group . '_cache_prefix', $prefix, $group );
		}

		return 'rtcl_cache_' . $prefix . '_';
	}

	/**
	 * Invalidate cache group.
	 *
	 * @param string $group Group of cache to clear.
	 *
	 * @since 2.2.6
	 */
	public static function invalidate_cache_group( $group ) {
		wp_cache_set( 'rtcl_' . $group . '_cache_prefix', microtime(), $group );
	}
}