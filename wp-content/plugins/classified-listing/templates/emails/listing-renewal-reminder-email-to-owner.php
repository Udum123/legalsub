<?php
/**
 * User email to renew listing
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/listing-renewal-reminder-email-to-owner.php
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 * @var RtclEmail $email
 */

use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'classified-listing' ), $listing->get_owner_name() ); ?></p>
    <p><?php printf( __( 'We\'ve noticed that you haven\'t renewed your listing <strong>%s</strong> at %s and just wanted to remind you that it expired on <strong>%s</strong>. Please remember you can still renew it here: %s', 'classified-listing' ), $listing->get_the_title(), $email->get_placeholders_item( '{site_link}' ), $email->get_placeholders_item( '{expiration_date}' ), $email->get_placeholders_item( '{renewal_link}' ) ) ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
