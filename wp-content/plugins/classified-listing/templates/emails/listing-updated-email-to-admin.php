<?php
/**
 * Listing Updated email notification to owner
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/listing-updated-email-to-admin.php
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var RtclEmail $email
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php esc_html_e( 'Hi Administrator,', 'classified-listing' ); ?></p>
    <p><?php printf( __( 'Listing <a href="%s">%s</a> is updated on the website %s.', 'classified-listing' ), $listing->get_the_permalink(),
			$listing->get_the_title(),
			Functions::get_blogname() ) ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
