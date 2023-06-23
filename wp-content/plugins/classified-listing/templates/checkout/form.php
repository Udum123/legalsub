<?php
/**
 *
 * @var string $type
 * @var string $value
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Functions::print_notices();

?>
<div class="rtcl-checkout-form-wrap">

	<?php do_action( 'rtcl_before_checkout_form', $type, $value ); ?>

	<form id="<?php echo esc_attr( apply_filters( 'rtcl_checkout_form_id', 'rtcl-checkout-form' ) ) ?>"
		  class="form-vertical" method="post">

		<?php
		do_action( 'rtcl_checkout_form_start', $type, $value );

		/**
		 * @deprecated since 1.4.1
		 */
		do_action( 'rtcl_submission_form', $type, $value );

		?>
		<div id="rtcl-checkout-fields-wrap">
			<?php do_action( 'rtcl_checkout_form', $type, $value ); ?>
		</div>
		<?php


		do_action( 'rtcl_checkout_form_submit_button', $type, $value );

		do_action( 'rtcl_checkout_form_end', $type, $value );
		?>

	</form>

	<?php do_action( 'rtcl_after_checkout_form', $type, $value ); ?>

</div>