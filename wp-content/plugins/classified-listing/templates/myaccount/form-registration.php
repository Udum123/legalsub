<?php
/**
 * Login Form
 *
 * @package classified-listing/Templates
 * @version 1.0.0
 * @since 1.5.20
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Functions::print_notices();
?>
<?php do_action( 'rtcl_before_user_registration_form' ); ?>
<div class="rtcl-user-registration-wrapper" id="rtcl-user-registration-wrapper">
	<?php if ( Functions::is_registration_enabled() && Functions::is_registration_page_separate() ): ?>
        <div class="rtcl-registration-form-wrap">

            <h2><?php esc_html_e( 'Registration', 'classified-listing' ); ?></h2>

            <form id="rtcl-register-form" class="form-horizontal" method="post">

				<?php do_action( 'rtcl_register_form_start' ); ?>

                <div class="form-group">
                    <label for="rtcl-reg-username" class="control-label">
						<?php esc_html_e( 'Username', 'classified-listing' ); ?>
                        <strong class="rtcl-required">*</strong>
                    </label>
                    <input type="text" name="username"
                           value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
                           autocomplete="username" id="rtcl-reg-username" class="form-control" required/>
                    <span class="help-block"><?php esc_html_e( 'Username cannot be changed.', 'classified-listing' ); ?></span>
                </div>

                <div class="form-group">
                    <label for="rtcl-reg-email" class="control-label">
						<?php esc_html_e( 'Email address', 'classified-listing' ); ?>
                        <strong class="rtcl-required">*</strong>
                    </label>
                    <input type="email" name="email"
                           value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>"
                           autocomplete="email" id="rtcl-reg-email" class="form-control" required/>
                </div>

                <div class="form-group">
                    <label for="rtcl-reg-password" class="control-label">
						<?php esc_html_e( 'Password', 'classified-listing' ); ?>
                        <strong class="rtcl-required">*</strong>
                    </label>
                    <input type="password" name="password" id="rtcl-reg-password" autocomplete="new-password"
                           class="form-control rtcl-password" required/>
                </div>

                <div class="form-group">
                    <label for="rtcl-reg-confirm-password" class="control-label">
			            <?php esc_html_e( 'Confirm Password', 'classified-listing' ); ?>
                        <strong class="rtcl-required">*</strong>
                    </label>
                    <div class="confirm-password-wrap">
                        <input type="password" name="pass2" id="rtcl-reg-confirm-password" class="form-control"
                               autocomplete="off"
                               data-rule-equalTo="#rtcl-reg-password"
                               data-msg-equalTo="<?php esc_attr_e( 'Password does not match.', 'classified-listing' ); ?>" required/>
                        <span class="rtcl-checkmark"></span>
                    </div>
                </div>

				<?php do_action( 'rtcl_register_form' ); ?>

                <div class="form-group">
                    <div id="rtcl-registration-g-recaptcha" class="mb-2"></div>
                    <div id="rtcl-registration-g-recaptcha-message"></div>
                    <input type="submit" name="rtcl-register" class="btn btn-primary"
                           value="<?php esc_attr_e( 'Register', 'classified-listing' ); ?>"/>
                    <p class="login-link"><?php esc_html_e( 'Already have an account? Please login', 'classified-listing' ); ?>
                        <a
                                href="<?php echo esc_url( Link::get_my_account_page_link() ); ?>"><?php esc_html_e( 'Here', 'classified-listing' ); ?></a>
                    </p>
                </div>
				<?php do_action( 'rtcl_register_form_end' ); ?>
            </form>
        </div>
	<?php endif; ?>
</div>
<?php do_action( 'rtcl_after_user_registration_form' ); ?>
