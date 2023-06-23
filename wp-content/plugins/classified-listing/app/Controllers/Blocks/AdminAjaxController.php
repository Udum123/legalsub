<?php

namespace Rtcl\Controllers\Blocks;

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class AdminAjaxController
{
	public function __construct()
	{
		//get categories for inspector controller
		add_action('wp_ajax_rtcl_gb_categories', [$this, 'rtcl_gb_categories']);
		add_action('wp_ajax_nopriv_rtcl_gb_categories', [$this, 'rtcl_gb_categories']);
		//get locations for inspector controller
		add_action('wp_ajax_rtcl_gb_location_ajax', [$this, 'rtcl_gb_location_ajax']);
		add_action('wp_ajax_nopriv_rtcl_gb_location_ajax', [$this, 'rtcl_gb_location_ajax']);
		//get listing type for inspector controller
		add_action('wp_ajax_rtcl_gb_listing_type_ajax', [$this, 'rtcl_gb_listing_type_ajax']);
		add_action('wp_ajax_nopriv_rtcl_gb_listing_type_ajax', [$this, 'rtcl_gb_listing_type_ajax']);
		//get promotion for inspector controller
		add_action('wp_ajax_rtcl_gb_listing_promotion_ajax', [$this, 'rtcl_gb_listing_promotion_ajax']);
		add_action('wp_ajax_nopriv_rtcl_gb_listing_promotion_ajax', [$this, 'rtcl_gb_listing_promotion_ajax']);
		//get categories for category box block
		add_action('wp_ajax_nopriv_rtcl_gb_listing_cat_box', [$this, 'rtcl_gb_listing_cat_box']);
		add_action('wp_ajax_rtcl_gb_listing_cat_box', [$this, 'rtcl_gb_listing_cat_box']);

		//get location for all location block
		add_action('wp_ajax_nopriv_rtcl_gb_all_locations', [$this, 'rtcl_gb_all_locations']);
		add_action('wp_ajax_rtcl_gb_all_locations', [$this, 'rtcl_gb_all_locations']);

		//get location for all location block
		add_action('wp_ajax_nopriv_rtcl_gb_single_location', [$this, 'rtcl_gb_single_location']);
		add_action('wp_ajax_rtcl_gb_single_location', [$this, 'rtcl_gb_single_location']);

		//get categories for category box block
		add_action('wp_ajax_nopriv_rtcl_gb_get_all_image_size_ajax', [$this, 'rtcl_gb_get_all_image_size_ajax']);
		add_action('wp_ajax_rtcl_gb_get_all_image_size_ajax', [$this, 'rtcl_gb_get_all_image_size_ajax']);
	}

	public static function rtcl_gb_single_location_query($data)
	{
		$results = [];
		if (isset($data['location']) && !empty($data['location'])) {
			$term = get_term($data['location'], 'rtcl_location');
			if ($term && !is_wp_error($term)) {
				$results['title'] = $term->name;
				$results['count'] = $term->count;
				$results['permalink'] = get_term_link($term);
			}
		} else {
			$results['title'] = __('Please Select a Location and Background', 'classified-listing');
			$results['count'] = '';
			$results['permalink'] = '#';
		}
		return $results;
	}

	public function rtcl_gb_single_location()
	{
		if (!wp_verify_nonce($_POST['rtcl_nonce'], 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}
		$data = $_POST['attributes'];

		$results = self::rtcl_gb_single_location_query($data);

		if (!empty($results)) {
			wp_send_json_success($results);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public static function rtcl_gb_all_location_query($data)
	{

		$results = [];
		$data['location_limit'] = isset($data['location_limit']) ? $data['location_limit'] : 5;

		$args = [
			'taxonomy'   => 'rtcl_location',
			'hide_empty' => $data['hide_empty'] == 'true' ? true : false,
			'order'      => 'asc',
		];

		if ($data['orderby'] == 'custom') {
			$args['orderby'] = 'meta_value_num';
			$args['order'] = $data['sortby'] ? $data['sortby'] : 'asc';
			$args['meta_key'] = '_rtcl_order';
		} else {
			$args['orderby'] = $data['orderby'] ? $data['orderby'] : 'date';
			$args['order'] = $data['sortby'] ? $data['sortby'] : 'asc';
		}

		if ($data['enable_parent'] == 'true') {
			$args["parent"] = 0;
		}

		if ('selected' == $data['location_type'] && !empty($data['locations'])) {
			$data['locations'] = wp_list_pluck($data['locations'], 'value');
			$args['include'] = !empty($data['locations']) ? $data['locations'] : array();
		} elseif ('selected' == $data['location_type'] && empty($data['locations'])) {
			return array();
		}
		$terms = get_terms($args);

		if ('all' == $data['location_type'] && $data['location_limit'] && !is_wp_error($terms)) {
			$number = !empty($data['location_limit']) ? $data['location_limit'] : 4;
			$terms = array_slice($terms, 0, $number);
		}

		if (!is_wp_error($terms)) :
			foreach ($terms as $term) {
				$order = get_term_meta($term->term_id, '_rtcl_order', true);
				$count = $term->count;
				// if (!empty($count)):
				//     $count = sprintf(_n('%s Ad', '%s Ads', $count, 'classified-listing'), $count);
				// endif;
				//location children list
				$child_html = '';
				$child_args = array(
					'taxonomy'   => 'rtcl_location',
					'parent'     => $term->term_id,
					'number'     => $data['sub_location_limit'],
					'hide_empty' => false,
					'orderby'    => 'count',
					'order'      => 'DESC',
				);
				$child_terms = get_terms($child_args);
				if (!empty($child_terms) && !is_wp_error($child_terms)) {
					foreach ($child_terms as $child_trm) {
						$child_html .= sprintf(
							'<li><i class="rtcl-icon rtcl-icon-angle-right"></i><a href="%s">%s (%s)</a></li>',
							get_term_link($child_trm),
							$child_trm->name,
							$child_trm->count
						);
					}
				}

				$results[] = [
					'name'        => $term->name,
					'description' => $term->description,
					'order'       => (int)$order,
					'permalink'   => get_term_link($term),
					'count'       => $count,
					'child_html'  => $child_html,
				];

				$child_html = '';

				if ('count' == $args['orderby']) {
					if ('desc' == $args['order']) {
						usort($results, function ($a, $b) {
							return $b['count'] - $a['count'];
						});
					}
					if ('asc' == $args['order']) {
						usort($results, function ($a, $b) {
							return $a['count'] - $b['count'];
						});
					}
				}
			}
		endif;

		return $results;
	}

	public function rtcl_gb_all_locations()
	{
		if (!wp_verify_nonce($_POST['rtcl_nonce'], 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}
		$data = $_POST['attributes'];
		$results = self::rtcl_gb_all_location_query($data);

		if (!empty($results)) {
			wp_send_json_success($results);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function rtcl_gb_get_all_image_size_ajax()
	{
		global $_wp_additional_image_sizes;
		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$sizes = array();
		$image_name_sizes = array();
		foreach (get_intermediate_image_sizes() as $s) {
			$sizes[$s] = array(0, 0);
			if (in_array($s, array('thumbnail', 'medium', 'medium_large', 'large'))) {
				$sizes[$s][0] = get_option($s . '_size_w');
				$sizes[$s][1] = get_option($s . '_size_h');
			} else {
				if (isset($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes[$s])) {
					$sizes[$s] = array($_wp_additional_image_sizes[$s]['width'], $_wp_additional_image_sizes[$s]['height']);
				}
			}
		}
		foreach ($sizes as $size => $atts) {
			$remove_uh_sizes = str_replace('_', ' ', $size);
			$remove_uh_sizes = ucwords(str_replace('-', ' ', $remove_uh_sizes));
			$image_name_sizes[$size] = $remove_uh_sizes . ' ' . implode('x', $atts);
		}
		$image_name_sizes['custom'] = 'Custom';

		if (!empty($image_name_sizes)) {
			wp_send_json_success($image_name_sizes);
		} else {
			wp_send_json_error("no image size");
		}
		wp_die();
	}

	public function rtcl_gb_listing_promotion_ajax()
	{
		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}
		$promotions = Options::get_listing_promotions();
		if (!empty($promotions)) :
			wp_send_json($promotions);
		endif;
		wp_die();
	}

	public function rtcl_gb_listing_type_ajax()
	{
		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$listing_types_arr = ["all" => "All"];
		$listing_types = Functions::get_listing_types();

		if (!empty($listing_types) && !is_wp_error($listing_types)) {
			foreach ($listing_types as $id => $name) {
				$listing_types_arr[$id] = $name;
			}
		}

		if (!empty($listing_types_arr)) :
			wp_send_json($listing_types_arr);
		endif;
		wp_die();
	}

	public function rtcl_gb_location_ajax()
	{

		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$args = [
			'taxonomy'     => 'rtcl_location',
			'fields'       => 'id=>name',
			'height_empty' => true,
		];

		$location_dropdown = [];
		$terms = get_terms($args);
		if (!is_wp_error($terms)) {
			foreach ($terms as $id => $name) {
				$location_dropdown[$id] = $name;
			}
		}

		if (!empty($location_dropdown)) :
			wp_send_json($location_dropdown);
		endif;

		wp_reset_postdata();
		wp_die();
	}

	public static function rtcl_cat_box_query($data)
	{
		$results = [];
		$data['sub_category_limit'] = isset($data['sub_category_limit']) ? $data['sub_category_limit'] : 5;
		$data['category_limit'] = isset($data['category_limit']) ? $data['category_limit'] : 8;

		$data['hide_empty'] = $data['hide_empty'] == 'true' ? true : false;
		$data['count_child'] = $data['count_child'] == 'true' ? 1 : 0;

		if (!empty($data['cats'])) :
			$data['cats'] = wp_list_pluck($data['cats'], 'value');
		endif;

		$args = [
			'include'    => isset($data['cats']) ? $data['cats'] : [],
			'hide_empty' => $data['hide_empty'],
			'order'      => 'asc',
		];

		if ($data['orderby'] == 'custom') {
			$args['orderby'] = 'meta_value_num';
			$args['order'] = $data['sortby'] ? $data['sortby'] : 'asc';
			$args['meta_key'] = '_rtcl_order';
		} else {
			$args['orderby'] = $data['orderby'] ? $data['orderby'] : 'date';
			$args['order'] = $data['sortby'] ? $data['sortby'] : 'asc';
		}

		if ($data['enable_parent'] == 'true') {
			$args["parent"] = 0;
		}

		$terms = get_terms('rtcl_category', $args);

		if (!empty($data['category_limit']) && !is_wp_error($terms)) {
			$number = $data['category_limit'];
			$terms = array_slice($terms, 0, $number);
		}

		if (!empty($terms) && !is_wp_error($terms)) :
			foreach ($terms as $term) {

				$order = get_term_meta($term->term_id, '_rtcl_order', true);
				$icon_html = '';
				if ($data['icon_type'] == 'icon') {
					$icon = get_term_meta($term->term_id, '_rtcl_icon', true);
					if ($icon) {
						$icon_html = sprintf('<span class="rtcl-icon rtcl-icon-%s"></span>', $icon);
					}
				} elseif ($data['icon_type'] == 'image') {
					//image size
					$image_size = isset($data['image_size']) ? $data['image_size'] : 'rtcl-thumbnail';
					if ('custom' == $image_size) {
						if (isset($data['custom_image_width']) && isset($data['custom_image_height'])) {
							$image_size = array(
								$data['custom_image_width'],
								$data['custom_image_height'],
							);
						}
					}
					$image = get_term_meta($term->term_id, '_rtcl_image', true);
					if ($image) {
						$image = wp_get_attachment_image_src($image, $image_size);
						$width = $image[1];
						$height = $image[2];
						$image = $image[0];
						$icon_html = sprintf('<img src="%s" alt="%s" width="%s" height="%s" />', $image, $term->name, $width, $height);
					} else {
						$icon = get_term_meta($term->term_id, '_rtcl_icon', true);
						if ($icon) {
							$icon_html = sprintf('<span class="rtcl-icon rtcl-icon-%s"></span>', $icon);
						}
					}
				}

				//category children list
				$child_html = '';
				$child_args = array(
					'taxonomy'   => 'rtcl_category',
					'parent'     => $term->term_id,
					'number'     => $data['sub_category_limit'],
					'hide_empty' => false,
					'orderby'    => 'count',
					'order'      => 'DESC',
				);
				$child_terms = get_terms($child_args);
				if (!empty($child_terms) && !is_wp_error($child_terms)) {
					foreach ($child_terms as $child_trm) {
						$child_html .= sprintf(
							'<li><i class="rtcl-icon rtcl-icon-angle-right"></i><a href="%s">%s (%s)</a></li>',
							get_term_link($child_trm),
							$child_trm->name,
							$child_trm->count
						);
					}
				}
				$count = Functions::get_listings_count_by_taxonomy(
					$term->term_id,
					rtcl()->category,
					$data['count_child']
				);
				$results[] = [
					'name'        => $term->name,
					'description' => $term->description,
					'order'       => (int)$order,
					'permalink'   => get_term_link($term),
					'count'       => $count,
					'icon_html'   => $icon_html,
					'child_html'  => $child_html,
				];
				$child_html = '';

				// if ('count' == $args['orderby']) {
				// 	if ('desc' == $args['order']) {
				// 		usort($results, function ($a, $b) {
				// 			return $b['count'] - $a['count'];
				// 		});
				// 	}
				// 	if ('asc' == $args['order']) {
				// 		usort($results, function ($a, $b) {
				// 			return $a['count'] - $b['count'];
				// 		});
				// 	}
				// }
			}
		endif;

		return $results;
	}

	public function rtcl_gb_listing_cat_box()
	{
		if (!wp_verify_nonce($_POST['rtcl_nonce'], 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}
		$data = $_POST['attributes'];
		$results = self::rtcl_cat_box_query($data);

		if (!empty($results)) {
			wp_send_json_success($results);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function rtcl_gb_categories()
	{
		$rtcl_nonce = $_POST['rtcl_nonce'];
		if (!wp_verify_nonce($rtcl_nonce, 'rtcl-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'classified-listing'));
		}

		$args = [
			'taxonomy' => 'rtcl_category',
			'fields'   => 'id=>name',
		];

		if ($_POST['portion'] === 'listing') :
			$category_dropdown = [];
			$args['hide_empty'] = true;
		elseif ($_POST['portion'] === 'catbox') :
			$category_dropdown = [];
			$args['hide_empty'] = false;
			$args['parent'] = 0;
		else :
			$category_dropdown = [];
		endif;

		$terms = get_terms($args);

		if (!empty($terms) && !is_wp_error($terms)) {
			foreach ($terms as $id => $name) {
				$category_dropdown[$id] = html_entity_decode($name);
			}
		}

		if (!empty($category_dropdown)) :
			wp_send_json($category_dropdown);
		endif;

		wp_reset_postdata();
		wp_die();
	}
}
