<?php

namespace Rtcl\Controllers;

use Rtcl\Helpers\Functions;
use WP_Query;

class Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	public $query_vars = [];

	/**
	 * Reference to the main listing query on the page.
	 *
	 * @var array
	 */
	private static $listing_query;

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_endpoints' ] );
		if ( ! is_admin() ) {
			add_action( 'wp_loaded', [ $this, 'get_errors' ], 20 );
			add_filter( 'query_vars', [ $this, 'add_query_vars' ], 0 );
			add_action( 'parse_request', [ $this, 'parse_request' ], 0 );
			add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
			add_action( 'pre_get_posts', [ $this, 'allow_pending_listings' ] );
			add_action( 'pre_get_posts', [ $this, 'exclude_blocked_listings_users' ] );
		}
	}

	public function allow_pending_listings( $q ) {
		if ( $q->is_main_query() && is_user_logged_in() && isset( $_GET['p'] ) && rtcl()->post_type === $q->get( 'post_type' ) && ( $post = get_post( $_GET['p'] ) ) && $post->post_status === 'pending' && $post->post_author == get_current_user_id() ) {
			$q->set( 'post_status', [ 'pending' ] );
		}

		return $q;
	}

	public function exclude_blocked_listings_users( $q ) {
		$q->set( 'author__not_in', $this->get_author__not_in( $q->get( 'author__not_in' ) ) );
		$q->set( 'post__not_in', $this->get_post__not_in( $q->get( 'post__not_in' ) ) );

		return $q;
	}


	/**
	 * Endpoint mask describing the places the endpoint should be added.
	 *
	 * @return int
	 * @since 2.6.2
	 */
	public function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front     = get_option( 'page_on_front' );
			$myaccount_page_id = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount' );
			$checkout_page_id  = Functions::get_option_item( 'rtcl_advanced_settings', 'checkout' );

			if ( in_array( $page_on_front, [ $myaccount_page_id, $checkout_page_id ], true ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {
		$this->init_query_vars();
		$mask = $this->get_endpoints_mask();
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, $mask );
			}
		}

		$this->add_rewrites();
	}

	private function add_rewrites() {
		$url = home_url();

		$page_settings = Functions::get_page_ids();
		if ( $id = Functions::get_page_id( 'listing_form' ) ) {
			$link = str_replace( $url, '', get_permalink( $id ) );
			$link = trim( $link, '/' );
			add_rewrite_rule( "$link/([^/]+)/([0-9]{1,})/?$", 'index.php?page_id=' . $id . '&rtcl_action=$matches[1]&rtcl_listing_id=$matches[2]', 'top' );
		}

		if ( $id = Functions::get_page_id( 'myaccount' ) ) {
			$link = str_replace( $url, '', get_permalink( $id ) );
			$link = trim( $link, '/' );
			foreach ( $this->get_query_vars() as $key => $var ) {
				if ( $key === "listings" || $key === "favourites" || $key === "payments" ) {
					add_rewrite_rule( "$link/$var/page/?([0-9]{1,})/?$", 'index.php?' . $var . '=&page_id=' . $id . '&paged=$matches[1]', 'top' );
				} elseif ( Functions::is_wc_active() && $key === "edit-account" ) {
					add_rewrite_rule( "$link/($var)/?$", 'index.php?page_id=' . $id . '&rtcl_edit_account=$matches[1]', 'top' );
					add_rewrite_tag( '%rtcl_edit_account%', '([^/]+)' );
				} elseif ( Functions::is_wc_active() && $key === "lost-password" ) {
					add_rewrite_rule( "$link/($var)/?$", 'index.php?page_id=' . $id . '&rtcl_lost_password=$matches[1]', 'top' );
					add_rewrite_tag( '%rtcl_lost_password%', '([^/]+)' );
				}
			}

		}

		// Rewrite tags
		add_rewrite_tag( '%rtcl_listing_id%', '([0-9]{1,})' );
		add_rewrite_tag( '%rtcl_action%', '([^&]+)' );
		add_rewrite_tag( '%rtcl_payment_id%', '([0-9]{1,})' );

		do_action( 'rtcl_add_rewrites', $page_settings );
	}


	/**
	 * Get any errors from querystring.
	 */
	public function get_errors() {
		$error = ! empty( $_GET['rtcl_error'] ) ? sanitize_text_field( wp_unslash( $_GET['rtcl_error'] ) ) : ''; // WPCS: input var ok, CSRF ok.

		if ( $error && ! Functions::has_notice( $error, 'error' ) ) {
			Functions::add_notice( $error, 'error' );
		}
	}


	/**
	 * Add query vars.
	 *
	 * @access public
	 *
	 * @param array $vars Query vars.
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {

		// Query vars to add to WP.
		$this->query_vars = array_merge(
			Functions::get_my_account_page_endpoints(),
			Functions::get_checkout_page_endpoints()
		);
	}


	/**
	 * Controls WP displays the courses in a page which setup to display on homepage
	 *
	 * @param $q WP_Query
	 *
	 */
	public function pre_get_posts( $q ) {
		// We only want to affect the main query and not in admin
		if ( ! $q->is_main_query() || is_admin() ) {
			return;
		}
		remove_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 10 );

		$listings_page_id = Functions::get_page_id( 'listings' );
		$front_page_id    = absint( get_option( 'page_on_front' ) );
		// Fixes for queries on static homepages.
		if ( $this->is_showing_page_on_front( $q ) ) {

			// Fix for endpoints on the homepage.
			if ( ! $this->page_on_front_is( $q->get( 'page_id' ) ) ) {
				$_query = wp_parse_args( $q->query );
				if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_merge( array_keys( $this->get_query_vars() ), [
						'rtcl_location',
						'rtcl_category'
					] ) ) ) {
					$q->is_page     = true;
					$q->is_home     = false;
					$q->is_singular = true;
					$q->set( 'page_id', $front_page_id );
					add_filter( 'redirect_canonical', '__return_false' );
				}
			}

			// When orderby is set, WordPress shows posts on the front-page. Get around that here.
			if ( $this->page_on_front_is( $listings_page_id ) ) {
				$_query = wp_parse_args( $q->query );
				if ( empty( $_query ) || ! array_diff( array_keys( $_query ), [
						'preview',
						'page',
						'paged',
						'cpage',
						'orderby'
					] ) ) {
					$q->set( 'page_id', $front_page_id );
					$q->is_page = true;
					$q->is_home = false;

					// WP supporting themes show post type archive.
					if ( current_theme_supports( 'rtcl' ) ) {
						$q->set( 'post_type', rtcl()->post_type );
					} else {
						$q->is_singular = true;
					}
				}
			} elseif ( ! empty( $_GET['orderby'] ) ) {
				$q->set( 'page_id', $front_page_id );
				$q->is_page     = true;
				$q->is_home     = false;
				$q->is_singular = true;
			}
		}

		// Fix product feeds.
		if ( $q->is_feed() && $q->is_post_type_archive( rtcl()->post_type ) ) {
			$q->is_comment_feed = false;
		}
		// Special check for shops with the PRODUCT POST TYPE ARCHIVE on front.
		if ( current_theme_supports( 'rtcl' ) && $q->is_page() && 'page' === get_option( 'show_on_front' ) && $listings_page_id && absint( $q->get( 'page_id' ) ) === $listings_page_id ) {
			// This is a front-page shop.
			$q->set( 'post_type', rtcl()->post_type );
			$q->set( 'page_id', '' );

			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page shop later on.
			rtcl()->define( 'RTCL_LISTINGS_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors and let us use is_front_page().
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
			global $wp_post_types;

			$listings_page = get_post( $listings_page_id );

			$wp_post_types[ rtcl()->post_type ]->ID         = $listings_page->ID;
			$wp_post_types[ rtcl()->post_type ]->post_title = $listings_page->post_title;
			$wp_post_types[ rtcl()->post_type ]->post_name  = $listings_page->post_name;
			$wp_post_types[ rtcl()->post_type ]->post_type  = $listings_page->post_type;
			$wp_post_types[ rtcl()->post_type ]->ancestors  = get_ancestors( $listings_page->ID, $listings_page->post_type );

			// Fix conditional Functions like is_front_page.
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = true;
			$q->set( 'post_type', rtcl()->post_type );


			// Remove post type archive name from front page title tag.
			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// Fix WP SEO.
			if ( class_exists( 'WPSEO_Meta' ) ) {
				add_filter( 'wpseo_metadesc', [ $this, 'wpseo_metadesc' ] );
				add_filter( 'wpseo_metakey', [ $this, 'wpseo_metakey' ] );
			}
		} elseif ( ! $q->is_post_type_archive( rtcl()->post_type ) && ! $q->is_tax( get_object_taxonomies( rtcl()->post_type ) ) ) {
			// Only apply to listing categories, the listing post archive, the Listings page, listing location taxonomies.
			return;
		}
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 10 );
		$this->listing_query( $q );
	}


	/**
	 * Are we currently on the front page?
	 *
	 * @param WP_Query $q Query instance.
	 *
	 * @return bool
	 */
	private function is_showing_page_on_front( $q ) {
		return ( $q->is_home() && ! $q->is_posts_page ) && 'page' === get_option( 'show_on_front' );
	}

	/**
	 * Is the front page a page we define?
	 *
	 * @param int $page_id Page ID.
	 *
	 * @return bool
	 */
	private function page_on_front_is( $page_id ) {
		return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
	}


	/**
	 * Remove the query.
	 */
	public function remove_product_query() {
		remove_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
	}


	/**
	 * WP SEO meta description.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 */
	public function wpseo_metadesc() {
		return \WPSEO_Meta::get_value( 'metadesc', Functions::get_page_id( 'listings' ) );
	}


	/**
	 * WP SEO meta key.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 */
	public function wpseo_metakey() {
		return \WPSEO_Meta::get_value( 'metakey', Functions::get_page_id( 'listings' ) );
	}

	/**
	 * Remove ordering queries.
	 */
	public function remove_ordering_args() {
		// TODO : need to add here
	}


	/**
	 * Returns an array of arguments for ordering products based on the selected values.
	 *
	 * @param string $orderby Order by param.
	 * @param string $order Order param.
	 *
	 * @return array
	 */
	public function get_catalog_ordering_args( $orderby = '', $order = '' ) {
		// Get ordering from query string unless defined.
		$order = ! empty( $order ) ? $order : Functions::get_option_item( 'rtcl_general_settings', 'order', 'desc' );
		if ( ! $orderby ) {
			$orderby_value = isset( $_GET['orderby'] ) ? Functions::clean( (string) wp_unslash( $_GET['orderby'] ) ) : ''; // WPCS: sanitization ok, input var ok, CSRF ok.

			if ( ! $orderby_value ) {
				$order_by      = Functions::get_option_item( 'rtcl_general_settings', 'orderby', 'date' );
				$order         = Functions::get_option_item( 'rtcl_general_settings', 'order', 'desc' );
				$orderby_value = apply_filters( 'rtcl_default_catalog_orderby', $order_by . "-" . $order, $order_by, $order );
			}
			// Get order + orderby args from string.
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
		}

		// Convert to correct format.
		$orderby = strtolower( is_array( $orderby ) ? (string) current( $orderby ) : (string) $orderby );
		$order   = strtoupper( is_array( $order ) ? (string) current( $order ) : (string) $order );
		$args    = [
			'orderby'  => $orderby,
			'order'    => ( 'DESC' === $order ) ? 'DESC' : 'ASC',
			'meta_key' => '', // @codingStandardsIgnoreLine
		];

		switch ( $orderby ) {
			case 'id':
				$args['orderby'] = 'ID';
				break;
			case 'menu_order':
				$args['orderby'] = 'menu_order title';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'date' :
				$args['orderby'] = 'date';
				$args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'price' :
				$args['meta_key'] = 'price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'views' :
				$args['meta_key'] = '_views';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			case 'rand' :
				$args['orderby'] = 'rand';
				break;
		}

		return apply_filters( 'rtcl_get_catalog_ordering_args', $args, $orderby, $order );
	}


	/**
	 * Query the listings, applying sorting/ordering etc.
	 * This applies to the main WordPress loop.
	 *
	 * @param WP_Query $q Query instance.
	 */
	public function listing_query( $q ) {
		if ( ! is_feed() ) {
			$ordering = $this->get_catalog_ordering_args();
			$q->set( 'orderby', $ordering['orderby'] );
			$q->set( 'order', $ordering['order'] );

			if ( isset( $ordering['meta_key'] ) ) {
				$q->set( 'meta_key', $ordering['meta_key'] );
			}
		}

		if ( isset( $_GET['q'] ) && ( rtcl()->post_type === $q->get( 'post_type' ) || $q->is_tax( get_object_taxonomies( rtcl()->post_type ) ) ) ) {
			$q->set( 's', (string) Functions::clean( wp_unslash( $_GET['q'] ) ) );
		}

		// Meta query for listing
		$q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ), true ) );
		$q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ), true ) );
		$q->set( 'rtcl_query', 'rtcl_listing_query' );
		$q->set( 'post__in', array_unique( (array) apply_filters( 'rtcl_loop_listing_post_in', [] ) ) );

		// Listings per page.
		$q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'rtcl_loop_listing_per_page', Functions::get_option_item( 'rtcl_general_settings', 'listings_per_page' ) ) );

		// Store reference to this query.
		self::$listing_query = $q;

		do_action( 'rtcl_listing_query', $q, $this );
	}

	/**
	 * Appends meta queries to an array.
	 *
	 * @param array $meta_query Meta query.
	 * @param bool $main_query If is main query.
	 *
	 * @return array
	 */
	public function get_meta_query( $meta_query = [], $main_query = false ) {
		if ( ! is_array( $meta_query ) ) {
			$meta_query = [
				'relation' => 'AND'
			];
		}

		$filters = isset( $_GET['filters'] ) ? (array) $_GET['filters'] : [];
		if ( ! empty( $filters ) ) {

			// Price filter
			if ( ! empty( $filters['price'] ) ) {

				$price = array_filter( $filters['price'] );

				if ( $n = count( $price ) ) {

					if ( 2 == $n ) {
						$meta_query[] = [
							'key'     => 'price',
							'value'   => array_map( 'intval', array_values( $price ) ),
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN'
						];
					} else {
						if ( empty( $price['min'] ) ) {
							$meta_query[] = [
								'key'     => 'price',
								'value'   => (int) $price['max'],
								'type'    => 'NUMERIC',
								'compare' => '<='
							];
						} else {
							$meta_query[] = [
								'key'     => 'price',
								'value'   => (int) $price['min'],
								'type'    => 'NUMERIC',
								'compare' => '>='
							];
						}
					}

				}
				unset( $filters['price'] );
			}

			// Ad type filter
			if ( ! empty( $filters['ad_type'] ) && in_array( $filters['ad_type'], array_keys( Functions::get_listing_types() ) ) && ! Functions::is_ad_type_disabled() ) {
				$ad_type = $filters['ad_type'];

				$meta_query[] = [
					'key'     => 'ad_type',
					'value'   => $ad_type,
					'compare' => '='
				];

			}

			$cf = array_filter( $filters );
			if ( ! empty( $cf ) ) {
				$cf_meta_query = [];
				foreach ( $cf as $key => $values ) {
					$field_id = absint( str_replace( "_field_", '', $key ) );
					$field    = rtcl()->factory->get_custom_field( $field_id );
					if ( $field ) {
						if ( is_array( $values ) ) {
							if ( $field->getType() === 'number' ) {
								$values = array_filter( $values );
								if ( $n = count( $values ) ) {
									if ( 2 == $n ) {
										$cf_meta_query[] = [
											'key'     => $key,
											'value'   => array_map( 'intval', array_values( $values ) ),
											'type'    => 'NUMERIC',
											'compare' => 'BETWEEN'
										];
									} else {
										if ( empty( $values['min'] ) ) {
											$cf_meta_query[] = [
												'key'     => $key,
												'value'   => (int) $values['max'],
												'type'    => 'NUMERIC',
												'compare' => '<='
											];
										} else {
											$cf_meta_query[] = [
												'key'     => $key,
												'value'   => (int) $values['min'],
												'type'    => 'NUMERIC',
												'compare' => '>='
											];
										}
									}

								}
							} else if ( in_array( $field->getType(), [ 'checkbox', 'select', 'radio' ] ) ) {
								if ( count( $values ) > 1 ) {

									$sub_meta_queries = [];

									foreach ( $values as $value ) {
										$sub_meta_queries[] = [
											'key'     => $key,
											'value'   => sanitize_text_field( $value ),
											'compare' => 'LIKE'
										];
									}

									$meta_query_relation = array_merge( [ 'relation' => 'AND' ], $sub_meta_queries );

									$cf_meta_query[] = apply_filters( 'rtcl_cf_sub_meta_queries', $meta_query_relation, $field );

								} else {
									$cf_meta_query[] = [
										'key'     => $key,
										'value'   => sanitize_text_field( $values[0] ),
										'compare' => 'LIKE'
									];
								}
							}
						} else {
							if ( $field->getType() === 'date' ) {
								$date_type   = $field->getDateType();
								$search_type = $field->getDateSearchableType();
								$type        = $date_type == 'date_time' || $date_type == 'date_time_range' ? 'DATETIME' : 'DATE';
								if ( $date_type == 'date' || $date_type == 'date_time' ) {
									$meta_key = $field->getMetaKey();

									if ( $search_type == 'single' ) {
										$cf_meta_query[] = [
											'key'     => $meta_key,
											'value'   => $field->sanitize_date_field( $values, [ 'range' => false ] ),
											'compare' => '=',
											'type'    => $type
										];
									} else {
										$dates           = $field->sanitize_date_field( $values, [ 'range' => true ] );
										$start_date      = $dates['start'];
										$end_date        = $dates['end'];
										$cf_meta_query[] = [
											'key'     => $meta_key,
											'value'   => [ $start_date, $end_date ],
											'compare' => 'BETWEEN',
											'type'    => $type
										];
									}

								} else if ( $date_type == 'date_range' || $date_type == 'date_range_time' ) {
									$start_meta_key = $field->getDateRangeMetaKey( 'start' );
									$end_meta_key   = $field->getDateRangeMetaKey( 'end' );

									if ( $search_type == 'single' ) {
										$start_date = $end_date = $field->sanitize_date_field( $values, [ 'range' => false ] );
									} else {
										$dates      = $field->sanitize_date_field( $values, [ 'range' => true ] );
										$start_date = $dates['start'];
										$end_date   = $dates['end'];
									}
									if ( $start_date ) {
										$cf_meta_query[] = [
											'key'     => $start_meta_key,
											'value'   => $start_date,
											'compare' => $search_type == 'single' ? '<=' : '>=',
											'type'    => $type
										];
									}
									if ( $end_date ) {
										$cf_meta_query[] = [
											'key'     => $end_meta_key,
											'value'   => $end_date,
											'compare' => $search_type == 'single' ? '>=' : '<=',
											'type'    => $type
										];
									}
								}

							} else {
								$operator        = ( in_array( $field->getType(), [
									'text',
									'textarea',
									'url'
								] ) ) ? 'LIKE' : '=';
								$cf_meta_query[] = [
									'key'     => $key,
									'value'   => sanitize_text_field( $values ),
									'compare' => $operator
								];
							}
						}
					}
				}
				// Hook Added By rashid. Translatepress Need this hook.
				$cf_meta_query = apply_filters( 'rtcl_listing_custom_fields_meta_query', $cf_meta_query );
				$meta_query    = array_merge( $meta_query, $cf_meta_query );

			}
		}

		return array_filter( apply_filters( 'rtcl_listing_query_meta_query', $meta_query, $this ) );
	}


	/**
	 * Appends tax queries to an array.
	 *
	 * @param array $tax_query Tax query.
	 * @param bool $main_query If is main query.
	 *
	 * @return array
	 */
	public function get_tax_query( $tax_query = [], $main_query = false ) {
		if ( ! is_array( $tax_query ) ) {
			$tax_query = [
				'relation' => 'AND',
			];
		}

		if ( ! Functions::is_listings() && ( ! empty( $_GET['rtcl_location'] ) || ! empty( $_GET['rtcl_category'] ) || ! empty( $_GET['location'] ) || ! empty( $_GET['category'] ) ) ) {
			$location = ! empty( $_GET['location'] ) ? Functions::clean( $_GET['location'] ) : ( ! empty( $_GET['rtcl_location'] ) ? Functions::clean( $_GET['rtcl_location'] ) : '' );
			if ( $location ) {
				$locations = array_map( 'sanitize_title', explode( ',', $location ) );
				$field     = 'slug';

				if ( is_numeric( $locations[0] ) ) {
					$field     = 'term_id';
					$locations = array_map( 'absint', $locations );
					// Check numeric slugs.
					foreach ( $locations as $cat ) {
						$the_cat = get_term_by( 'slug', $cat, rtcl()->location );
						if ( false !== $the_cat ) {
							$locations[] = $the_cat->term_id;
						}
					}
				}
				$tax_query[] = [
					'taxonomy' => rtcl()->location,
					'terms'    => $locations,
					'field'    => $field
				];
			}

			$category = ! empty( $_GET['category'] ) ? Functions::clean( $_GET['category'] ) : ( ! empty( $_GET['rtcl_category'] ) ? Functions::clean( $_GET['rtcl_category'] ) : '' );
			if ( $category ) {
				$categories = array_map( 'sanitize_title', explode( ',', $category ) );
				$field      = 'slug';

				if ( is_numeric( $categories[0] ) ) {
					$field      = 'term_id';
					$categories = array_map( 'absint', $categories );
					// Check numeric slugs.
					foreach ( $categories as $cat ) {
						$the_cat = get_term_by( 'slug', $cat, rtcl()->category );
						if ( false !== $the_cat ) {
							$categories[] = $the_cat->term_id;
						}
					}
				}
				$tax_query[] = [
					'taxonomy' => rtcl()->category,
					'terms'    => $categories,
					'field'    => $field
				];
				$tax_query[] = [
					'taxonomy' => rtcl()->category,
					'terms'    => $categories,
					'field'    => $field
				];
			}
		}

		return array_filter( apply_filters( 'rtcl_listing_query_tax_query', $tax_query, $this ) );
	}

	/**
	 * Appends excluded author user ids.
	 *
	 * @param array $author_ids Author ids.
	 * @param bool $main_query If is main query.
	 *
	 * @return array
	 */
	public function get_author__not_in( $author_ids = [], $main_query = false ) {
		$author_ids      = ! is_array( $author_ids ) ? [] : $author_ids;
		$current_user_id = get_current_user_id();
		if ( ! empty( $current_user_id ) ) {
			$blockedUserIds = Functions::getBlockedUserIds( $current_user_id );
			if ( ! empty( $blockedUserIds ) ) {
				$author_ids = array_merge( $author_ids, $blockedUserIds );
			}
		}

		return apply_filters( 'rtcl_listing_query_author__not_in', $author_ids, $this );
	}


	/**
	 * Appends excluded listings ids.
	 *
	 * @param array $post_ids Post ids.
	 * @param bool $main_query If is main query.
	 *
	 * @return array
	 */
	public function get_post__not_in( $post_ids = [], $main_query = false ) {
		$post_ids        = ! is_array( $post_ids ) ? [] : $post_ids;
		$current_user_id = get_current_user_id();
		if ( ! empty( $current_user_id ) ) {
			$blockedPostIds = Functions::getBlockedListingIds( $current_user_id );
			if ( ! empty( $blockedPostIds ) ) {
				$post_ids = array_merge( $post_ids, $blockedPostIds );
			}
		}

		return apply_filters( 'rtcl_listing_query_post__not_in', $post_ids, $this );
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return apply_filters( 'rtcl_get_query_vars', $this->query_vars );
	}

	/**
	 * Get query current active query var.
	 *
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;

		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}

		return '';
	}


	/**
	 * Get page title for an endpoint.
	 *
	 * @param string $endpoint Endpoint key.
	 *
	 * @return string
	 */
	public static function get_endpoint_title( $endpoint ) {

		switch ( $endpoint ) {
			case 'submission':
			case 'promote':
				$title = __( 'Promote your ad', 'classified-listing' );
				break;
			case 'payment-receipt':
				$title = __( 'Order received', 'classified-listing' );
				break;
			case 'payment-failure':
				$title = __( 'Order failed', 'classified-listing' );
				break;
			case 'payments':
				$title = __( 'Payments Orders', 'classified-listing' );
				break;
			case 'listings':
				$title = __( 'My Listings', 'classified-listing' );
				break;
			case 'favourites':
				$title = __( 'My Favourites Listings', 'classified-listing' );
				break;
			case 'chat':
				$title = __( 'Chat', 'classified-listing' );
				break;
			case 'edit-account':
				$title = __( 'Account details', 'classified-listing' );
				break;
			case 'verify':
				$title = __( 'Verify your account', 'classified-listing' );
				break;
			case 'lost-password':
				$title = __( 'Lost password', 'classified-listing' );
				break;
			default:
				$title = '';
				break;
		}

		return apply_filters( 'rtcl_endpoint_' . $endpoint . '_title', $title, $endpoint );
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported.
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) { // WPCS: input var ok, CSRF ok.
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) ); // WPCS: input var ok, CSRF ok.
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}


}
