<?php
/**
 * Order completed email to Customer
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/order-created-email-to-user.php
 *
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var RtclEmail $email
 * @var Payment $order
 * @var bool $sent_to_admin
 */

use Rtcl\Models\Payment;
use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'classified-listing' ), esc_html( $order->get_customer_full_name() ) ); ?></p>
<?php /* translators: %s: Site title */ ?>
    <p><?php esc_html_e( 'We have finished processing your order.', 'classified-listing' ); ?></p>
    <p><?php printf( __( 'Order <strong>#%d</strong> is completed.', 'classified-listing' ), $order->get_maybe_id() ); ?></p>
<?php

/**
 * @hooked RtclEmails::order_details() Output the email order details
 */
do_action( 'rtcl_email_order_details', $order, $sent_to_admin, $email );

/**
 * @hooked RtclEmails::order_customer_details() Output the email order customer details
 */

do_action( 'rtcl_email_order_customer_details', $order, $sent_to_admin, $email );

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
