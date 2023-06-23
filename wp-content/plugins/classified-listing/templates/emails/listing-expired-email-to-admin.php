<?php
/**
 * User email to renew listing
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/listing-renewal-email-to-owner.php
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var RtclEmail $email
 * @var Listing   $listing
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php esc_html_e( 'Hi Administrator,', 'classified-listing' ); ?></p>
    <p><?php printf( __( 'This notification was for the listing on the website <strong>%s</strong> "{listing_title}" and is expired.', 'classified-listing' ), Functions::get_blogname() ) ?></p>
    <p><?php printf( __( '<strong>Listing :</strong> <a href="%s">%s</a>', 'classified-listing' ), get_edit_post_link($listing->get_id()), $listing->get_the_title() ); ?></p>
    <p><?php printf( __( '<strong>Expired on:</strong> %s', 'classified-listing' ), $email->get_placeholders_item( '{expiration_date}' ) ); ?></p>
    <p><?php esc_html_e( 'Please do not respond to this message. It is automatically generated and is for information purposes only.', 'classified-listing' ); ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
