<?php

namespace Rtcl\Gateways\Paypal\lib;

/**
 * Here for backwards compatibility.
 * @since 1.0.0
 */
class PaypalRefund extends PayPalApiHandler {
	public static function get_request( $order, $amount = null, $reason = '' ) {
		return self::get_refund_request( $order, $amount, $reason );
	}

	public static function refund_order( $order, $amount = null, $reason = '', $sandbox = false ) {
		if ( $sandbox ) {
			self::$sandbox = $sandbox;
		}
		$result = self::refund_transaction( $order, $amount, $reason );
		if ( is_wp_error( $result ) ) {
			return $result;
		} else {
			return (array) $result;
		}
	}
}