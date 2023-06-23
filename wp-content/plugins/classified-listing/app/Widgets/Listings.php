<?php

namespace Rtcl\Widgets;


use Rtcl\Helpers\Functions;
use Rtcl\Models\WidgetFields;
use Rtcl\Resources\Options;
use WP_Widget;

/**
 * Class Listings
 *
 * @package Rtcl\Widgets
 */
class Listings extends WP_Widget
{

	protected $widget_slug;

	protected $defaults;

	protected $general_settings;

	public function __construct() {

		$this->widget_slug = 'rtcl-widget-listing';
		$this->general_settings = Functions::get_option('rtcl_general_settings');
		$this->defaults = array(
			'title'            => esc_html__('Listings', 'classified-listing'),
			'location'         => 0,
			'category'         => 0,
			'related_listings' => 0,
			'type'             => 'all',
			'limit'            => 8,
			'orderby'          => !empty($this->general_settings['orderby']) ? $this->general_settings['orderby'] : '',
			'order'            => !empty($this->general_settings['order']) ? $this->general_settings['order'] : '',
			'view'             => 'grid',
			'columns'          => 4,
			'tab_items'        => 3,
			'mobile_items'     => 1,
			'show_image'       => 1,
			'image_position'   => 'top',
			'show_category'    => 1,
			'show_location'    => 1,
			'show_labels'      => 1,
			'show_price'       => 1,
			'show_date'        => 1,
			'show_user'        => 1,
			'show_views'       => 1,
			'pagination'       => 0,
		);

		parent::__construct(
			$this->widget_slug,
			esc_html__('Classified Listing Listings', 'classified-listing'),
			[
				'classname'   => 'rtcl ' . $this->widget_slug . '-class',
				'description' => esc_html__('Displays classified Listings.', 'classified-listing')
			]
		);

		add_action('wp_enqueue_scripts', [$this, 'enqueue_styles_scripts'], 11);

	}

	public function widget($args, $instance) {

		// Merge incoming $instance array with $defaults
		if (is_array($instance)) {
			$instance = array_merge($this->defaults, $instance);
		} else {
			$instance = $this->defaults;
		}

		// WP Query
		global $post;

		$params = array(
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => !empty($instance['limit']) ? (int)$instance['limit'] : -1
		);

		$tax_queries = array();
		$meta_queries = array();

		$location = (int)$instance['location'];

		if ($instance['related_listings']) {

			$term_slug = get_query_var('rtcl_location');

			if ('' != $term_slug) {
				$term = get_term_by('slug', sanitize_text_field($term_slug), rtcl()->location);
				$location = $term->term_id;
			}

		}

		if ($location > 0) {

			$tax_queries[] = array(
				'taxonomy'         => rtcl()->location,
				'field'            => 'term_id',
				'terms'            => $location,
				'include_children' => isset($this->general_settings['include_results_from']) && in_array('child_locations',
						$this->general_settings['include_results_from']),
			);

		}


		$category = (int)$instance['category'];

		if ($instance['related_listings']) {

			if (is_singular(rtcl()->post_type)) {

				$category = wp_get_object_terms($post->ID, rtcl()->category);
				$category = !empty($category) ? $category[0]->term_id : 0;

				$params['post__not_in'] = array($post->ID);

			} else {
				$term_slug = get_query_var('rtcl_category');
				if ('' != $term_slug && $term = get_term_by('slug', sanitize_text_field($term_slug), rtcl()->category)) {
					$category = $term->term_id;
				}

			}

		}

		if ($category > 0) {

			$tax_queries[] = array(
				'taxonomy'         => rtcl()->category,
				'field'            => 'term_id',
				'terms'            => $category,
				'include_children' => isset($this->general_settings['include_results_from']) && in_array('child_categories',
						$this->general_settings['include_results_from']),
			);

		}

		switch ($instance['type']) {
			case "featured_only":
				$meta_queries[] = array(
					'key'     => 'featured',
					'value'   => 1,
					'compare' => '='
				);
				break;

			case "top_only":
				$meta_queries[] = array(
					'key'     => '_top',
					'value'   => 1,
					'compare' => '='
				);
				break;

			case "feature_top":
				$meta_queries[] = array(
					'key'     => 'featured',
					'value'   => 1,
					'compare' => '='
				);
				$meta_queries[] = array(
					'key'     => '_top',
					'value'   => 1,
					'compare' => '='
				);
				break;

			default:
				break;

		}

		$count_tax_queries = count($tax_queries);
		if ($count_tax_queries) {
			$params['tax_query'] = ($count_tax_queries > 1) ? array_merge(['relation' => 'AND'],
				$tax_queries) : [$tax_queries];
		}

		$count_meta_queries = count($meta_queries);
		if ($count_meta_queries) {
			$params['meta_query'] = ($count_meta_queries > 1) ? array_merge(['relation' => 'AND'],
				$meta_queries) : [$meta_queries];
		}

		$orderby = sanitize_text_field($instance['orderby']);
		$order = sanitize_text_field($instance['order']);

		switch ($orderby) {
			case 'price' :
				$params['meta_key'] = $orderby;
				$params['orderby'] = 'meta_value_num';
				$params['order'] = $order;
				break;
			case 'views' :
				$params['meta_key'] = '_views';
				$params['orderby'] = 'meta_value_num';
				$params['order'] = $order;
				break;
			case 'rand' :
				$params['orderby'] = $orderby;
				break;
			default :
				$params['orderby'] = $orderby;
				$params['order'] = $order;
		}

		$rtcl_query = new \WP_Query(apply_filters('rtcl_widget_listings_query_params', $params, $instance, $args, $this));
		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}
		$instance['slider_options'] = [];

