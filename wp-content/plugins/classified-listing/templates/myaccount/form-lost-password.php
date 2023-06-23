<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version     1.0.0
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Functions::print_notices(); ?>

<form id="rtcl-lost-password-form" class="form-horizontal" method="post">

    <p><?php echo apply_filters( 'rtcl_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'classified-listing' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>
    <div class="form-group">
        <label for="rtcl-user-login"
               class="control-label"><?php _e( 'Username or E-mail', 'classified-listing' ); ?></label>
        <input type="text" name="user_login" id="rtcl-user-login" class="form-control" required/>
    </div>

	<?php do_action( 'rtcl_lost_password_form' ); ?>

    <div class="form-group">
        <input type="submit" name="rtcl-lost-password" class="btn btn-primary"
               value="<?php _e( 'Reset Password', 'classified-listing' ); ?>"/>
    </div>
	<?php wp_nonce_field( 'rtcl-lost-password', 'rtcl-lost-password-nonce' ); ?>

</form>
