<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var Payment $order
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;

Functions::print_notices();
?>

<div class="rtcl-payment-receipt">
	<?php
	do_action( 'rtcl_payment_receipt_top_' . $order->get_payment_method(), $order->get_id(), $order );
	do_action( 'rtcl_payment_receipt', $order->get_id(), $order );
	do_action( 'rtcl_payment_receipt_bottom_' . $order->get_payment_method(), $order->get_id(), $order );
	?>
</div>
