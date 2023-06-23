<?php
/**
 * new listing email notification to owner
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/report-abuse-email-to-admin.php
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var array $data
 * @var RtclEmail $email
 * @var Listing $listing
 * @var bool $sent_to_admin
 */


use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php _e( 'Hi Administrator,', 'classified-listing' ); ?></p>
    <p><?php printf( __( 'This is an email abuse report for a listing at <a href="%s">%s</a>.', 'classified-listing' ), $listing->get_the_permalink(), $listing->get_the_title() ) ?></p>
    <p><?php printf( __( '<strong>Sender name:</strong> %s', 'classified-listing' ), $data['name'] ); ?></p>
    <p><?php printf( __( '<strong>Sender email:</strong> %s', 'classified-listing' ), $data['email'] ); ?></p>
    <p><?php printf( __( '<strong>Sender message:</strong> %s', 'classified-listing' ), $data['message'] ); ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
