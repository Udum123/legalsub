<?php

namespace Rtcl\Controllers\Ajax;

use Rtcl\Helpers\Functions;
use WP_User_Query;

class InlineSearchAjax
{

	public static function init() {
		add_action('wp_ajax_rtcl_get_all_cat_list_for_modal', array(__CLASS__, 'rtcl_get_all_cat_list_for_modal'));
		add_action('wp_ajax_nopriv_rtcl_get_all_cat_list_for_modal', array(
			__CLASS__,
			'rtcl_get_all_cat_list_for_modal'
		));

		add_action('wp_ajax_rtcl_get_all_location_list_for_modal', array(
			__CLASS__,
			'rtcl_get_all_location_list_for_modal'
		));
		add_action('wp_ajax_nopriv_rtcl_get_all_location_list_for_modal', array(
			__CLASS__,
			'rtcl_get_all_location_list_for_modal'
		));

		add_action('wp_ajax_rtcl_inline_search_autocomplete', array(__CLASS__, 'rtcl_inline_search_autocomplete'));
		add_action('wp_ajax_nopriv_rtcl_inline_search_autocomplete', array(
			__CLASS__,
			'rtcl_inline_search_autocomplete'
		));

		add_action('wp_ajax_rtcl_json_search_taxonomy', [__CLASS__, 'rtcl_inline_search_autocomplete']);
		add_action('wp_ajax_rtcl_json_search_users', [__CLASS__, 'rtcl_json_search_users']);
		add_action('wp_ajax_nopriv_rtcl_json_search_taxonomy', [__CLASS__, 'rtcl_inline_search_autocomplete']);
		add_action('wp_ajax_rtcl_ajax_taxonomy_filter_get_sub_level_html', [
			__CLASS__,
			'rtcl_ajax_taxonomy_filter_get_sub_level_html'
		]);
		add_action('wp_ajax_nopriv_rtcl_ajax_taxonomy_filter_get_sub_level_html', [
			__CLASS__,
			'rtcl_ajax_taxonomy_filter_get_sub_level_html'
		]);
	}

	/**
	 * Search for customers and return json.
	 */
	public static function rtcl_json_search_users() {
		$suggestions = [];
		if (!Functions::verify_nonce()) {
			wp_send_json_error(esc_html__("Session error !!", "classified-listing"));
		}
		$search_term = isset($_REQUEST['term']) ? (string)Functions::clean(wp_unslash($_REQUEST['term'])) : '';
		if (!$search_term) {
			wp_send_json_error(esc_html__("Please provide all field!!", "classified-listing"));
		}

		$args = [
			'order'          => 'ASC',
			'orderby'        => 'display_name',
			'number'         => 20,
			'search'         => '*' . esc_attr($search_term) . '*',
			'search_columns' => ['user_login', 'user_email', 'user_nicename']
		];

		$wp_user_query = new WP_User_Query(apply_filters('rtcl_json_search_users_query_args', $args));
		$authors = $wp_user_query->get_results();
		if (!empty($authors)) {
			foreach ($authors as $author) {
				$author_info = get_userdata($author->ID);
				$user_name = $author_info->first_name . ' ' . $author_info->last_name;
				$user_name = trim($user_name) ? $user_name : $author_info->display_name;
				$suggestions[] = [
					'id'     => $author->ID,
					'label'  => sprintf(
					/* translators: $1: customer name, $2 customer id, $3: customer email */
						esc_html__('%1$s (#%2$s &ndash; %3$s)', 'classified-listing'),
						$user_name,
						$author->ID,
						$author_info->user_email
					),
					'target' => ''
				];
			}
		}


		wp_send_json(apply_filters('rtcl_json_search_found_users', $suggestions));
	}


	static function rtcl_ajax_taxonomy_filter_get_sub_level_html() {
		do_action('rtcl_set_local');
		$args = wp_parse_args(
			$_REQUEST,
			[
				'taxonomy' => rtcl()->category,
				'parent'   => -1,
				'instance' => []
			]
		);
		wp_send_json_success(Functions::get_sub_terms_filter_html($args));
	}

