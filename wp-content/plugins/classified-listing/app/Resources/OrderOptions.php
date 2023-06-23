<?php

namespace Rtcl\Resources;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

class OrderOptions
{

    public static function order_action($post) {
        ?>
        <div class="payment_actions major-publishing-actions">
            <?php
            if (current_user_can('delete_post', $post->ID)) {
                printf('<div id="delete-action"><a class="submitdelete deletion" href="%s">%s</a></div>',
                    esc_url(get_delete_post_link($post->ID)),
                    esc_html(__('Move to trash', 'classified-listing'))
                );
            }
            printf('<div id="publishing-action"><button type="submit" class="button save_order button-primary" name="save" value="%s">%s</button></div>',
                esc_attr__('Update', 'classified-listing'),
                esc_html__('Update', 'classified-listing')
            );
            ?>
            <div class="clear"></div>
        </div>
        <?php
    }

    public static function order_data($post) {
        $order = rtcl()->factory->get_order($post->ID);
        $order_type_object = get_post_type_object($post->post_type);
        wp_nonce_field(rtcl()->nonceText, rtcl()->nonceId);
        ?>
        <style type="text/css">
            #post-body-content, #titlediv {
                display: none;
            }
        </style>
        <div class="panel-wrap rtcl">
            <input name="post_title" type="hidden"
                   value="<?php echo empty($post->post_title) ? __('Order',
                       'classified-listing') : esc_attr($post->post_title); ?>"/>
            <input name="post_status" type="hidden" value="<?php echo esc_attr($post->post_status); ?>"/>
            <div id="payment_data" class="panel rtcl-order-data">
                <h2 class="rtcl-order-data__heading"><?php

                    /* translators: 1: order type 2: order number */
                    printf(
                        esc_html__('Order #%1$s details', 'classified-listing'),
                        esc_html($post->ID)
                    );

                    ?></h2>
                <p class="rtcl-order-data__meta payment_number"><?php

                    $meta_list = array();
                    $payment_method_string = sprintf(
                        __('Payment via %s', 'classified-listing'),
                        esc_html(!empty($order->gateway) ? $order->gateway->method_title : $order->get_payment_method())
                    );
                    $meta_list[] = $payment_method_string;

                    if ($order->get_date_paid()) {
                        /* translators: 1: date 2: time */
                        $meta_list[] = sprintf(
                            __('Paid on %1$s @ %2$s', 'classified-listing'),
                            Functions::datetime('rtcl-date', $order->get_date_paid()),
                            Functions::datetime('rtcl-time', $order->get_date_paid())
                        );
                    }

                    if ($ip_address = $order->get_customer_ip_address()) {
                        /* translators: %s: IP address */
                        $meta_list[] = sprintf(
                            __('Customer IP: %s', 'classified-listing'),
                            '<span class="rtcl-payment-customerIP">' . esc_html($ip_address) . '</span>'
                        );
                    }

                    echo wp_kses_post(implode('. ', $meta_list));

                    ?></p>
                <div class="payment_data_column_container">
                    <div class="payment_data_column">
                        <h3><?php esc_html_e('General', 'classified-listing'); ?></h3>
                        <p class="form-field form-field-wide rtcl-payment-status">
                            <label for="payment_status"><?php _e('Status:', 'classified-listing') ?><?php
                                if ($order->needs_payment()) {
                                    printf('<a href="%s">%s</a>',
                                        esc_url(Link::get_checkout_endpoint_url("submission", $order->get_listing_id())),
                                        __('Customer payment page &rarr;', 'classified-listing')
                                    );
                                }
                                ?></label>
                            <select id="payment_status" name="post_status">
                                <?php
                                $statuses = Options::get_payment_status_list();
                                foreach ($statuses as $status => $status_name) {
                                    echo '<option value="' . esc_attr($status) . '" ' . selected($status,
                                            $order->get_status(),
                                            false) . '>' . esc_html($status_name) . '</option>';
                                }
                                ?>
                            </select></p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }

    public static function order_items($post) {
        $order = rtcl()->factory->get_order($post->ID);
        if ($order) { ?>
            <div class="rtcl_payment_items_wrapper">
                <table cellpadding="0" cellspacing="0" class="rtcl_payment_items">
                    <thead>
                    <tr>
                        <th><?php _e("Item", 'classified-listing') ?></th>
                        <th><?php _e("Pricing Title", 'classified-listing') ?></th>
                        <th><?php _e("Visible <small>Days</small>", 'classified-listing') ?></th>
                        <th><?php _e("Promotions", 'classified-listing') ?></th>
                        <th><?php _e("Price", 'classified-listing') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="rtcl_payment_item_details">
                            <?php do_action('rtcl_payment_item_details', $order->get_id(), $order); ?>
                        </td>
                        <td class="option"><?php echo $order->pricing ? $order->pricing->getTitle() : ''; ?></td>
                        <td class="visible"><?php echo $order->pricing ? $order->pricing->getVisible() : ''; ?></td>
                        <td class="rtcl_promotions">
                            <?php do_action('rtcl_payment_promotions_content', $order->get_id(), $order); ?>
                        </td>
                        <td class="price"><?php echo $order->pricing ? Functions::get_payment_formatted_price_html($order->pricing->getPrice()) : ''; ?></td>
                    </tr>
                    <?php do_action( 'rtcl_after_payment_items', $order->get_id(), $order ); ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    }

    public static function order_notes($post) {
        global $post;

        $args = array(
            'order_id' => $post->ID,
        );

		$notes = Functions::get_order_notes($args);

        include(RTCL_PATH . "views/html-order-notes.php");
        ?>
        <div class="rtcl-add-note">
            <p>
                <label for="rtcl-add-payment-note"><?php esc_html_e('Add note', 'classified-listing'); ?><?php echo Functions::help_tip(__('Add a note for your reference, or add a customer note (the user will be notified).', 'classified-listing')); ?></label>
                <textarea type="text" name="rtcl_order_note" id="rtcl-add-payment-note" class="input-text" cols="20"
                          rows="5"></textarea>
            </p>
            <p>
                <label for="rtcl-order-note-type"
                       class="screen-reader-text"><?php esc_html_e('Note type', 'classified-listing'); ?></label>
                <select name="rtcl-order-note-type" id="rtcl-order-note-type">
                    <option value=""><?php esc_html_e('Private note', 'classified-listing'); ?></option>
                    <option value="customer"><?php esc_html_e('Note to customer', 'classified-listing'); ?></option>
                </select>
                <button type="button"
                        class="add-note button"><?php esc_html_e('Add', 'classified-listing'); ?></button>
            </p>
        </div>
        <?php

    }

}