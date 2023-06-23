<?php

namespace Rtcl\Widgets;


use Rtcl\Helpers\Functions;
use Rtcl\Models\WidgetFields;
use Rtcl\Resources\Options;

class Search extends \WP_Widget
{

	protected $style = [];

	protected $widget_slug;

	public function __construct() {

		$this->widget_slug = 'rtcl-widget-search';

		parent::__construct(
			$this->widget_slug,
			esc_html__('Classified Listing Search', 'classified-listing'),
			array(
				'classname'   => 'rtcl ' . $this->widget_slug,
				'description' => esc_html__('A Search feature', 'classified-listing')
			)
		);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	public function enqueue_scripts() {
		if (Functions::is_enable_map() && (is_active_widget(false, false, $this->id_base, true) || Functions::is_active_elementor_widget($this->id_base))) {
			wp_enqueue_script('rtcl-map');
		}
		do_action('rtcl_widget_search_enqueue_scripts', $this);
	}

	public function widget($args, $instance) {
		$data = [
			'id'                          => wp_rand(),
			'style'                       => !empty($instance['style']) && $instance['style'] === 'inline' ? 'inline' : 'vertical',
			'can_search_by_category'      => !empty($instance['search_by_category']) ? 1 : 0,
			'can_search_by_location'      => !empty($instance['search_by_location']) ? 1 : 0,
			'can_search_by_listing_types' => !empty($instance['search_by_listing_types']) ? 1 : 0,
			'can_search_by_price'         => !empty($instance['search_by_price']) ? 1 : 0,
			'radius_search'               => !empty($instance['radius_search']) ? 1 : 0,
			'selected_location'           => false,
			'selected_category'           => false,
			'default_template_path'       => ''
		];
		$data['template'] = "widgets/search/{$data['style']}";
		$data = apply_filters('rtcl_widget_search_values', $data, $args, $instance, $this);
		echo $args['before_widget'];

		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		Functions::get_template($data['template'], $data, '', $data['default_template_path']);

		echo $args['after_widget'];

	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
		$instance['style'] = !empty($new_instance['style']) ? strip_tags($new_instance['style']) : 'vertical';
		$instance['search_by_category'] = !empty($new_instance['search_by_category']) ? 1 : 0;
		$instance['search_by_location'] = !empty($new_instance['search_by_location']) ? 1 : 0;
		$instance['search_by_listing_types'] = !empty($new_instance['search_by_listing_types']) ? 1 : 0;
		$instance['search_by_price'] = !empty($new_instance['search_by_price']) ? 1 : 0;
		$instance['radius_search'] = !empty($new_instance['radius_search']) ? 1 : 0;

		return apply_filters('rtcl_widget_search_update_values', $instance, $new_instance, $old_instance, $this);
	}

	public function form($instance) {

		// Define the array of defaults
		$defaults = [
			'title'                   => esc_html__('Search Listings', 'classified-listing'),
			'style'                   => 'inline',
			'search_by_category'      => 1,
			'search_by_location'      => 1,
			'radius_search'           => 0,
			'search_by_listing_types' => 0,
			'search_by_price'         => 0
		];

		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array)$instance,
			apply_filters('rtcl_widget_search_default_values', $defaults, $instance, $this)
		);
		$fields = Options::widget_search_fields();
		$widgetFields = new WidgetFields($fields, $instance, $this);
		$widgetFields->render();
	}

}