	public static function rtcl_inline_search_autocomplete() {
		$suggestions = [];
		$q = isset($_REQUEST['term']) ? (string)Functions::clean(wp_unslash($_REQUEST['term'])) : '';
		$type = isset($_REQUEST['type']) ? (string)Functions::clean(wp_unslash($_REQUEST['type'])) : '';
		if (!$type || !$q) {
			wp_send_json_error(esc_html__("Please provide all field!!", "classified-listing"));
		}
		if ($type === 'listing') {
			// Query for suggestions
			$args = array(
				'post_type'        => rtcl()->post_type,
				'posts_per_page'   => 20,
				'post_status'      => 'publish',
				'orderby'          => 'title',
				'order'            => 'asc',
				'suppress_filters' => false,
				'fields'           => 'ids',
				's'                => $q,
			);
			$tax_queries = array();
			$general_settings = Functions::get_option('rtcl_general_settings');
			if (isset($_REQUEST['location_slug']) && !empty($_REQUEST['location_slug']) && $location = get_term_by('slug', $_REQUEST['location_slug'], rtcl()->location)) {
				$tax_queries[] = array(
					'taxonomy'         => rtcl()->location,
					'field'            => 'term_id',
					'terms'            => $location->term_id,
					'include_children' => isset($general_settings['include_results_from']) && in_array('child_categories',
							$general_settings['include_results_from']),
				);
			}

			if (isset($_REQUEST['category_slug']) && !empty($_REQUEST['category_slug']) && $category = get_term_by('slug', $_REQUEST['category_slug'], rtcl()->category)) {
				$tax_queries[] = array(
					'taxonomy'         => rtcl()->category,
					'field'            => 'term_id',
					'terms'            => $category->term_id,
					'include_children' => isset($general_settings['include_results_from']) && in_array('child_locations',
							$general_settings['include_results_from']),
				);
			}

			if (!empty($tax_queries)) {
				$args['tax_query'] = (count($tax_queries) > 1) ? array_merge(array('relation' => 'AND'),
					$tax_queries) : $tax_queries;
			}
			$result = new \WP_Query(apply_filters('rtcl_inline_search_autocomplete_args', $args, $type, $_REQUEST));

			// Initialise suggestions array
			if (!empty($result->posts)) {
				foreach ($result->posts as $post_id) {
					$post = get_post($post_id);
					$suggestions[] = [
						'id'     => $post_id,
						'label'  => !empty($post->post_title) ? $post->post_title : esc_html__("Empty listing title", 'classified-listing'),
						'target' => get_the_permalink($post_id)
					];
				}
			}
		} else if (in_array($type, ['location', 'category'])) {
			$args = array(
				'taxonomy'   => $type === 'location' ? rtcl()->location : rtcl()->category,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => 20,
				'name__like' => $q
			);
			$terms = get_terms(apply_filters('rtcl_inline_search_autocomplete_args', $args, $type, $_REQUEST));
			if (!empty($terms)) {
				foreach ($terms as $term) {
					$suggestions[] = [
						'id'     => $term->term_id,
						'label'  => $term->name,
						'target' => $term->slug
					];
				}
			}
		}

		wp_send_json($suggestions);
	}

	public static function rtcl_get_all_cat_list_for_modal() {
		do_action('rtcl_set_local');
		$transient_name = rtcl()->get_transient_name('', rtcl()->category, 'list');
		if (false === ($terms = get_transient($transient_name))) {
			$cats = Functions::get_one_level_categories();
			$terms = Functions::get_multilevel_terms_data($cats);
			set_transient($transient_name, $terms, WEEK_IN_SECONDS);
		}
		$response = array(
			'success'    => true,
			'categories' => $terms
		);
		wp_send_json($response);
	}

	public static function rtcl_get_all_location_list_for_modal() {
		do_action('rtcl_set_local');
		$transient_name = rtcl()->get_transient_name('', rtcl()->location, 'list');
		if (false === ($terms = get_transient($transient_name))) {
			$topLocations = Functions::get_one_level_locations();
			$terms = Functions::get_multilevel_terms_data($topLocations);
			set_transient($transient_name, $terms, WEEK_IN_SECONDS);
		}
		$response = array(
			'success'   => true,
			'locations' => $terms
		);
		wp_send_json($response);
	}
}