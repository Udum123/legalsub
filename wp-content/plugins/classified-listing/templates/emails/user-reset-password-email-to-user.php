<?php
/**
 * Password Reset email
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/user-reset-password-email-to-user.php
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       2.3.0
 *
 * @var RtclEmail $email
 * @var WP_User $user
 * @var string $reset_key
 */


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'classified-listing' ), esc_html( $user->user_login ) ); ?></p>
    <p><?php printf( esc_html__( 'Someone has requested a new password for the following account on %s:', 'classified-listing' ), Functions::get_blogname() ); ?></p>
    <p><?php printf( esc_html__( 'Username: %s', 'classified-listing' ), esc_html( $user->user_login ) ); ?></p>
    <p><?php esc_html_e( 'If you didn\'t make this request, just ignore this email. If you\'d like to proceed:', 'classified-listing' ); ?></p>
    <p>
        <a class="link" href="<?php echo esc_url( add_query_arg( array(
			'key'   => $reset_key,
			'login' => $user->user_login
		), Link::get_my_account_page_link( 'lost-password' ) ) ); ?>"><?php // phpcs:ignore ?>
			<?php esc_html_e( 'Click here to reset your password', 'classified-listing' ); ?>
        </a>
    </p>
    <p><?php esc_html_e( 'Thanks for reading.', 'classified-listing' ); ?></p>

<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
