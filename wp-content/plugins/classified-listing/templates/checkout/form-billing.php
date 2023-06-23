<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.2.25
 *
 */

use Rtcl\Helpers\Functions;

defined( 'ABSPATH' ) || exit;
?>
<div id="rtcl-billing-fields">

    <h3 class="rtcl-checkout-heading"><?php esc_html_e( 'Billing details', 'classified-listing' ); ?></h3>

	<?php do_action( 'rtcl_before_checkout_billing_form' ); ?>

    <div class="rtcl-billing-fields__field-wrapper">
		<?php
		$checkout = rtcl()->checkout();
		$fields   = $checkout->get_checkout_fields( 'billing' );
		foreach ( $fields as $key => $field ) {
			Functions::form_field( $key, $field, $checkout->get_value( $key ) );
		}
		?>
    </div>

	<?php do_action( 'rtcl_after_checkout_billing_form' ); ?>
</div>