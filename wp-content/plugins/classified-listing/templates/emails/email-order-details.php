<?php
/**
 * Order details table shown in emails.
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/email-order-details.php.
 *
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       2.3.0
 *
 * @var string $sent_to_admin
 * @var string $email
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;

defined('ABSPATH') || exit;

$text_align = is_rtl() ? 'right' : 'left';

/** @var Payment $order */
do_action('rtcl_email_before_order_table', $order, $sent_to_admin, $email); ?>

<h2>
    <?php
    if ($sent_to_admin) {
        $before = '<a class="link" href="' . esc_url($order->get_edit_order_url()) . '">';
        $after = '</a>';
    } else {
        $before = '';
        $after = '';
    }
    /* translators: %s: Order ID. */
    echo wp_kses_post($before . sprintf(__('[Order #%s]', 'classified-listing') . $after . ' (<time datetime="%s">%s</time>)', $order->get_maybe_order_number(), get_the_date('c', get_post($order->get_id())), get_the_date('l F j, Y', get_post($order->get_id()))));
    ?>
</h2>

<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6"
           style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
        <tr>
            <td><?php esc_html_e('PAYMENT', 'classified-listing'); ?> #</td>
            <td><?php echo esc_html($order->get_maybe_id()); ?></td>
        </tr>
        <?php do_action('rtcl_payment_receipt_details_before_total_amount', $order ); ?>
        <tr>
            <td><?php esc_html_e('Total Amount', 'classified-listing'); ?></td>
            <td>
                <?php
                if ($amount = $order->get_total()) {
                    echo Functions::get_payment_formatted_price_html($amount);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Date', 'classified-listing'); ?></td>
            <td>
                <?php echo Functions::datetime('rtcl', $order->get_date_paid()); ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Payment Method', 'classified-listing'); ?></td>
            <td>
                <?php echo $order->get_payment_method_title(); ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Payment Status', 'classified-listing'); ?></td>
            <td>
                <?php echo Functions::get_status_i18n($order->get_status()); ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Transaction Key', 'classified-listing'); ?></td>
            <td><?php echo esc_html($order->get_transaction_id()); ?></td>
        </tr>
    </table>
</div>
<?php if (apply_filters('rtcl_email_order_item_details_trigger', true) && !empty($item_details_fields)): ?>
    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6"
               style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <?php

            foreach ($item_details_fields as $item_key => $item) {
                $is_title = isset($item['type']) && $item['type'] == "title";
                $title_style = 'background-color:#F0F0F0;';
                if (isset($item['style'])) {
                    $tr_style = $item['style'];
                }
                $th = sprintf('<th %s>%s</th>',
                    $is_title ? ' colspan="2"' : null,
                    isset($item['label']) ? $item['label'] : null
                );
                $td = !$is_title ? sprintf('<td>%s</td>', isset($item['value']) ? $item['value'] : null) : null;
                printf('<tr%s>%s%s</tr>',
                    $is_title ? sprintf(' style="%s"', $title_style) : null,
                    $th, $td);
            }
            ?>
        </table>
    </div>
<?php endif; ?>

<?php do_action('rtcl_email_after_order_table', $order, $sent_to_admin, $email); ?>
