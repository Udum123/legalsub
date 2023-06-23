<?php

namespace Rtcl\Shortcodes;


use Rtcl\Controllers\Shortcodes;
use Rtcl\Helpers\BlockFns;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use WP_Query;

class Listings
{

	/**
	 * Shortcode type.
	 *
	 * @since 1.5.56
	 * @var   string
	 */
	protected $type = 'listings';

	/**
	 * Attributes.
	 *
	 * @since 1.5.56
	 * @var   array
	 */
	protected $attributes = array();

	/**
	 * Query args.
	 *
	 * @since 1.5.56
	 * @var   array
	 */
	protected $query_args = array();

	/**
	 * Initialize shortcode.
	 *
	 * @param array  $attributes Shortcode attributes.
	 * @param string $type       Shortcode type.
	 *
	 * @since 1.5.56
	 */
	public function __construct($attributes = array(), $type = 'listings') {
		$this->type = $type;
		$this->attributes = $this->parse_attributes($attributes);
		$this->query_args = $this->parse_query_args();
	}


	/**
	 * Parse attributes.
	 *
	 * @param array $attributes Shortcode attributes.
	 *
	 * @return array
	 * @since  1.5.56
	 */
	protected function parse_attributes($attributes) {
		$attributes = $this->parse_legacy_attributes($attributes);
		$general_settings = Functions::get_option('rtcl_general_settings');
		$attributes = shortcode_atts(
			array(
				'limit'             => '-1',      // Results limit.
				'columns'           => '',        // Number of columns.
				'orderby'           => !empty($general_settings['orderby']) ? $general_settings['orderby'] : 'date',        // menu_order, title, date, rand, price, popularity, rating, or id.
				'order'             => !empty($general_settings['order']) ? $general_settings['order'] : 'DESC',        // ASC or DESC.
				'ids'               => '',        // Comma separated IDs.
				'category'          => '',        // Comma separated category slugs or ids.
				'cat_operator'      => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
				'location'          => '',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
				'location_operator' => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
				'attribute'         => '',        // Single attribute slug.
				'terms'             => '',        // Comma separated term slugs or ids.
				'terms_operator'    => 'IN',      // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
				'visibility'        => 'visible', // Product visibility setting. Possible values are 'visible', 'catalog', 'search', 'hidden'.
				'class'             => '',        // HTML class.
				'page'              => 1,         // Page for pagination.
				'paginate'          => false,     // Should results be paginated.
				'cache'             => true,      // Should shortcode output be cached.,
				'map'               => 0,
				'authors'           => '',
				'filterby'          => ''
			),
			$attributes,
			$this->type
		);

		return apply_filters('rtcl_shortcode_listings_attributes', $attributes);
	}

	protected function parse_legacy_attributes($attributes) {
		$mapping = array(
			'listings_per_page' => 'limit'
		);

		foreach ($mapping as $old => $new) {
			if (isset($attributes[$old])) {
				$attributes[$new] = $attributes[$old];
				unset($attributes[$old]);
			}
		}

		return $attributes;
	}


