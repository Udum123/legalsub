<?php

namespace Rtcl\Widgets;


use Rtcl\Helpers\Functions;
use WP_Widget;

class Categories extends WP_Widget
{

    protected $widget_slug;

    public function __construct() {

        $this->widget_slug = 'rtcl-widget-categories';

        parent::__construct(
            $this->widget_slug,
            esc_html__('Classified Listing Categories', 'classified-listing'),
            array(
                'classname'   => 'rtcl ' . $this->widget_slug . '-class',
                'description' => esc_html__('A list of classified listing Categories.', 'classified-listing')
            )
        );
    }

    public function widget($args, $instance) {

        $query_args = array(
            'parent'         => !empty($instance['parent']) ? (int)$instance['parent'] : 0,
            'term_id'        => !empty($instance['parent']) ? (int)$instance['parent'] : 0,
            'hide_empty'     => !empty($instance['hide_empty']) ? 1 : 0,
            'show_image'     => !empty($instance['show_image']) ? 1 : 0,
            'show_icon'      => !empty($instance['show_icon']) ? 1 : 0,
            'show_count'     => !empty($instance['show_count']) ? 1 : 0,
            'orderby'        => !empty($instance['orderby']) ? $instance['orderby'] : "_rtcl_order",
            'order'          => !empty($instance['order']) ? $instance['order'] : "asc",
            'imm_child_only' => !empty($instance['imm_child_only']) ? 1 : 0,
            'active_term_id' => 0,
            'ancestors'      => [],
            'pad_counts'     => true
        );

        $term_slug = get_query_var('rtcl_category');

        if ('' != $term_slug && $term = get_term_by('slug', $term_slug, rtcl()->category)) {
            $query_args['active_term_id'] = $term->term_id;

            $query_args['ancestors'] = get_ancestors($query_args['active_term_id'], rtcl()->category);
            $query_args['ancestors'][] = $query_args['active_term_id'];
            $query_args['ancestors'] = array_unique($query_args['ancestors']);
        }

        $data = [];
        $data['categories'] = $this->list_categories($query_args);

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        Functions::get_template("widgets/categories", $data);

        echo $args['after_widget'];

    }

    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['parent'] = !empty($new_instance['parent']) ? (int)$new_instance['parent'] : 0;
        $instance['imm_child_only'] = !empty($new_instance['imm_child_only']) ? 1 : 0;
        $instance['orderby'] = !empty($new_instance['orderby']) ? esc_attr($new_instance['orderby']) : "name";
        $instance['order'] = !empty($new_instance['order']) ? esc_attr($new_instance['order']) : "asc";
        $instance['hide_empty'] = !empty($new_instance['hide_empty']) ? 1 : 0;
        $instance['show_count'] = !empty($new_instance['show_count']) ? 1 : 0;
        $instance['show_image'] = !empty($new_instance['show_image']) ? 1 : 0;
        $instance['show_icon'] = !empty($new_instance['show_icon']) ? 1 : 0;

        return $instance;

    }

    public function form($instance) {

        // Define the array of defaults
        $defaults = array(
            'title'          => esc_html__('Categories', 'classified-listing'),
            'parent'         => 0,
            'imm_child_only' => 0,
            'hide_empty'     => 1,
            'show_count'     => 1,
            'show_image'     => 1,
            'orderby'        => 'name',
            'order'          => 'asc',
            'show_icon'      => 0
        );

        // Parse incoming $instance into an array and merge it with $defaults
        $instance = wp_parse_args(
            (array)$instance,
	        apply_filters( 'rtcl_widget_category_default_values', $defaults, $instance, $this )
        );

        // Display the admin form
        include(RTCL_PATH . "views/widgets/categories.php");

    }

    public function list_categories($settings) {

        if ($settings['imm_child_only']) {

            if ($settings['term_id'] > $settings['parent'] && !in_array($settings['term_id'],
                    $settings['ancestors'])) {
                return '';
            }

        }

        $args = array(
            'orderby'      => $settings['orderby'],
            'order'        => $settings['order'],
            'hide_empty'   => $settings['hide_empty'],
            'parent'       => $settings['term_id'],
            'hierarchical' => !empty($settings['hide_empty'])
        );
        if ($settings['orderby'] === '_rtcl_order') {
            $args['meta_key'] = '_rtcl_order';
        }

        $terms = get_terms(rtcl()->category, $args);

        $html = '';

        if (count($terms) > 0) {

            $html .= '<ul class="rtcl-category-list">';

            foreach ($terms as $term) {
                $settings['term_id'] = $term->term_id;

                $count = 0;
                if (!empty($settings['hide_empty']) || !empty($settings['show_count'])) {
                    $count = Functions::get_listings_count_by_taxonomy($term->term_id, rtcl()->category,
                        $settings['pad_counts']);

                    if (!empty($settings['hide_empty']) && 0 == $count) {
                        continue;
                    }
                }
                $cat_img = $cat_icon = null;
                if (!empty($settings['show_image'])) {
                    $image_id = get_term_meta($term->term_id, '_rtcl_image', true);
                    if ($image_id) {
                        $image_attributes = wp_get_attachment_image_src((int)$image_id, 'medium');
                        $image = $image_attributes[0];
                        if ('' !== $image) {
                            $cat_img = sprintf('<img src="%s" class="rtcl-cat-img" />', $image);
                        }
                    }
                }
                if (!empty($settings['show_icon'])) {
                    $icon_id = get_term_meta($term->term_id, '_rtcl_icon', true);
                    if ($icon_id) {
                        $cat_icon = sprintf('<span class="rtcl-cat-icon rtcl-icon rtcl-icon-%s"></span>', $icon_id);
                    }
                }
                $html .= sprintf("<li%s><a href='%s'>%s%s%s</a>%s</li>",
                    $term->term_id === $settings['active_term_id'] ? " class='rtcl-active'" : null,
                    get_term_link($term),
                    $cat_img ? $cat_img : $cat_icon,
                    $term->name,
                    !empty($settings['show_count']) ? '<span>(' . $count . ')</span>' : null,
                    $this->list_categories($settings)
                );
            }

            $html .= '</ul>';

        }

        return $html;

    }

}