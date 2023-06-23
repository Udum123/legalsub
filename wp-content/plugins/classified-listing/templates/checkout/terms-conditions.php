<?php
/**
 * Checkout Terms and conditions
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.2.17
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (apply_filters('rtcl_show_checkout_terms_conditions', true) && Functions::is_enable_terms_conditions('checkout')) {
    do_action('rtcl_checkout_before_terms_and_conditions');
    ?>

    <div class="rtcl-checkout-terms-conditions rtcl-post-section">
        <?php do_action('rtcl_checkout_terms_and_conditions'); ?>
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox"
                       class="form-check-input"
                       name="rtcl_agree"
                       id="rtcl-terms-conditions"
                       required
                    <?php checked(1, apply_filters('rtcl_checkout_terms_is_checked_default', false)); // WPCS: input var ok, csrf ok. ?>
                >
                <label class="form-check-label" for="rtcl-terms-conditions">
                    <?php Functions::terms_and_conditions_checkbox_text(); ?>
                </label>
                <div class="with-errors help-block"
                     data-error="<?php esc_attr_e("This field is required", 'classified-listing') ?>"></div>
            </div>
        </div>
    </div>
    <?php
    do_action('rtcl_checkout_after_terms_and_conditions');
}