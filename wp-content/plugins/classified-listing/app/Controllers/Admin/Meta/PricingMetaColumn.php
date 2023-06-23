<?php

namespace Rtcl\Controllers\Admin\Meta;


use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class PricingMetaColumn
{

    public static function init() {
        add_action('manage_edit-' . rtcl()->post_type_pricing . '_columns', [__CLASS__, 'pricing_columns']);
        add_action('manage_' . rtcl()->post_type_pricing . '_posts_custom_column', [__CLASS__, 'pricing_column_content'], 10, 2);
        add_action('rtcl_pricing_promotions_column_content', [__CLASS__, 'add_regular_pricing_promotions'], 10);
    }

    public static function add_regular_pricing_promotions($pricing_id) {
        $pricing_type = get_post_meta($pricing_id, 'pricing_type', true);
        if (!$pricing_type || "regular" === $pricing_type) {
            $promotions = Options::get_listing_promotions();
            echo '<div class="regular-promotions rtcl-pricing-promotions">';
            foreach ($promotions as $promotion_key => $promotion_label) {
                $value = absint(get_post_meta($pricing_id, $promotion_key, true));
                echo sprintf('<div class="item"><span class="item-label">%s:</span><span class="rtcl-tick-cross">%s</span></div>',
                    $promotion_label,
                    $value === 1 ? '&#x2713;' : '&#x2717;'
                );
            }
            echo "</div>";
        }
    }


    public static function pricing_columns($columns) {

        $new_columns = apply_filters('pricing_columns', [
            'price'           => __('Price', 'classified-listing'),
            'visible'         => __('Visible <small>Days</small>', 'classified-listing'),
            'rtcl_promotions' => __('Promotions', 'classified-listing'),
        ], $columns);
        $target_column = 'title';

        return Functions::array_insert_after($target_column, $columns, $new_columns);
    }

    public static function pricing_column_content($column, $post_id) {
        switch ($column) {
            case 'price' :
                $price = get_post_meta($post_id, 'price', true);
                echo $price ? Functions::get_payment_formatted_price_html($price) : __("Free", "classified-listing");
                break;
            case 'rtcl_promotions':
                do_action('rtcl_pricing_promotions_column_content', $post_id);
                break;
            case 'visible' :
                echo absint(get_post_meta($post_id, 'visible', true));
                break;

        }
    }

}
