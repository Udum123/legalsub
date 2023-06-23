<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var Payment $payment
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;

?>
<div class="payment-info">
	<div class="row">
		<div class="col-md-6">
			<table class="table table-bordered">
				<tr>
					<td><?php esc_html_e('PAYMENT', 'classified-listing'); ?> #</td>
					<td><?php esc_html_e($payment->get_maybe_id()); ?></td>
				</tr>

				<tr>
					<td><?php esc_html_e('Total Amount', 'classified-listing'); ?></td>
					<td>
						<?php echo Functions::get_payment_formatted_price_html($payment->get_total()); ?>
					</td>
				</tr>

				<tr>
					<td><?php esc_html_e('Date', 'classified-listing'); ?></td>
					<td>
						<?php echo Functions::datetime('rtcl', $payment->get_date_paid()); ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="col-md-6">
			<table class="table table-bordered">
				<tr>
					<td><?php esc_html_e('Payment Method', 'classified-listing'); ?></td>
					<td>
						<?php echo $payment->get_payment_method_title() ?>
					</td>
				</tr>

				<tr>
					<td><?php esc_html_e('Payment Status', 'classified-listing'); ?></td>
					<td><?php echo Functions::get_status_i18n($payment->get_status()); ?></td>
				</tr>
				<?php if ($transaction_key = $payment->get_transaction_id()): ?>
					<tr>
						<td><?php esc_html_e('Transaction Key', 'classified-listing'); ?></td>
						<td><?php echo esc_html($transaction_key); ?></td>
					</tr>
				<?php else: ?>
					<tr>
						<td><?php esc_html_e('Order Key', 'classified-listing'); ?></td>
						<td><?php echo esc_html($payment->get_order_key()); ?></td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
	</div>
</div>
