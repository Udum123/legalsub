<?php

namespace Rtcl\Controllers\Admin\Meta;


use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options;

class OrderMetaColumn
{

    public static function init() {
        add_action('manage_edit-' . rtcl()->post_type_payment . '_columns', [__CLASS__, 'order_get_columns']);
        add_action('manage_' . rtcl()->post_type_payment . '_posts_custom_column', [__CLASS__, 'order_custom_column_content'], 10, 2);
        add_action('manage_edit-' . rtcl()->post_type_payment . '_sortable_columns', [__CLASS__, 'get_sortable_columns']);
        add_action('restrict_manage_posts', [__CLASS__, 'restrict_manage_posts']);
//		add_action( 'before_delete_post', [ __CLASS__, 'before_delete_post' ] );
        add_action('post_row_actions', [__CLASS__, 'remove_row_actions'], 10, 2);
        add_action('rtcl_payment_item_details', [__CLASS__, 'rtcl_payment_regular_item_details'], 10, 2);
        add_action('rtcl_payment_promotions_content', [__CLASS__, 'rtcl_payment_regular_promotions_content'], 10, 2);
    }

    /**
     * @param int     $payment_id
     * @param Payment $payment
     */
    public static function rtcl_payment_regular_item_details($payment_id, $payment) {
        if ($payment && !empty($payment->pricing) && ("regular" === $payment->pricing->getType() || !$payment->pricing->getType())) {
            $listing = rtcl()->factory->get_listing($payment->get_listing_id());
            ?>
            <div class="item-wrap">
                <div class="item-thumbnail"><?php $listing ? $listing->the_thumbnail('thumbnail') : ''; ?></div>
                <div class="item-title">
                    <a href="<?php echo get_the_permalink($payment->get_listing_id()); ?>"><?php echo get_the_title($payment->get_listing_id()); ?></a>
                </div>
            </div>
            <?php
        }
    }

    /**
     * @param int     $payment_id
     * @param Payment $payment
     */
    public static function rtcl_payment_regular_promotions_content($payment_id, $payment) {
        if ($payment && !empty($payment->pricing) && ("regular" === $payment->pricing->getType() || !$payment->pricing->getType())) {
            $promotions = Options::get_listing_promotions();
            echo '<div class="regular-promotions rtcl-pricing-promotions">';
            foreach ($promotions as $promotion_key => $promotion_label) {
                $value = absint(get_post_meta($payment->pricing->getId(), $promotion_key, true));
                echo sprintf('<div class="item"><span class="item-label">%s:</span><span class="rtcl-tick-cross">%s</span></div>',
                    $promotion_label,
                    $value === 1 ? '&#x2713;' : '&#x2717;'
                );
            }
            echo "</div>";
        }
    }

    public static function order_get_columns() {
        return array(
            'cb'             => '<input type="checkbox" />', // Render a checkbox instead of text
            'ID'             => __('Order ID', 'classified-listing'),
            'type'           => __('Type', 'classified-listing'),
            'total'          => __('Total', 'classified-listing'),
            'transaction_id' => __('Transaction ID', 'classified-listing'),
            'date'           => __('Date', 'classified-listing'),
            'status'         => __('Status', 'classified-listing')
        );
    }

    public static function order_custom_column_content($column, $post_id) {

        global $post;

        $order = rtcl()->factory->get_order($post_id);
        switch ($column) {
            case 'ID' :
                if ($order->get_customer_id() && $user = get_user_by('id', $order->get_customer_id())) {
                    $username = '<a href="user-edit.php?user_id=' . absint($order->get_customer_id()) . '">';
                    $username .= esc_html(ucwords($user->display_name));
                    $username .= '</a>';
                    $userEmail = sprintf('<small class="meta email"><a href="%s">%s</a></small>',
                        esc_url('mailto:' . $user->user_email),
                        esc_html($user->user_email)
                    );
                } else {
                    $userEmail = '';
                    $username = __('Guest', 'classified-listing');
                }
                $wc_order_id = $order->get_wc_id();
                /* translators: 1: order and number (i.e. Order #13) 2: user name */
                printf('<a href="%s">#%d%s</a> by %s %s',
                    get_edit_post_link($post_id),
                    $post_id,
                    $wc_order_id ? " (WC #{$wc_order_id})" : '',
                    $username,
                    $userEmail
                );
                break;
            case 'type' :
                $types = Options::get_pricing_types();
                if ($order->pricing) {
                    $type = $order->pricing->getType();
                    echo $types[$type];
                }
                break;
            case 'total' :
                $title = $order->get_payment_method_title();
                $main_amount_html = Functions::get_payment_formatted_price_html($order->get_total()) ;
                $main_amount = apply_filters( 'rtcl_payment_table_total_amount', $main_amount_html, $order );
                printf("%s<small class='meta'>%s</small>", $main_amount ,
                    $order->get_total() === 0 ? $title : sprintf(__('Pay via <strong>%s</strong>', 'classified-listing'), $title)
                );
                break;
            case 'transaction_id' :
                echo $order->get_transaction_id();
                break;
            case 'date' :
                $date = strtotime($post->post_date);
                $value = date_i18n(get_option('date_format'), $date);

                echo $value;
                break;
            case 'status' :
                echo Functions::get_status_i18n($order->get_status());
                break;
        }

    }

    public static function get_sortable_columns() {

        return array(
            'ID'    => 'ID',
            'total' => 'amount',
            'date'  => 'date'
        );

    }

    public static function parse_query($query) {

        global $pagenow, $post_type;
		
    }

    public static function restrict_manage_posts() {

        global $typenow, $wp_query;

        if (rtcl()->post_type_payment == $typenow) {

            // Restrict by payment status
            $statuses = Options::get_payment_status_list();
            $current_status = isset($_GET['post_status']) ? $_GET['post_status'] : '';
            echo '<select name="post_status">';
            echo '<option value="all">' . __("All payments", 'classified-listing') . '</option>';
            foreach ($statuses as $value => $title) {
                printf('<option value="%s"%s>%s</option>', $value,
                    ($value == $current_status ? ' selected="selected"' : ''), $title);
            }
            echo '</select>';

        }

    }

    public static function remove_row_actions($actions, $post) {

        global $current_screen;

        if (is_object($current_screen) && $current_screen->post_type === rtcl()->post_type_payment) {
            unset($actions['view']);
            unset($actions['inline hide-if-no-js']);

            return $actions;
        }

        return $actions;

    }

}