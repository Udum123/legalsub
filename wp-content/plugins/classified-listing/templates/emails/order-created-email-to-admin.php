<?php
/**
 * New order email to Admin
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/order-created-email-to-admin.php
 *
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       2.3.0
 *
 * @var RtclEmail $email
 * @var bool      $sent_to_admin
 * @var Payment   $order
 */


use Rtcl\Models\Payment;
use Rtcl\Models\RtclEmail;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action('rtcl_email_header', $email); ?>
<?php /* translators: %s: Customer billing full name */ ?>
    <p><?php printf(esc_html__('You’ve received the following order from %s:', 'classified-listing'), $order->get_customer_full_name()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php

/**
 * @hooked RtclEmails::order_details() Output the email order details
 */
do_action('rtcl_email_order_details', $order, $sent_to_admin, $email);

/**
 * @hooked RtclEmails::order_customer_details() Output the email order customer details
 */

do_action('rtcl_email_order_customer_details', $order, $sent_to_admin, $email);

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);