	/**
	 * Parse query args.
	 *
	 * @return array
	 * @since  1.5.56
	 */
	protected function parse_query_args() {
		$query_args = array(
			'post_type'           => rtcl()->post_type,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false === Functions::string_to_bool($this->attributes['paginate']),
			'orderby'             => empty($_GET['orderby']) ? $this->attributes['orderby'] : Functions::clean(wp_unslash($_GET['orderby'])), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'posts_per_page'      => intval($this->attributes['limit']),
			'paged'               => Pagination::get_page_number()
		);
		if ($this->attributes['authors'] && $authors = explode(',', $this->attributes['authors'])) {
			if (!empty($authors)) {
				$query_args['author__in'] = $authors;
			}
		}

		$orderby_value = explode('-', $query_args['orderby']);
		$orderby = esc_attr($orderby_value[0]);
		$order = !empty($orderby_value[1]) ? $orderby_value[1] : strtoupper($this->attributes['order']);
		$query_args['orderby'] = $orderby;
		$query_args['order'] = $order;

		if (Functions::string_to_bool($this->attributes['paginate'])) {
			$this->attributes['page'] = absint(empty($_GET['listing-page']) ? 1 : $_GET['listing-page']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$ordering_args = rtcl()->query->get_catalog_ordering_args($query_args['orderby'], $query_args['order']);
		$query_args['orderby'] = $ordering_args['orderby'];
		$query_args['order'] = $ordering_args['order'];
		if ($ordering_args['meta_key']) {
			$query_args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		}

		if (1 < $this->attributes['page']) {
			$query_args['paged'] = absint($this->attributes['page']);
		}

		if (!Functions::is_listings() && !empty($_GET['q'])) {
			$query_args['s'] = (string)Functions::clean(wp_unslash($_GET['q']));
		}

		$query_args['meta_query'] = rtcl()->query->get_meta_query();


		// Categories.
		$this->set_categories_query_args($query_args);

		// Locations.
		$this->set_locations_query_args($query_args);

		// Override tax query from filter with $_GET params location, category, rtcl_location, rtcl_category
		$query_args['tax_query'] = rtcl()->query->get_tax_query(!empty($query_args['tax_query']) ? $query_args['tax_query'] : []);

		// Visibility.
		$this->set_visibility_query_args($query_args);

		// IDs.
		$this->set_ids_query_args($query_args);

		// Set specific types query args.
		if (method_exists($this, "set_{$this->type}_query_args")) {
			$this->{"set_{$this->type}_query_args"}($query_args);
		}


		$query_args = apply_filters('rtcl_shortcode_listings_query_args', $query_args, $this->attributes, $this->type);

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}


	/**
	 * Set visibility query args.
	 *
	 * @param array $query_args Query args.
	 *
	 * @since 1.5.56
	 */
	protected function set_visibility_query_args(&$query_args) {
		if (method_exists($this, 'set_visibility_' . $this->attributes['visibility'] . '_query_args')) {
			$this->{'set_visibility_' . $this->attributes['visibility'] . '_query_args'}($query_args);
		} else {
			$query_args['tax_query'] = array_merge($query_args['tax_query'], rtcl()->query->get_tax_query()); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}
	}


	/**
	 * Set ids query args.
	 *
	 * @param array $query_args Query args.
	 *
	 * @since 1.5.56
	 */
	protected function set_ids_query_args(&$query_args) {
		if (!empty($this->attributes['ids'])) {
			$ids = array_map('trim', explode(',', $this->attributes['ids']));

			if (1 === count($ids)) {
				$query_args['p'] = $ids[0];
			} else {
				$query_args['post__in'] = $ids;
			}
		}
	}


	/**
	 * Set categories query args.
	 *
	 * @param array $query_args Query args.
	 *
	 * @since 1.5.56
	 */
	protected function set_categories_query_args(&$query_args) {
		if (!empty($this->attributes['category'])) {
			$categories = array_map('sanitize_title', explode(',', $this->attributes['category']));
			$field = 'slug';

			if (is_numeric($categories[0])) {
				$field = 'term_id';
				$categories = array_map('absint', $categories);
				// Check numeric slugs.
				foreach ($categories as $cat) {
					$the_cat = get_term_by('slug', $cat, rtcl()->category);
					if (false !== $the_cat) {
						$categories[] = $the_cat->term_id;
					}
				}
			}

			$query_args['tax_query'][] = array(
				'taxonomy'         => rtcl()->category,
				'terms'            => $categories,
				'field'            => $field,
				'operator'         => $this->attributes['cat_operator'],

				/*
				 * When cat_operator is AND, the children categories should be excluded,
				 * as only products belonging to all the children categories would be selected.
				 */
				'include_children' => !('AND' === $this->attributes['cat_operator']),
			);
		}
	}


	/**
	 * Set categories query args.
	 *
	 * @param array $query_args Query args.
	 *
	 * @since 1.5.56
	 */
	protected function set_locations_query_args(&$query_args) {
		if (!empty($this->attributes['location'])) {
			$locations = array_map('sanitize_title', explode(',', $this->attributes['location']));
			$field = 'slug';

			if (is_numeric($locations[0])) {
				$field = 'term_id';
				$locations = array_map('absint', $locations);
				// Check numeric slugs.
				foreach ($locations as $cat) {
					$the_cat = get_term_by('slug', $cat, rtcl()->location);
					if (false !== $the_cat) {
						$locations[] = $the_cat->term_id;
					}
				}
			}

			$query_args['tax_query'][] = array(
				'taxonomy'         => rtcl()->location,
				'terms'            => $locations,
				'field'            => $field,
				'operator'         => $this->attributes['location_operator'],

				/*
				 * When cat_operator is AND, the children categories should be excluded,
				 * as only products belonging to all the children categories would be selected.
				 */
				'include_children' => !('AND' === $this->attributes['location_operator']),
			);
		}
	}

	/**
	 * Set meta query args for filter.
	 *
	 * @param array $query_args Query args.
	 *
	 * @since 1.5.56
	 */
	protected function set_meta_query_args(&$query_args) {

	}

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public static function get($atts) {
		return Shortcodes::shortcode_wrapper(array(__CLASS__, 'output'), $atts);
	}


	/**
	 * Get shortcode content.
	 *
	 * @return string
	 * @since 1.5.56
	 */
	public function get_content() {
		return $this->listing_loop();
	}

	/**
	 * Get wrapper classes.
	 *
	 * @return array
	 * @since  1.5.56
	 */
	protected function get_wrapper_classes() {
		$classes = array('rtcl', 'rtcl-listings-sc-wrapper');
		if ($this->attributes['map']) {
			$classes[] = 'has-map';
		}
		$classes[] = $this->attributes['class'];

		return $classes;
	}

	protected function get_query_results() {

		$query = new WP_Query($this->query_args);

		$paginated = !$query->get('no_found_rows');
		// TODO : Need to  caching here
		$results = (object)array(
			'ids'          => wp_parse_id_list($query->posts),
			'total'        => $paginated ? $query->found_posts : count($query->posts),
			'total_pages'  => $paginated ? $query->max_num_pages : 1,
			'per_page'     => (int)$query->get('posts_per_page'),
			'current_page' => $paginated ? (int)max(1, $query->get('paged', 1)) : 1,
		);

		return $results;
	}

	/**
	 * Loop over found products.
	 *
	 * @return string
	 * @since  1.5.56
	 */
	protected function listing_loop() {
		if ($this->attributes['map']) {
			wp_enqueue_script('rtcl-map');
		}
		$wrapper_classes = apply_filters('rtcl_shortcode_listings_wrapper_class', $this->get_wrapper_classes(), $this);
		$listings = $this->get_query_results();


		ob_start();
		do_action('rtcl_listing_loop_prepend_data');
		$listing_loop_prepend_data = ob_get_clean();
		ob_start();

		do_action("rtcl_shortcode_before_{$this->type}_loop_start", $this->attributes);

		if ($listings && $listings->ids) {
			// Prime caches to reduce future queries.
			if (is_callable('_prime_post_caches')) {
				_prime_post_caches($listings->ids);
			}
			// Setup the loop.
			Functions::setup_loop(
				array(
					'name'         => $this->type,
					'is_shortcode' => true,
					'is_search'    => false,
					'is_paginated' => Functions::string_to_bool($this->attributes['paginate']),
					'total'        => $listings->total,
					'total_pages'  => $listings->total_pages,
					'per_page'     => $listings->per_page,
					'current_page' => $listings->current_page,
				)
			);

			$original_post = $GLOBALS['post'];

			do_action("rtcl_shortcode_before_{$this->type}_loop", $this->attributes);

			// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
			if (Functions::string_to_bool($this->attributes['paginate'])) {
				do_action('rtcl_before_listing_loop');
			}

			Functions::listing_loop_start();
			if ($listing_loop_prepend_data) {
				echo wp_kses($listing_loop_prepend_data, BlockFns::kses_allowed_svg() );
			}
			if (Functions::get_loop_prop('total')) {
				foreach ($listings->ids as $product_id) {
					$GLOBALS['post'] = get_post($product_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata($GLOBALS['post']);

					// Render product template.
					Functions::get_template_part('content', 'listing');
				}
			}

			$GLOBALS['post'] = $original_post;
			Functions::listing_loop_end();

			// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
			if (Functions::string_to_bool($this->attributes['paginate'])) {
				do_action('rtcl_after_listing_loop');
			}

			do_action("rtcl_shortcode_after_{$this->type}_loop", $this->attributes);

			wp_reset_postdata();
			Functions::reset_loop();
		} else {

			/**
			 * Top listings
			 */
			if ($listing_loop_prepend_data) {
				Functions::listing_loop_start();
				echo $listing_loop_prepend_data;
				Functions::listing_loop_end();
			}

			do_action("rtcl_shortcode_{$this->type}_loop_no_results", $this->attributes);
		}

		$listings_html = ob_get_clean();

		ob_start();
		do_action("rtcl_shortcode_before_{$this->type}", $this->attributes);
		$before_html = ob_get_clean();

		ob_start();
		do_action("rtcl_shortcode_after_{$this->type}", $this->attributes);
		$after_html = ob_get_clean();
		return sprintf('<div class="%s">%s<div class="rtcl-listings-wrapper">%s</div>%s%s</div>',
			esc_attr(implode(' ', $wrapper_classes)),
			$before_html,
			$listings_html,
			$this->attributes['map'] ? apply_filters('rtcl_shortcode_listings_map_placeholder_html', '<div class="rtcl-search-map"><div class="rtcl-map-view" data-map-type="search"></div></div>') : '',
			$after_html
		);
	}

}