		$class[] = 'rtcl-grid-view';
		$class[] = 'columns-' . absint($instance['columns']);
		if ($instance['view'] == 'slider') {
			$class[] = 'rtcl-carousel-slider';
			$instance['slider_options'] = apply_filters('rtcl_widget_listings_slider_options', [
				"rtl"          => is_rtl(),
				"spaceBetween" => 10,
				"breakpoints"  => [
					0   => [
						"slidesPerView" => absint($instance['mobile_items'])
					],
					576 => [
						"slidesPerView" => absint($instance['tab_items'])
					],
					768 => [
						"slidesPerView" => absint($instance['columns'])
					]
				]
			]);
		}
		if ($instance['view'] == 'grid') {
			$class[] = 'tab-columns-' . absint($instance['tab_items']);
			$class[] = 'mobile-columns-' . absint($instance['mobile_items']);
		}

		$instance['wrapper_classes'] = implode(' ', $class);
		// Process Output
		if ($rtcl_query->have_posts()) {

			$data = [
				'template'              => 'widgets/listings',
				'default_template_path' => null,
				'rtcl_query'            => $rtcl_query,
				'instance'              => $instance
			];
			$data = apply_filters('rtcl_widget_listings_data', $data, $args, $instance, $this);

			Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		}
		echo $args['after_widget'];

	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array $new_instance The new instance of values to be generated via the update.
	 * @param array $old_instance The previous instance of values before the update.
	 *
	 * @return array
	 * @since     1.0.0
	 * @access    public
	 */
	public function update($new_instance, $old_instance) {

		$instance = $old_instance;

		$instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
		$instance['location'] = !empty($new_instance['location']) ? (int)$new_instance['location'] : 0;
		$instance['category'] = !empty($new_instance['category']) ? (int)$new_instance['category'] : 0;
		$instance['related_listings'] = !empty($new_instance['related_listings']) ? 1 : 0;
		$instance['type'] = !empty($new_instance['type']) ? sanitize_text_field($new_instance['type']) : 'all';
		$instance['limit'] = !empty($new_instance['limit']) ? (int)$new_instance['limit'] : 8;
		$instance['orderby'] = !empty($new_instance['orderby']) ? sanitize_text_field($new_instance['orderby']) : 'title';
		$instance['order'] = !empty($new_instance['order']) ? sanitize_text_field($new_instance['order']) : 'asc';
		$instance['view'] = !empty($new_instance['view']) ? sanitize_text_field($new_instance['view']) : 'grid';
		$instance['columns'] = !empty($new_instance['columns']) ? (int)$new_instance['columns'] : 4;
		$instance['tab_items'] = !empty($new_instance['tab_items']) ? (int)$new_instance['tab_items'] : 3;
		$instance['mobile_items'] = !empty($new_instance['mobile_items']) ? (int)$new_instance['mobile_items'] : 1;
		$instance['show_image'] = !empty($new_instance['show_image']) ? 1 : 0;
		$instance['image_position'] = !empty($new_instance['image_position']) ? sanitize_text_field($new_instance['image_position']) : 'left';
		$instance['show_category'] = !empty($new_instance['show_category']) ? 1 : 0;
		$instance['show_location'] = !empty($new_instance['show_location']) ? 1 : 0;
		$instance['show_labels'] = !empty($new_instance['show_labels']) ? 1 : 0;
		$instance['show_price'] = !empty($new_instance['show_price']) ? 1 : 0;
		$instance['show_date'] = !empty($new_instance['show_date']) ? 1 : 0;
		$instance['show_user'] = !empty($new_instance['show_user']) ? 1 : 0;
		$instance['show_views'] = !empty($new_instance['show_views']) ? 1 : 0;

		return apply_filters('rtcl_widget_listings_update_values', $instance, $new_instance, $old_instance, $this);
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array $instance The array of keys and values for the widget.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function form($instance) {

		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array)$instance,
			apply_filters('rtcl_widget_listings_default_values', $this->defaults, $instance, $this)
		);
		$fields = Options::widget_listings_fields();
		$widgetFields = new WidgetFields($fields, $instance, $this);
		echo sprintf('<div class="rtcl-widget-listings-view-%s">', $instance['view'] ?: '');
		$widgetFields->render();
		echo '</div>';

	}


	public function enqueue_styles_scripts() {
		// 2nd one is for Elementor page has this widget
		if (is_active_widget(false, false, $this->id_base, true) || Functions::is_active_elementor_widget($this->id_base)) {
			wp_enqueue_script('swiper');
			wp_enqueue_script('rtcl-public');
			wp_enqueue_style('rtcl-public');
		}

	}
}
