<?php
// If uninstall not called from WordPress, then exit

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
require_once __DIR__ . '/vendor/autoload.php';

use Rtcl\Helpers\Installer;
use Rtcl\Models\Roles;

Installer::clean_cron_jobs();

$settings = get_option( 'rtcl_tools_settings' );
if ( ! empty( $settings['delete_all_data'] ) && 'yes' === $settings['delete_all_data'] ) {

	// Delete All the Custom Post Types
	$rtcl_post_types = [
		'rtcl_listing',
		'rtcl_cf',
		'rtcl_cfg',
		'rtcl_payment',
		'rtcl_pricing'
	];

	foreach ( $rtcl_post_types as $post_type ) {

		$items = get_posts( [
			'post_type'   => $post_type,
			'post_status' => 'any',
			'numberposts' => - 1,
			'fields'      => 'ids'
		] );

		if ( $items ) {
			foreach ( $items as $item ) {
				// Delete the actual post
				wp_delete_post( $item, true );
			}
		}

	}

	// Delete All the Terms & Taxonomies
	$rtcl_taxonomies = [ 'rtcl_category', 'rtcl_location' ];

	global $wpdb;

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}rtcl_sessions" );

	foreach ( $rtcl_taxonomies as $taxonomy ) {

		$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

		// Delete Terms
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_taxonomy, [ 'term_taxonomy_id' => $term->term_taxonomy_id ] );
				$wpdb->delete( $wpdb->terms, [ 'term_id' => $term->term_id ] );
			}
		}

		// Delete Taxonomies
		$wpdb->delete( $wpdb->term_taxonomy, [ 'taxonomy' => $taxonomy ], [ '%s' ] );

	}


	// Delete all the Plugin Options
	$rtcl_settings = [
		'rtcl_general_settings',
		'rtcl_moderation_settings',
		'rtcl_payment_settings',
		'rtcl_payment_offline',
		'rtcl_email_settings',
		'rtcl_account_settings',
		'rtcl_misc_settings',
		'rtcl_style_settings',
		'rtcl_advanced_settings',
		'rtcl_tools_settings'
	];

	foreach ( $rtcl_settings as $settings ) {
		delete_option( $settings );
	}

	delete_option( 'rtcl_version' );
	Roles::remove_roles();

	// Clear any cached data that has been removed
	wp_cache_flush();
}