<?php
/**
 * Listing Terms and conditions
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.2.17
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( apply_filters( 'rtcl_show_listing_terms_conditions', true ) && Functions::get_option_item( 'rtcl_account_settings', 'enable_listing_terms_conditions', '', 'checkbox' ) ) {
	do_action( 'rtcl_listing_before_terms_and_conditions' );
	?>

    <div class="rtcl-listing-terms-conditions rtcl-post-section">
		<?php do_action( 'rtcl_listing_terms_and_conditions' ); ?>
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox"
                       class="form-check-input"
                       name="rtcl_agree"
                       id="rtcl-terms-conditions"
                       required
					<?php checked( 1, $post_id ? $agreed : apply_filters( 'rtcl_listing_terms_is_checked_default', false ) ); // WPCS: input var ok, csrf ok. ?>
                >
                <label class="form-check-label" for="rtcl-terms-conditions">
					<?php Functions::terms_and_conditions_checkbox_text(); ?>
                </label>
                <div class="with-errors help-block"
                     data-error="<?php esc_attr_e( "This field is required", 'classified-listing' ) ?>"></div>
            </div>
        </div>
    </div>
	<?php
	do_action( 'rtcl_listing_after_terms_and_conditions' );
}