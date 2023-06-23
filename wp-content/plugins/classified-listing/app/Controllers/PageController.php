<?php

namespace Rtcl\Controllers;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

class PageController {

	public static function init() {
		add_action( 'wp_loaded', [ __CLASS__, 'maybe_flush_rules' ] );
		add_filter( 'force_ssl', [ __CLASS__, 'force_ssl_https' ], 10, 2 );
		add_filter( 'the_title', [ __CLASS__, 'page_endpoint_title' ] );
		add_filter( 'lostpassword_url', [ __CLASS__, 'lostpassword_url' ], 10, 1 );
		add_action( 'wp_robots', [ __CLASS__, 'account_page_wp_robots_no_robots' ] );
		add_action( 'wp_head', [ __CLASS__, 'add_views_counter' ] );
		add_action( 'wp_head', [ __CLASS__, 'og_metatags' ], 1 );
	}


	public static function og_metatags() {
		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		$page_settings = Functions::get_page_ids();
		$page          = '';
		if ( Functions::is_listing() ) {
			$page = 'listing';
		} elseif ( ! empty( $page_settings['listings'] ) && $page_settings['listings'] === $post->ID ) {
			$page = 'listings';
		}

		if ( Functions::get_option_item( 'rtcl_misc_settings', 'social_pages', $page, 'multi_checkbox' ) ) {

			$title = get_the_title();

			echo '<meta property="og:url" content="' . Link::get_current_url() . '" />';
			echo '<meta property="og:type" content="article" />';
			echo '<meta property="og:title" content="' . $title . '" />';
			if ( 'listing' === $page ) {
				if ( ! empty( $post->post_content ) ) {
					echo '<meta property="og:description" content="' . wp_trim_words( $post->post_content,
							150 ) . '" />';
				}
				$attachment_id = Functions::get_listing_first_image_id( $post->ID );
				if ( ! empty( $attachment_id ) ) {
					$thumbnail = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( ! empty( $thumbnail ) ) {
						echo '<meta property="og:image" content="' . $thumbnail[0] . '" />';
					}
				}
			}
			
			echo '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '" />';
			echo '<meta name="twitter:card" content="summary" />';
			do_action( 'rtcl_share_og_metatags', $page );
		}
	}

	/**
	 * Disable search engines indexing, myaccount pages.
	 *
	 * @param array $robots
	 *
	 * @return array
	 * @since 1.5.5
	 *
	 */
	static function account_page_wp_robots_no_robots( $robots ) {
		if ( is_page( Functions::get_page_id( 'myaccount' ) ) ) {
			return wp_robots_no_robots( $robots );
		}

		return $robots;
	}


	public static function add_views_counter() {
		if ( Functions::is_listing() && is_main_query() ) {
			global $post;
			Functions::update_listing_views_count( $post->ID );
		}
	}


	/**
	 * Replace a page title with the endpoint title.
	 *
	 * @param string $title Post title.
	 *
	 * @return string
	 */
	static function page_endpoint_title( $title ) {
		global $wp_query;

		if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && Functions::is_endpoint_url() ) {
			$endpoint       = rtcl()->query->get_current_endpoint();
			$endpoint_title = rtcl()->query->get_endpoint_title( $endpoint );
			$title          = $endpoint_title ? $endpoint_title : $title;

			remove_filter( 'the_title', [ __CLASS__, 'page_endpoint_title' ] );
		}

		return $title;
	}

	/**
	 * Returns the url to the lost password endpoint url.
	 *
	 * @param string $default_url Default lost password URL.
	 *
	 * @return string
	 */
	public static function lostpassword_url( $default_url = '' ) {
		return Link::lostpassword_url( $default_url );
	}


	public static function maybe_flush_rules() {

		$rewrite_rules = get_option( 'rewrite_rules' );

		if ( $rewrite_rules ) {

			global $wp_rewrite;
			$rewrite_rules_array = [];
			foreach ( $rewrite_rules as $rule => $rewrite ) {
				$rewrite_rules_array[ $rule ]['rewrite'] = $rewrite;
			}
			$rewrite_rules_array = array_reverse( $rewrite_rules_array, true );

			$maybe_missing = $wp_rewrite->rewrite_rules();
			$missing_rules = false;

			foreach ( $maybe_missing as $rule => $rewrite ) {
				if ( ! array_key_exists( $rule, $rewrite_rules_array ) ) {
					$missing_rules = true;
					break;
				}
			}

			if ( true === $missing_rules ) {
				flush_rewrite_rules();
			}

		}

	}

	public static function force_ssl_https( $force_ssl, $post_id ) {

		$checkout_page_id = Functions::get_page_id( 'checkout' );

		if ( $post_id === $checkout_page_id && Functions::get_option_item( 'rtcl_payment_settings', 'use_https', false ) ) {
			return true;
		}

		return $force_ssl;

	}

}
