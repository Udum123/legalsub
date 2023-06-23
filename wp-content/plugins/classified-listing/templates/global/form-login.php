<?php
/**
 * Login form
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 * @var bool $hidden
 * @var string $message
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (is_user_logged_in()) {
    return;
}

?>
<div class="rtcl rtcl-login-form-wrap" <?php if ($hidden){ ?>style="display:none;"<?php } ?>>

    <?php Functions::print_notices(); ?>
    <form class="rtcl-form rtcl-login-form" method="post">

        <?php do_action('rtcl_login_form_start'); ?>

        <?php if ($message) {
            echo wpautop(wptexturize($message));
        } // @codingStandardsIgnoreLine ?>

        <div class="form-group">
            <label for="rtcl-user-login" class="control-label"><?php esc_html_e('Username or E-mail',
                    'classified-listing'); ?></label>
            <input type="text" name="username" autocomplete="username"
                   value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                   id="rtcl-user-login" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="rtcl-user-pass" class="control-label">
                <?php esc_html_e('Password', 'classified-listing'); ?>
            </label>
            <input type="password" name="password" id="rtcl-user-pass" autocomplete="current-password"
                   class="form-control" required/>
        </div>

        <?php do_action('rtcl_login_form'); ?>

		<div class="form-group">
			<div id="rtcl-login-g-recaptcha" class="mb-2"></div>
			<div id="rtcl-login-g-recaptcha-message"></div>
		</div>
		
        <div class="form-group d-flex align-items-center">
            <button type="submit" name="rtcl-login" class="btn btn-primary" value="login">
                <?php esc_html_e('Login', 'classified-listing'); ?>
            </button>
            <div class="form-check">
                <input type="checkbox" name="rememberme" id="rtcl-rememberme" value="forever">
                <label class="form-check-label" for="rtcl-rememberme">
                    <?php esc_html_e('Remember Me', 'classified-listing'); ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <p class="rtcl-forgot-password">
                <?php if (Functions::is_registration_enabled()):
                    $register_link = Link::get_my_account_page_link();
                    if( Functions::is_registration_page_separate() ) {
	                    $register_link = Link::get_registration_page_link();
                    }
                    ?>
                    <a href="<?php echo esc_url($register_link); ?>"><?php esc_html_e('Register',
                            'classified-listing'); ?></a><span>|</span>
                <?php endif; ?>
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Forgot your password?',
                        'classified-listing'); ?></a>

            </p>
        </div>
        <?php do_action('rtcl_login_form_end'); ?>

    </form>
</div>
