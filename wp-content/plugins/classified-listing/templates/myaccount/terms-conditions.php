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
if (apply_filters('rtcl_show_registration_terms_conditions', true) && Functions::is_enable_terms_conditions('registration')) {
	do_action('rtcl_registration_before_terms_and_conditions');
	?>

	<div class="rtcl-registration-terms-conditions">
		<?php do_action('rtcl_registration_terms_and_conditions'); ?>
		<div class="form-group">
			<div class="form-check">
				<input type="checkbox"
					   class="form-check-input"
					   name="rtcl_terms_conditions"
					   id="rtcl-terms-conditions"
					   required
					<?php checked(1, apply_filters('rtcl_registration_terms_is_checked_default', false)); // WPCS: input var ok, csrf ok. ?>
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
	do_action('rtcl_registration_after_terms_and_conditions');
}