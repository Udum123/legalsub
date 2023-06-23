<?php

namespace Rtcl\Models;

class WidgetFields
{
    private $widget;
    private $fields;
    private $instance;

    /**
     * WidgetFields constructor.
     *
     * @param $fields
     * @param $instance
     * @param $widget \WP_Widget
     */
    public function __construct($fields, $instance, $widget) {
        $this->widget = $widget;
        $this->fields = $fields;
        $this->instance = $instance;
    }

    /**
     * @param bool $echo
     *
     * @return string|void
     */
    public function render($echo = true) {
        if (empty($this->fields) || !is_array($this->fields)) {
            return;
        }
        $html = '';
        foreach ($this->fields as $field_id => $field) {
            $type = $this->get_field_type($field);
            if (method_exists($this, 'generate_' . $type . '_html')) {
                $html .= $this->{'generate_' . $type . '_html'}($field_id, $field);
            } else {
                $html .= $this->generate_text_html($field_id, $field);
            }
        }

        if (!$echo)
            return $html;

        echo $html;
    }


    /**
     * Get a fields type. Defaults to "text" if not set.
     *
     * @param array $field
     *
     * @return string
     */
    private function get_field_type($field) {
        return empty($field['type']) ? 'text' : $field['type'];
    }

    private function get_placeholder_data() {
        return array(
            'label'          => '',
            'class'          => '',
            'placeholder'    => '',
            'blank'          => true,
            'type'           => 'text',
            'wrap_class'     => '',
            'options'        => array(),
            'select_buttons' => false,
        );
    }

    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_text_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);

        ob_start();
        ?>
        <p class="<?php echo esc_attr($data['wrap_class']); ?>">
            <label for="<?php echo $this->widget->get_field_id($field_id); ?>"><?php echo esc_html($data['label']); ?></label>
            <input class="widefat" id="<?php echo $this->widget->get_field_id($field_id); ?>"
                   name="<?php echo $this->widget->get_field_name($field_id); ?>" type="text"
                   value="<?php echo esc_attr($this->instance[$field_id]); ?>">
        </p>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_section_title_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);
        ob_start();
        ?>
        <div class="widget-title rtcl-widget-section-title" style="background: #fafafa; border: 1px solid #e5e5e5;">
            <h4 style="text-transform: uppercase;"><?php echo esc_html($data['label']); ?></h4>
        </div>
        <?php

        return ob_get_clean();
    }


    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_radio_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);
        $options = $data['options'];
        $option_count = count($options);
        ob_start();
        ?>
        <p class="<?php echo esc_attr($data['wrap_class']); ?>">
            <label for="<?php echo $this->widget->get_field_id($field_id); ?>"> <?php echo esc_html($data['label']); ?> </label><br>
            <?php
            $i = 1;
            foreach ($options as $id => $option) {
                $option_id = $field_id . '_' . $id;
                ?>
                <label for="<?php echo $this->widget->get_field_id($option_id); ?>">
                    <input class="" id="<?php echo $this->widget->get_field_id($option_id); ?>"
                           name="<?php echo $this->widget->get_field_name($field_id); ?>" type="radio"
                           value="<?php echo esc_attr($id) ?>" <?php if ($this->instance[$field_id] === $id) {
                        echo 'checked="checked"';
                    } ?> />
                    <?php echo esc_html($option); ?>
                </label>
                <?php
                if ($option_count > $i)
                    echo '<br>';
                $i++;
            }
            ?>
        </p>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_location_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);
        ob_start();
        ?>
        <p class="<?php echo esc_attr($data['wrap_class']); ?>">
            <label for="<?php echo $this->widget->get_field_id($field_id); ?>"><?php echo esc_html($data['label']); ?></label>
            <?php
            wp_dropdown_categories(array(
                'show_option_none'  => '-- ' . __('Select a Location', 'classified-listing') . ' --',
                'taxonomy'          => rtcl()->location,
                'name'              => $this->widget->get_field_name($field_id),
                'class'             => 'widefat',
                'orderby'           => 'name',
                'selected'          => (int)$this->instance[$field_id],
                'option_none_value' => '',
                'hierarchical'      => true,
                'depth'             => 10,
                'show_count'        => false,
                'hide_empty'        => false,
            ));
            ?>
        </p>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_category_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);
        ob_start();
        ?>
        <p class="<?php echo esc_attr($data['wrap_class']); ?>">
            <label for="<?php echo $this->widget->get_field_id($field_id); ?>"><?php echo esc_html($data['label']); ?></label>
            <?php
            wp_dropdown_categories(array(
                'show_option_none'  => '-- ' . __('Select a Category', 'classified-listing') . ' --',
                'option_none_value' => '',
                'taxonomy'          => rtcl()->category,
                'name'              => $this->widget->get_field_name($field_id),
                'class'             => 'widefat',
                'orderby'           => 'name',
                'selected'          => (int)$this->instance[$field_id],
                'hierarchical'      => true,
                'depth'             => 10,
                'show_count'        => false,
                'hide_empty'        => false,
            ));
            ?>
        </p>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_select_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);
        $options = $data['options'];
        ob_start();
        ?>
        <p class="<?php echo esc_attr($data['wrap_class']); ?>">
            <label for="<?php echo $this->widget->get_field_id($field_id); ?>"><?php echo esc_html($data['label']); ?></label>
            <select class="widefat" id="<?php echo $this->widget->get_field_id($field_id); ?>"
                    name="<?php echo $this->widget->get_field_name($field_id); ?>">
                <?php
                foreach ($options as $id => $option) {
                    printf('<option value="%s"%s>%s</option>', $id, selected($id, $this->instance[$field_id]),
                        $option);
                }
                ?>
            </select>
        </p>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate Text Input HTML.
     *
     * @param string $field_id
     * @param mixed  $data
     *
     * @return string
     */
    public function generate_checkbox_html($field_id, $data) {
        $defaults = $this->get_placeholder_data();
        $data = wp_parse_args($data, $defaults);
        ob_start();
        ?>
        <p class="<?php echo esc_attr($data['wrap_class']); ?>">
            <label for="<?php echo $this->widget->get_field_id($field_id); ?>">
				<input <?php checked($this->instance[$field_id]); ?>
					id="<?php echo $this->widget->get_field_id($field_id); ?>"
					name="<?php echo $this->widget->get_field_name($field_id); ?>" type="checkbox"/> <?php echo esc_html($data['label']); ?></label>
        </p>
        <?php

        return ob_get_clean();
    }


}