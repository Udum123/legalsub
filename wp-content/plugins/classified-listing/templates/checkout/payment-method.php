<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var PaymentGateway $gateway
 *
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\PaymentGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="list-group-item rtcl-no-margin-left rtcl-payment-method form-check">
	<label for="gateway-<?php echo esc_attr( $gateway->id ) ?>">
		<span class="rtcl-payment-method-input">
			<input type="radio" name="payment_method"
				   id="gateway-<?php echo esc_attr( $gateway->id ) ?>"
				   value="<?php echo esc_attr( $gateway->id ) ?>"
				   required> <?php echo esc_html( $gateway->get_title() ) ?>
		</span>
		<span class="rtcl-payment-method-icons"><?php Functions::print_html( $gateway->get_icon() ); ?></span>
	</label>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) {
		echo sprintf( '<div class="payment_box payment_method_%s" %s>%s</div>',
			$gateway->id,
			! $gateway->chosen ? 'style="display:none;"' : null,
			$gateway->payment_fields()
		);
	} ?>
</li>