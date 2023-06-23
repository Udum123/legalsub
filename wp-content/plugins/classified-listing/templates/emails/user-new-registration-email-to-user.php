<?php
/**
 * New Registration email to user
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/user-new-registration-email-to-user.php
 *
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       2.3.0
 *
 * @var RtclEmail $email
 * @var WP_User   $user
 * @var array     $data
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action('rtcl_email_header', $email); ?>
<?php /* translators: %s: Customer billing full name */ ?>
	<p><?php printf(__("Hi %s", "classified-listing"), $user->user_login); ?></p>
	<p><?php printf(__("You are successfully registered to this site %s", 'classified-listing'), Functions::get_blogname()) ?></p>
	<p><?php printf(__("Username: %s", 'classified-listing'), $user->user_login) ?></p>
<?php if (!empty($data['password_generated'])) { ?>
	<p><?php printf(__("Password: %s", 'classified-listing'), $data['user_pass']) ?></p>
<?php } ?>
	<br>
	<p><?php esc_html_e('Thanks for reading.', 'classified-listing'); ?></p>
<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action('rtcl_email_footer', $email);

