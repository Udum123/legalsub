<?php
/**
 * new listing email notification to owner
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/new-post-notification-user.php
 *
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var RtclEmail $email
 * @var array     $data
 * @var Listing   $listing
 */

use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;
use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action('rtcl_email_header', $email); ?>
    <p><?php esc_html_e('Hi Administrator,', 'classified-listing'); ?></p>
    <p><?php printf(__('A contact request is received at your <a href="%s">%s.</a>', 'classified-listing'), get_site_url(), Functions::get_blogname()) ?></p>
    <p><?php printf(__('<strong>Name :</strong> %s', 'classified-listing'), $data['name']); ?></p>
    <p><?php printf(__('<strong>Email :</strong> %s', 'classified-listing'), $data['email']); ?></p>
<?php if (!empty($data['phone'])): ?>
    <p><?php printf(__('<strong>Phone :</strong> %s', 'classified-listing'), $data['phone']); ?></p>
<?php endif; ?>
    <p><?php printf(__('<strong>Message :</strong> %s', 'classified-listing'), $data['message']); ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);
