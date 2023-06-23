<?php
/**
 * new listing email notification to owner
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/new-post-notification-user.php
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var RtclEmail $email
 */

use Rtcl\Models\RtclEmail;
use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php esc_html_e( 'Hi Administrator,', 'classified-listing' ); ?></p>
    <p><?php printf( __( 'You have received a new listing on the website %s.', 'classified-listing' ), Functions::get_blogname() ) ?></p>
    <p><?php printf( __( '<strong>Listing :</strong> <a href="%s">%s</a>', 'classified-listing' ), $listing->get_the_permalink(), $listing->get_the_title() ); ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
